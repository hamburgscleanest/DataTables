<?php

namespace hamburgscleanest\DataTables\Models;

use Closure;
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

    /** @var Closure */
    private $_rowRenderer; // TODO: IColumnFormatter => DateColumnFormatter etc.

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
    public function model(string $modelName, array $columns = [])
    {
        if (!\class_exists($modelName))
        {
            throw new RuntimeException('Class "' . $modelName . '" does not exist!');
        }

        if (!\is_subclass_of($modelName, Model::class))
        {
            throw new RuntimeException('"' . $modelName . '" is not an active record!');
        }

        $this->_queryBuilder = (new $modelName)->newQuery();
        $this->_columns = $columns;

        return $this;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->_queryBuilder;
    }

    /**
     * Displayed columns
     *
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns)
    {
        $this->_columns += $columns;

        return $this;
    }

    /**
     * Manipulate each rendered row.
     *
     * @param Closure $customRowRenderer
     * @return $this
     */
    public function renderRow(Closure $customRowRenderer)
    {
        $this->_rowRenderer = $customRowRenderer;

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
    public function addComponent(DataComponent $component)
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
    public function formatHeaders(HeaderFormatter $headerFormatter)
    {
        $this->_headerFormatters[] = $headerFormatter;

        return $this;
    }

    /**
     * Get data which should be displayed in the table.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \RuntimeException
     */
    private function _getData()
    {
        if ($this->_queryBuilder === null)
        {
            throw new RuntimeException('Unknown base model!');
        }

        if (\count($this->_relations) > 0)
        {
            $this->_queryBuilder->with($this->_relations);
        }

        /** @var DataComponent $component */
        foreach ($this->_components as $component)
        {
            $component->transformData();
        }

        return $this->_queryBuilder->get();
    }

    /**
     * Add classes to the table.
     *
     * @param string $classes
     *
     * @return $this
     */
    public function classes(string $classes)
    {
        $this->_classes = $classes;

        return $this;
    }

    /**
     * Renders the column headers.
     *
     * @return string
     */
    private function _renderHeaders()
    {
        if (\count($this->_columns) === 0)
        {
            $this->_columns = Schema::getColumnListing($this->_queryBuilder->getQuery()->from);
        }

        $headers = array_map(
            function($name)
            {
                return new Header($name);
            },
            $this->_columns
        );

        $html = '<tr>';

        /** @var Header $header */
        foreach ($headers as $header)
        {
            $header->formatArray($this->_headerFormatters, $this->_request);

            $html .= '<th>' . $header->name . '</th>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Displays the table body.
     *
     * @param Collection $data
     *
     * @return string
     */
    private function _renderBody(Collection $data)
    {
        $html = '';
        foreach ($data as $row)
        {
            $html .= $this->_renderRow($row);
        }

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
     * Displays a single row.
     *
     * @param Model $rowModel
     *
     * @return string
     */
    private function _renderRow(Model $rowModel)
    {
        if ($this->_rowRenderer !== null)
        {
            $rowModel = $this->_rowRenderer->call($this, $rowModel);
        }

        $attributes = $rowModel->getAttributes() + $this->_getMutatedAttributes($rowModel, $this->_columns);

        $html = '<tr>';
        foreach ($this->_columns as $column)
        {
            $html .= '<td>' . ($attributes[$column] ?? '') . '</td>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Add a relation to the table.
     *
     * @param array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->_relations += $relations;

        return $this;
    }

    /**
     * Starts the table.
     *
     * @return string
     */
    private function _open()
    {
        return '<table class="' . ($this->_classes ?? 'table') . '">';
    }

    /**
     * Closes the table.
     *
     * @return string
     */
    private function _close()
    {
        return '</table>';
    }

    /**
     * Renders the table.
     *
     * @param null|Closure $noDataView
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render(Closure $noDataView = null)
    {
        $data = $this->_getData();

        if ($data->count() === 0)
        {
            return $noDataView !== null ? $noDataView->call($this) : '<div>no data</div>';
        }

        return $this->_open() . $this->_renderHeaders() . $this->_renderBody($data) . $this->_close();
    }
}