<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Facades\TableRenderer;
use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Class DataTable
 * @package hamburgscleanest\DataTables\Models
 */
class DataTable {

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

    /** @var string */
    private $_noDataHtml = '<div>no data</div>';

    /**
     * Set the base model whose data is displayed in the table.
     *
     * @param string $modelName
     * @param array $columns
     * @return $this
     * @throws \RuntimeException
     */
    public function model(string $modelName, array $columns = []) : DataTable
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
    private function _fetchColumns(array $columns) : array
    {
        $columnModels = [];
        foreach ($columns as $column => $formatter)
        {
            [$column, $formatter] = $this->_setColumnFormatter($column, $formatter);
            $columnModels[] = new Column($column, $formatter);
        }

        return $columnModels;
    }

    /**
     * @param $column
     * @param $formatter
     * @return array
     */
    private function _setColumnFormatter($column, $formatter) : array
    {
        if (\is_int($column))
        {
            $column = $formatter;
            $formatter = null;
        }

        return [$column, $formatter];
    }

    /**
     * @return Builder
     */
    public function query() : Builder
    {
        return $this->_queryBuilder;
    }

    /**
     * Displayed columns
     *
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns) : DataTable
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
    public function addComponent(DataComponent $component) : DataTable
    {
        $component->init($this->_queryBuilder, $this->_columns);
        $this->_components[] = $component;

        return $this;
    }

    /**
     * Add a formatter for the column headers.
     *
     * @param HeaderFormatter $headerFormatter
     * @return DataTable
     */
    public function formatHeaders(HeaderFormatter $headerFormatter) : DataTable
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
    public function formatColumn(string $columnName, ColumnFormatter $columnFormatter) : DataTable
    {
        /** @var Column $column */
        $column = \array_first(
            $this->_columns,
            function($index, $column) use ($columnName)
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
    public function classes(string $classes) : DataTable
    {
        $this->_classes = $classes;

        return $this;
    }

    /**
     * Add a relation to the table.
     *
     * @param array $relations
     * @return DataTable
     */
    public function with(array $relations) : DataTable
    {
        $this->_relations += $relations;

        return $this;
    }

    /**
     * Set the HTML which should be displayed when the dataset is empty.
     *
     * @param string $html
     * @return DataTable
     */
    public function noDataHtml(string $html) : DataTable
    {
        $this->_noDataHtml = $html;

        return $this;
    }

    /**
     * Set a view which should be displayed when the dataset is empty.
     *
     * @param string $viewName
     * @return DataTable
     */
    public function noDataView(string $viewName) : DataTable
    {
        $this->_noDataHtml = \view($viewName)->render();

        return $this;
    }

    /**
     * Renders the table.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render() : string
    {
        $data = $this->_getData();

        if ($data->count() === 0)
        {
            return $this->_noDataHtml;
        }

        $this->_initColumns();

        return TableRenderer::open($this->_classes) .
                TableRenderer::renderHeaders($this->_fetchHeaders(), $this->_headerFormatters) .
                TableRenderer::renderBody($data, $this->_columns) .
                TableRenderer::close();
    }

    /**
     * Get data which should be displayed in the table.
     *
     * @return Collection
     *
     * @throws \RuntimeException
     */
    private function _getData() : Collection
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

    private function _addRelations() : void
    {
        if (\count($this->_relations) > 0)
        {
            $this->_queryBuilder->with($this->_relations);
        }
    }

    private function _initColumns() : void
    {
        if (\count($this->_columns) === 0)
        {
            $this->_columns = $this->_fetchColumns(Schema::getColumnListing($this->_queryBuilder->getQuery()->from));
        }
    }

    /**
     * @return array
     */
    private function _fetchHeaders() : array
    {
        return \array_map(
            function($column)
            {
                /** @var Column $column */
                return new Header($column->getKey());
            },
            $this->_columns
        );
    }
}