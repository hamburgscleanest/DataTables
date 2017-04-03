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
    private $_sortFields;

    protected function _afterInit()
    {
        /** @var string $sortFields */
        $sortFields = $this->_request->get('sort');

        if (empty($sortFields))
        {
            return;
        }

        $this->_sortFields = \array_map(
            function ($sorting)
            {
                if (mb_strpos($sorting, ':') === false)
                {
                    $sorting .= ':asc';
                }

                return $sorting;
            },
            \explode(',', $sortFields)
        );
    }

    /**
     * @return Builder
     */
    public function shapeData(): Builder
    {
        if (\count($this->_sortFields) > 0)
        {
            foreach ($this->_sortFields as $sortField)
            {
                $sorting = \explode(':', $sortField);

                $this->_queryBuilder->orderBy($sorting[0], $sorting[1]);
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
        $this->_sortFields[] = $field . ':' . $direction;

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