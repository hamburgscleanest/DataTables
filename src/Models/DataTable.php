<?php

namespace hamburgscleanest\DataTables\Models;

use Closure;
use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Class DataTable
 * @package hamburgscleanest\DataTables\Models
 */
class DataTable {

    /** @var Request */
    private $_request;

    /** @var Builder */
    private $_queryBuilder;

    /** @var array */
    private $_headerFormatters = [];

    /** @var array */
    private $_components = [];

    /** @var string */
    private $_classes;

    /** @var array */
    private $_columns = [];

    /** @var array */
    private $_relations = [];

    /**
     * DataTable constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Set the base model whose data is displayed in the table.
     *
     * @param string $modelName
     * @param array $columns
     * @return $this
     * @throws \RuntimeException
     */
    public function model(string $modelName, array $columns = []): DataTable
    {
        if (!\class_exists($modelName) || !\is_subclass_of($modelName, Model::class))
        {
            throw new RuntimeException('Class "' . $modelName . '" does not exist or is not an active record!');
        }

        $this->_queryBuilder = (new $modelName)->newQuery();
        $this->_columns = $this->_fetchColumns($columns);

        return $this;
    }

    /**
     * Returns an array of Column objects which may be bound to a formatter.
     *
     * @param array $columns
     * @return array
     */
    private function _fetchColumns(array $columns): array
    {
        $columnModels = [];
        foreach ($columns as $column => $formatter)
        {
            if (\is_int($column))
            {
                $column = $formatter;
                $formatter = null;
            }
            $columnModels[] = new Column($column, $formatter);
        }

        return $columnModels;
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->_queryBuilder;
    }

    /**
     * Displayed columns
     *
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns): DataTable
    {
        $this->_columns += $this->_fetchColumns($columns);

        return $this;
    }

    /**
     * Add a component to the data table.
     * For example a "Paginator" or a "Sorter".
     *
     * @param DataComponent $component
     *
     * @return $this
     */
    public function addComponent(DataComponent $component): DataTable
    {
        $component->init($this->_queryBuilder, $this->_request);
        $this->_components[] = $component;

        return $this;
    }

    /**
     * Add a formatter for the column headers.
     *
     * @param HeaderFormatter $headerFormatter
     * @return $this
     */
    public function formatHeaders(HeaderFormatter $headerFormatter): DataTable
    {
        $this->_headerFormatters[] = $headerFormatter;

        return $this;
    }

    /**
     * Add a formatter for a column.
     *
     * @param string $columnName
     * @param ColumnFormatter $columnFormatter
     * @return DataTable
     */
    public function formatColumn(string $columnName, ColumnFormatter $columnFormatter): DataTable
    {
        /** @var Column $column */
        $column = \array_first(
            $this->_columns,
            function ($index, $column) use ($columnName)
            {
                /** @var Column $column */
                return $column->getName() === $columnName;
            }
        );

        if ($column !== null)
        {
            $column->setFormatter($columnFormatter);
        }

        return $this;
    }

    /**
     * Add classes to the table.
     *
     * @param string $classes
     *
     * @return $this
     */
    public function classes(string $classes): DataTable
    {
        $this->_classes = $classes;

        return $this;
    }

    /**
     * Add a relation to the table.
     *
     * @param array $relations
     * @return $this
     */
    public function with(array $relations): DataTable
    {
        $this->_relations += $relations;

        return $this;
    }

    /**
     * Renders the table.
     *
     * @param null|Closure $noDataView
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render(Closure $noDataView = null): string
    {
        $data = $this->_getData();

        if ($data->count() === 0)
        {
            return $noDataView !== null ? $noDataView->call($this) : '<div>no data</div>';
        }

        $this->_initColumns();

        return $this->_open() . $this->_renderHeaders() . $this->_renderBody($data) . $this->_close();
    }

    /**
     * Get data which should be displayed in the table.
     *
     * @return Collection
     *
     * @throws \RuntimeException
     */
    private function _getData(): Collection
    {
        if ($this->_queryBuilder === null)
        {
            throw new RuntimeException('Unknown base model!');
        }

        $this->_addRelations();

        /** @var DataComponent $component */
        foreach ($this->_components as $component)
        {
            $component->transformData();
        }

        return $this->_queryBuilder->get();
    }

    private function _addRelations()
    {
        if (\count($this->_relations) > 0)
        {
            $this->_queryBuilder->with($this->_relations);
        }
    }

    private function _initColumns()
    {
        if (\count($this->_columns) === 0)
        {
            $this->_columns = $this->_fetchColumns(Schema::getColumnListing($this->_queryBuilder->getQuery()->from));
        }
    }

    /**
     * Starts the table.
     *
     * @return string
     */
    private function _open(): string
    {
        return '<table class="' . ($this->_classes ?? 'table') . '">';
    }

    /**
     * Renders the column headers.
     *
     * @return string
     */
    private function _renderHeaders(): string
    {
        $html = '<tr>';

        /** @var Header $header */
        foreach ($this->_fetchHeaders() as $header)
        {
            $html .= $header->formatArray($this->_headerFormatters, $this->_request)->print();
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * @return array
     */
    private function _fetchHeaders(): array
    {
        return array_map(
            function ($column)
            {
                /** @var Column $column */
                return new Header($column);
            },
            $this->_columns
        );
    }

    /**
     * Displays the table body.
     *
     * @param Collection $data
     *
     * @return string
     */
    private function _renderBody(Collection $data): string
    {
        $html = '';
        foreach ($data as $row)
        {
            $html .= $this->_renderRow($row);
        }

        return $html;
    }

    /**
     * Displays a single row.
     *
     * @param Model $rowModel
     *
     * @return string
     */
    private function _renderRow(Model $rowModel): string
    {
        $attributes = $rowModel->getAttributes() + $this->_getMutatedAttributes($rowModel, $this->_getColumnNames());

        $html = '<tr>';
        /** @var Column $column */
        foreach ($this->_columns as $column)
        {
            $html .= '<td>' .
                     ($column->getRelation() !== null ? $this->_getColumnValueFromRelation($rowModel, $column) : $column->format($attributes[$column->getName()] ?? ''))
                     . '</td>';
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
    private function _getMutatedAttributes(Model $model, array $columns = []): array
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
     * @return array
     */
    private function _getColumnNames(): array
    {
        return \array_map(function ($column)
        {
            /** @var Column $column */
            return $column->getName();
        },
            $this->_getColumnsWithoutRelations()
        );
    }

    /**
     * Get only the columns which are attributes from the base model.
     *
     * @return array
     */
    private function _getColumnsWithoutRelations(): array
    {
        return \array_filter(
            $this->_columns,
            function ($column)
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
    private function _getColumnValueFromRelation(Model $model, Column $column): string
    {
        $columnRelation = $column->getRelation();
        $relation = $model->getRelation($columnRelation->name);
        if ($relation === null)
        {
            return '';
        }

        if ($relation instanceof Model)
        {
            return $relation->{$column->getName()};
        }

        return $columnRelation->getValue($column->getName(), $relation);
    }

    /**
     * Closes the table.
     *
     * @return string
     */
    private function _close(): string
    {
        return '</table>';
    }
}