<?php

namespace hamburgscleanest\DataTables\Models;

use function array_keys;
use function array_map;
use Closure;
use RuntimeException;
use function str_replace;

class DataTable {

    /**
     * @var array
     */
    private $_data;

    /** @var Closure */
    private $_rowRenderer; // TODO: IColumnFormatter => DateColumnFormatter etc.

    /** @var array */
    private $_headers = [];

    /** @var string */
    private $_classes;

    /**
     * Set the data which should be displayed.
     *
     * @param array $data
     * @param Closure $customRowRenderer
     *
     * @return $this
     */
    public function data(array $data, Closure $customRowRenderer = null)
    {
        $this->_data = $data;
        $this->_rowRenderer = $customRowRenderer;

        return $this;
    }

    /**
     * Set the table headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function headers(array $headers)
    {
        $this->_headers = $headers;

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
     * Generates Headers by inspecting the first row.
     */
    private function _generateHeaders()
    {
        if (empty($this->_data))
        {
            return;
        }

        if (count($this->_data) === 0)
        {
            return; // TODO: What to do when no headers defined and there is no data?
        }

        $this->_headers = array_map(function ($header)
        {
            return str_replace('_', ' ', ucfirst($header));
        },
            array_keys($this->_data[0])
        );
    }

    /**
     * Renders the column headers.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function _renderHeaders()
    {
        if (empty($this->_headers))
        {
            $this->_generateHeaders();
        } else if (count($this->_headers) !== count(array_keys($this->_data[0])))
        {
            throw new RuntimeException('The headers count does not match the columnt count!');
        }

        $html = '<tr>';
        foreach ($this->_headers as $header)
        {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Displays the table body.
     *
     * @return string
     */
    private function _renderBody()
    {
        $html = '';
        foreach ($this->_data as $row)
        {
            $html .= $this->_renderRow($row);
        }

        return $html;
    }

    /**
     * Displays a single row.
     *
     * @param array $row
     * @return string
     */
    private function _renderRow(array $row)
    {
        if ($this->_rowRenderer !== null)
        {
            $row = $this->_rowRenderer->call($this, $row);
        }

        $html = '<tr>';
        foreach ($row as $column)
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
     * @throws \RuntimeException
     */
    public function render()
    {
        if (empty($this->_data))
        {
            // TODO: handle empty data
            throw new RuntimeException('No data was set.');
        }

        return $this->_open() . $this->_renderHeaders() . $this->_renderBody() . $this->_close();
    }
}