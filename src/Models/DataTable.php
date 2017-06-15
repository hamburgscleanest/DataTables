<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Exceptions\MultipleComponentAssertionException;
use hamburgscleanest\DataTables\Facades\TableRenderer;
use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class DataTable
 * @package hamburgscleanest\DataTables\Models
 */
class DataTable
{

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

    /** @var Model */
    private $_model;

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
    public function model(string $modelName, array $columns = []): DataTable
    {
        if (!\is_subclass_of($modelName, Model::class)) {
            throw new RuntimeException('Class "' . $modelName . '" is not an active record!');
        }

        $this->_model = new $modelName;
        $this->_queryBuilder = $this->_model->newQuery();
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
        foreach ($columns as $column => $formatter) {
            [$column, $formatter] = $this->_setColumnFormatter($column, $formatter);
            $columnModels[] = new Column($column, $formatter, $this->_model);
        }

        return $columnModels;
    }

    /**
     * @param $column
     * @param $formatter
     * @return array
     */
    private function _setColumnFormatter($column, $formatter): array
    {
        if (\is_int($column)) {
            $column = $formatter;
            $formatter = null;
        }

        return [$column, $formatter];
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->_columns;
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
     * @param string|null $name
     * @return $this|DataTable
     */
    public function addComponent(DataComponent $component, ?string $name = null): DataTable
    {
        $componentName = \str_replace(' ', '', Str::lower($name ?? \class_basename($component)));
        if ($this->componentExists($componentName)) throw new MultipleComponentAssertionException();
        $this->_components[$componentName] = $component->init($this);

        return $this;
    }

    /**
     * Check whether a component exists for the given data table.
     * @param string $componentName
     * @return bool
     */
    public function componentExists(string $componentName): bool
    {
        return \array_key_exists($componentName, $this->_components);
    }

    /**
     * Add a formatter for the column headers.
     *
     * @param HeaderFormatter $headerFormatter
     * @return DataTable
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
            function ($index, $column) use ($columnName) {
                /** @var Column $column */
                return $column->getName() === $columnName;
            }
        );

        if ($column !== null) {
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
     * @return DataTable
     */
    public function with(array $relations): DataTable
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
    public function noDataHtml(string $html): DataTable
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
    public function noDataView(string $viewName): DataTable
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
    public function render(): string
    {
        $data = $this->_getData();

        if ($data->count() === 0) {
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
    private function _getData(): Collection
    {
        if ($this->_queryBuilder === null) {
            throw new RuntimeException('Unknown base model!');
        }

        $this->_addRelations();

        /** @var DataComponent $component */
        foreach ($this->_components as $component) {
            $component->transformData();
        }

        return $this->_setSelection()->_queryBuilder->get();
    }

    private function _addRelations(): void
    {
        if (\count($this->_relations) === 0) {
            return;
        }

        foreach ($this->_relations as $relation) {
            $this->_addJoin($relation, $this->_model->$relation());
        }

        $this->_queryBuilder->getQuery()->groupBy($this->_model->getTable() . '.' . $this->_model->getKeyName());
    }

    /**
     * @param string $relation
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relationship
     */
    private function _addJoin(string $relation, \Illuminate\Database\Eloquent\Relations\Relation $relationship): void
    {
        /** @var Model $related */
        $related = $relationship->getRelated();

        $this->_queryBuilder->join(
            $related->getTable() . ' AS ' . $relation,
            $this->_model->getTable() . '.' . $this->_model->getKeyName(),
            '=',
            $relation . '.' . $related->getForeignKey()
        );
    }

    /**
     * @return DataTable
     */
    private function _setSelection(): DataTable
    {
        $query = $this->_queryBuilder->getQuery();

        $columns = $this->_getColumnsForSelect();
        if (!empty($columns)) {
            $query->selectRaw(
                \implode(',',
                    \array_map(function ($column) {
                        return $column->getIdentifier();
                    }, $columns)
                )
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    private function _getColumnsForSelect(): array
    {
        return \array_filter(
            $this->_columns,
            function ($column) {
                return !$column->isMutated();
            }
        );
    }

    private function _initColumns(): void
    {
        if (\count($this->_columns) === 0) {
            $this->_columns = $this->_fetchColumns(Schema::getColumnListing($this->_queryBuilder->getQuery()->from));
        }
    }

    /**
     * @return array
     */
    private function _fetchHeaders(): array
    {
        return \array_map(
            function ($column) {
                /** @var Column $column */
                return new Header($column->getKey());
            },
            $this->_columns
        );
    }

    public function __get($name)
    {
        if (\array_key_exists($name, $this->_components)) return $this->_components[$name];

        return $this->$name;
    }
}