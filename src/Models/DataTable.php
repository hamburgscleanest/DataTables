<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Exceptions\ColumnNotFoundException;
use hamburgscleanest\DataTables\Exceptions\MultipleComponentAssertionException;
use hamburgscleanest\DataTables\Exceptions\NotAnActiveRecordException;
use hamburgscleanest\DataTables\Exceptions\UnknownBaseModelException;
use hamburgscleanest\DataTables\Facades\TableRenderer;
use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use hamburgscleanest\DataTables\Models\Cache\Cache;
use hamburgscleanest\DataTables\Models\Cache\NoCache;
use hamburgscleanest\DataTables\Models\Column\Column;
use hamburgscleanest\DataTables\Models\Column\ColumnCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    /** @var ColumnCollection */
    private $_columns;

    /** @var Model */
    private $_model;

    /** @var array */
    private $_relations = [];

    /** @var string */
    private $_noDataHtml = '<div>no data</div>';

    /** @var Cache */
    private $_cache;

    /**
     * Set the base model whose data is displayed in the table.
     *
     * @param string $modelName
     * @param array $columns
     * @param Cache|null $cache
     * @return DataTable
     * @throws NotAnActiveRecordException
     */
    public function model(string $modelName, array $columns = [], Cache $cache = null) : DataTable
    {
        if (!\is_subclass_of($modelName, Model::class))
        {
            throw new NotAnActiveRecordException($modelName);
        }

        $this->_model = new $modelName;
        $this->_queryBuilder = $this->_model->newQuery();
        $this->_columns = new ColumnCollection($columns, $this->_model);
        $this->_cache = $cache ?? new NoCache();

        return $this;
    }

    /**
     * @param Cache $cache
     * @return DataTable
     */
    public function cache(Cache $cache) : DataTable
    {
        $this->_cache = $cache;

        return $this;
    }

    /**
     * @return ColumnCollection
     */
    public function getColumns() : ColumnCollection
    {
        return $this->_columns;
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
     * @return DataTable
     * @throws UnknownBaseModelException
     */
    public function columns(array $columns) : DataTable
    {
        if ($this->_columns === null)
        {
            throw new UnknownBaseModelException();
        }

        $this->_columns->push($columns);

        return $this;
    }

    /**
     * Add a component to the data table.
     * For example a "Paginator" or a "Sorter".
     *
     * @param DataComponent $component
     *
     * @param null|string $name
     * @return DataTable
     * @throws MultipleComponentAssertionException
     */
    public function addComponent(DataComponent $component, ? string $name = null) : DataTable
    {
        $componentName = $this->_getComponentName($component, $name);
        if ($this->componentExists($componentName))
        {
            throw new MultipleComponentAssertionException();
        }

        $this->_components[$componentName] = $component->init($this);

        return $this;
    }

    /**
     * @param DataComponent $component
     * @param null|string $name
     * @return string
     */
    private function _getComponentName(DataComponent $component, string $name = null) : string
    {
        if ($name !== null)
        {
            return \str_replace(' ', '', \mb_strtolower($name));
        }

        return $component->getName();
    }

    /**
     * Check whether a component exists for the given data table.
     * @param string $componentName
     * @return bool
     */
    public function componentExists(string $componentName) : bool
    {
        return \array_key_exists($componentName, $this->_components);
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
     * @throws ColumnNotFoundException
     */
    public function formatColumn(string $columnName, ColumnFormatter $columnFormatter) : DataTable
    {
        /** @var Column $column */
        $column = $this->_columns->first(function($column) use ($columnName) {
            /** @var Column $column */
            return $column->getName() === $columnName;
        });

        if ($column === null)
        {
            throw new ColumnNotFoundException($columnName);
        }

        $column->setFormatter($columnFormatter);

        return $this;
    }

    /**
     * Add classes to the table.
     *
     * @param string $classes
     *
     * @return DataTable
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
        $this->_relations += Relationship::createFromArray($this->_model, $relations);

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
     * @throws \Throwable
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
     * @throws UnknownBaseModelException
     */
    public function render() : string
    {
        if ($this->_queryBuilder === null)
        {
            throw new UnknownBaseModelException();
        }

        /** @var Collection $data */
        $data = $this->_cache->retrieve(function() {
            return $this->_getData();
        });

        if ($data->count() === 0)
        {
            return $this->_noDataHtml;
        }

        return TableRenderer::open($this->_classes) .
               TableRenderer::renderHeaders($this->_columns->getHeaders(), $this->_headerFormatters) .
               TableRenderer::renderBody($data, $this->_columns->toArray()) .
               TableRenderer::close();
    }

    /**
     * Get data which should be displayed in the table.
     *
     * @return Collection
     */
    private function _getData() : Collection
    {
        $this->_addRelations();

        /** @var DataComponent $component */
        foreach ($this->_components as $component)
        {
            $component->transformData();
        }

        return $this->_setSelection()->_queryBuilder->get();
    }

    private function _addRelations() : void
    {
        if (\count($this->_relations) === 0)
        {
            return;
        }

        /** @var Relationship $relation */
        foreach ($this->_relations as $relation)
        {
            $relation->addJoin($this->_queryBuilder);
        }

        $this->_queryBuilder->getQuery()->groupBy($this->_model->getTable() . '.' . $this->_model->getKeyName());
    }

    /**
     * @return DataTable
     */
    private function _setSelection() : DataTable
    {
        $query = $this->_queryBuilder->getQuery();

        $columns = $this->_columns->getUnmutatedColumns();
        if (!empty($columns))
        {
            $query->selectRaw(
                \implode(',',
                    \array_map(function($column) {
                        /** @var Column $column */
                        return $column->getIdentifier();
                    }, $columns)
                )
            );
        }

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (\array_key_exists($name, $this->_components))
        {
            return $this->_components[$name];
        }

        return $this->{$name};
    }
}