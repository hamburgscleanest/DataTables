<?php

namespace hamburgscleanest\DataTables\Models;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Class DataTable
 * @package hamburgscleanest\DataTables\Models
 *
 * TODO: remember sorting (multiple columns) / pagination
 */
class DataTable {

    /** @var Request */
    private $_request;

    /** @var Builder */
    private $_queryBuilder;

    /** @var Paginator */
    private $_paginator;

    /** @var Closure */
    private $_rowRenderer; // TODO: IColumnFormatter => DateColumnFormatter etc.

    /** @var string */
    private $_classes;

    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Set the Query builder instance which is used to display the data.
     *
     * @param Builder $queryBuilder
     * @param Closure $customRowRenderer Custom function to manipulate the fetched rows.
     * @return $this
     */
    public function query(Builder $queryBuilder, Closure $customRowRenderer = null)
    {
        $this->_queryBuilder = $queryBuilder;
        $this->_paginator = new Paginator($this->_queryBuilder, $this->_request);
        $this->_rowRenderer = $customRowRenderer;

        return $this;
    }

    /**
     * Get data which should be displayed in the table.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \RuntimeException
     */
    private function _getData()
    {
        if ($this->_queryBuilder === null)
        {
            throw new RuntimeException('No query builder instance set!');
        }

        // TODO: OrderBy

        return $this->_paginator->doPagination()->get();
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function paginate($perPage = 15)
    {
        $this->_paginator->paginate($perPage);

        return $this;
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
     * @param array $columns
     * @return array
     */
    private function _extractColumnNames(array $columns)
    {
        return array_map(
            function ($column)
            {
                return $this->_getColumnAlias($column);
            },
            \explode(',', \implode(',', $columns)));
    }

    /**
     * @param string $column
     * @return string
     */
    private function _getColumnAlias(string $column)
    {
        $aliasPos = mb_strpos($column, ' as ');

        return $aliasPos !== false ? mb_substr($column, $aliasPos + 4) : $column;
    }

    /**
     * Renders the column headers.
     *
     * @return string
     */
    private function _renderHeaders()
    {
        $query = $this->_queryBuilder->getQuery();
        $headers = $query->columns !== null ? $this->_extractColumnNames($query->columns) : Schema::getColumnListing($query->from);

        $html = '<tr>';
        foreach ($headers as $header)
        {
            $html .= '<th>' . $header . '</th>';
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
            $this->_rowRenderer->call($this, $rowModel);
        }

        $html = '<tr>';
        foreach ($rowModel->getAttributes() as $column)
        {
            $html .= '<td>' . $column . '</td>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Starts the table.
     *
     * @return string
     */
    private function _open()
    {
        $class = !empty($this->_classes) ? ' class="' . $this->_classes . '"' : '';

        return '<table' . $class . '>';
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
     * @param Closure $noDataView
     *
     * @return mixed|string
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

    /**
     * @return int
     */
    public function pageCount()
    {
        return $this->_paginator->pageCount();
    }

    /**
     * Renders the pagination links.
     *
     * @return string
     */
    public function renderPagination()
    {
        return $this->_paginator->render();
    }
}