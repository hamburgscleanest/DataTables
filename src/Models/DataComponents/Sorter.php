<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Models\Column;
use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class Sorter
 * @package hamburgscleanest\DataTables\Models\DataComponents
 */
class Sorter extends DataComponent {

    const SORTING_SEPARATOR = '~';
    const COLUMN_SEPARATOR  = '.';

    /** @var array */
    private $_sortFields = [];

    /**
     * Sorter constructor.
     * @param null|array $fields
     * @param bool $remember
     */
    public function __construct(array $fields = null, bool $remember = false)
    {
        $this->_rememberKey = 'sort';
        $this->_rememberState = $remember;

        if ($fields !== null)
        {
            foreach ($fields as $fieldName => $direction)
            {
                if (\is_int($fieldName))
                {
                    $fieldName = $direction;
                    $direction = 'asc';
                }

                $this->_sortFields[$fieldName] = \mb_strtolower($direction);
            }
        }
    }

    /**
     * Sort by this column.
     *
     * @param string $field
     * @param string $direction
     *
     * @return Sorter
     */
    public function addField(string $field, string $direction = 'asc') : Sorter
    {
        $this->_sortFields[$field] = \mb_strtolower($direction);

        return $this;
    }

    /**
     * @return string
     */
    public function render() : string
    {
        return implode(', ', \array_keys($this->_sortFields));
    }

    /**
     * @return Builder
     */
    protected function _shapeData() : Builder
    {
        if (\count($this->_sortFields) > 0)
        {
            foreach ($this->_sortFields as $fieldName => $direction)
            {
                if ($direction === 'none')
                {
                    $this->removeField($fieldName);
                    continue;
                }

                $this->_sortField($fieldName, $direction);
            }
        }

        return $this->_dataTable->query();
    }

    /**
     * Stop sorting by this column
     *
     * @param string $field
     *
     * @return Sorter
     */
    public function removeField(string $field) : Sorter
    {
        if (isset($this->_sortFields[$field]))
        {
            unset($this->_sortFields[$field]);
        }

        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $direction
     */
    private function _sortField(string $fieldName, string $direction) : void
    {
        /** @var Column $column */
        $column = \array_first($this->_dataTable->getColumns(), function($index, $column) use ($fieldName) {
            /** @var Column $column */
            return $column->getKey() === $fieldName;
        });

        if ($column !== null)
        {
            $this->_dataTable->query()->orderBy(DB::raw($column->getAttributeName()), $direction);
            $this->_addGroupingForAggregate($column);
        }
    }

    /**
     * @param Column $column
     */
    private function _addGroupingForAggregate(Column $column) : void
    {
        $relation = $column->getRelation();
        if ($relation === null || $relation->aggregate === 'first')
        {
            return;
        }

        $this->_dataTable->query()->groupBy($relation->name . '.' . $column->getName());
    }

    protected function _readFromSession() : void
    {
        $this->_sortFields = (array) SessionHelper::getState($this->_rememberKey, []);
    }

    protected function _storeInSession() : void
    {
        SessionHelper::saveState($this->_rememberKey, $this->_sortFields);
    }

    protected function _afterInit() : void
    {
        /** @var string $sortFields */
        $sortFields = \request()->get('sort');
        if (empty($sortFields))
        {
            return;
        }

        $this->_initFields($sortFields);
    }

    /**
     * @param string $fields
     */
    private function _initFields(string $fields) : void
    {
        $this->_sortFields = [];
        foreach (\explode(self::COLUMN_SEPARATOR, $fields) as $field)
        {
            $sortParts = \explode(self::SORTING_SEPARATOR, $field);
            if (\count($sortParts) === 1)
            {
                $sortParts[1] = 'asc';
            }

            if ($sortParts[1] === 'none')
            {
                SessionHelper::removeState($this->_rememberKey . '.' . $sortParts[0]);
            }

            $this->_sortFields[$sortParts[0]] = $sortParts[1];
        }
    }
}