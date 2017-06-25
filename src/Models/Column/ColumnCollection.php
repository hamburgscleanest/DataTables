<?php

namespace hamburgscleanest\DataTables\Models\Column;

use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ColumnCollection
 * @package hamburgscleanest\DataTables\Models\Column
 */
class ColumnCollection extends Collection {

    /** @var Model */
    private $_model;

    /**
     * Create a new collection of columns.
     *
     * @param  mixed $items
     * @param Model $model
     */
    public function __construct($items, Model $model)
    {
        $this->_model = $model;

        parent::__construct($this->_fetchColumns($items));
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
            $columnModels[] = new Column($column, $formatter, $this->_model);
        }

        return $columnModels;
    }

    /**
     * @param string|int $column
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
     * Push a column onto the end of the collection.
     *
     * @param  mixed $value
     * @return ColumnCollection
     */
    public function push($value) : ColumnCollection
    {
        $this->items += $this->_fetchColumns(\array_wrap($value));

        return $this;
    }

    /**
     * @return array
     */
    public function getUnmutatedColumns() : array
    {
        return \array_filter($this->items, function($column) {
            /** @var Column $column */
            return !$column->isMutated();
        });
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return \array_map(
            function($column) {
                /** @var Column $column */
                return new Header($column->getKey());
            },
            $this->items
        );
    }
}