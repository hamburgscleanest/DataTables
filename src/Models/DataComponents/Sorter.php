<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;
use function implode;

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
     * @return Builder
     */
    public function _shapeData(): Builder
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

                $this->_queryBuilder->orderBy($fieldName, $direction);
            }
        }

        return $this->_queryBuilder;
    }

    /**
     * Stop sorting by this column
     *
     * @param string $field
     *
     * @return $this
     */
    public function removeField(string $field)
    {
        if (isset($this->_sortFields[$field]))
        {
            unset($this->_sortFields[$field]);
        }

        return $this;
    }

    /**
     * Sort by this column.
     *
     * @param string $field
     * @param string $direction
     *
     * @return $this
     */
    public function addField(string $field, string $direction = 'asc')
    {
        $this->_sortFields[$field] = \mb_strtolower($direction);

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return implode(', ', $this->_sortFields);
    }

    protected function _readFromSession()
    {
        $this->_sortFields = (array) SessionHelper::getState($this->_request, $this->_rememberKey, []);
    }

    protected function _storeInSession()
    {
        SessionHelper::saveState($this->_request, $this->_rememberKey, $this->_sortFields);
    }

    protected function _afterInit()
    {
        /** @var string $sortFields */
        $sortFields = $this->_request->get('sort');
        if (empty($sortFields))
        {
            return;
        }

        $this->_initFields($sortFields);
    }

    /**
     * @param string $fields
     */
    private function _initFields(string $fields)
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
                SessionHelper::removeState($this->_request, $this->_rememberKey . '.' . $sortParts[0]);
            }

            $this->_sortFields[$sortParts[0]] = $sortParts[1];
        }
    }
}