<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use function implode;

/**
 * Class Sorter
 * @package hamburgscleanest\DataTables\Models
 */
class Sorter extends DataComponent {

    /** @var array */
    private $_sortFields = [];

    /**
     * Sorter constructor.
     * @param array $fields
     */
    public function __construct(array $fields = null)
    {
        if ($fields !== null)
        {
            foreach ($fields as $fieldName => $direction)
            {
                if ($fieldName === 0)
                {
                    $fieldName = $direction;
                    $direction = 'asc';
                }

                $this->_sortFields[$fieldName] = $direction;
            }
        }
    }

    /**
     * @param string $fields
     */
    private function _initFields(string $fields)
    {
        $this->_sortFields = \array_map(
            function ($sorting)
            {
                if (mb_strpos($sorting, ':') === false)
                {
                    $sorting .= ':asc';
                }

                return $sorting;
            },
            \explode(',', $fields)
        );
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
     * @return Builder
     */
    public function shapeData(): Builder
    {
        if (\count($this->_sortFields) > 0)
        {
            foreach ($this->_sortFields as $fieldName => $direction)
            {
                $this->_queryBuilder->orderBy($fieldName, $direction);
            }
        }

        return $this->_queryBuilder;
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
        $this->_sortFields[$field] = $direction;

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return implode(', ', $this->_sortFields);
    }
}