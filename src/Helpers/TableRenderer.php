<?php

namespace hamburgscleanest\DataTables\Helpers;

use hamburgscleanest\DataTables\Models\Column;
use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class TableRenderer
 * @package hamburgscleanest\DataTables\Helpers
 */
class TableRenderer {

    /** @var Request */
    private $_request;

    /**
     * TableRenderer constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Starts the table.
     *
     * @param null|string $classes
     * @return string
     */
    public function open(?string $classes = null) : string
    {
        return '<table class="' . ($classes ?? 'table') . '">';
    }

    /**
     * Closes the table.
     *
     * @return string
     */
    public function close() : string
    {
        return '</table>';
    }

    /**
     * Renders the column headers.
     *
     * @param array $headers
     * @param array $formatters
     * @return string
     */
    public function renderHeaders(array $headers, array $formatters = []) : string
    {
        $html = '<tr>';

        /** @var Header $header */
        foreach ($headers as $header)
        {
            $html .= $header->formatArray($formatters, $this->_request)->print();
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Displays the table body.
     *
     * @param Collection $data
     *
     * @param array $columns
     * @return string
     */
    public function renderBody(Collection $data, array $columns = []) : string
    {
        $html = '';
        foreach ($data as $row)
        {
            $html .= $this->_renderRow($row, $columns);
        }

        return $html;
    }

    /**
     * Displays a single row.
     *
     * @param Model $rowModel
     *
     * @param array $columns
     * @return string
     */
    private function _renderRow(Model $rowModel, array $columns) : string
    {
        $attributes = $rowModel->getAttributes() + $this->_getMutatedAttributes($rowModel, $this->_getColumnNames($columns));

        $html = '<tr>';
        /** @var Column $column */
        foreach ($columns as $column)
        {
            $html .= '<td>' . $column->format($column->getRelation() !== null ? $this->_getColumnValueFromRelation($rowModel, $column) : ($attributes[$column->getName()] ?? '')) . '</td>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Get all the mutated attributes which are needed.
     *
     * @param Model $model
     * @param array $columns
     * @return array
     */
    private function _getMutatedAttributes(Model $model, array $columns = []) : array
    {
        $attributes = [];
        foreach (\array_intersect_key($model->getMutatedAttributes(), $columns) as $attribute)
        {
            $attributes[$attribute] = $model->{$attribute};
        }

        return $attributes;
    }

    /**
     * Get all column names.
     *
     * @param array $columns
     * @return array
     */
    private function _getColumnNames(array $columns) : array
    {
        return \array_map(function($column)
        {
            /** @var Column $column */
            return $column->getName();
        },
            $this->_getColumnsWithoutRelations($columns)
        );
    }

    /**
     * Get only the columns which are attributes from the base model.
     *
     * @param array $columns
     * @return array
     */
    private function _getColumnsWithoutRelations(array $columns) : array
    {
        return \array_filter(
            $columns,
            function($column)
            {
                /** @var Column $column */
                return $column->getRelation() === null;
            }
        );
    }

    /**
     * @param Model $model
     * @param Column $column
     * @return string
     */
    private function _getColumnValueFromRelation(Model $model, Column $column) : string
    {
        $columnRelation = $column->getRelation();
        $relation = $model->getRelation($columnRelation->name);

        if ($relation instanceof Model)
        {
            return $relation->{$column->getName()};
        }

        return $columnRelation->getValue($column->getName(), $relation);
    }
}