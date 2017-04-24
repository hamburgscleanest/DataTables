<?php

namespace hamburgscleanest\DataTables\Helpers;

use hamburgscleanest\DataTables\Models\Column;
use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class TableRenderer
 * @package hamburgscleanest\DataTables\Helpers
 */
class TableRenderer {

    /**
     * Starts the table.
     *
     * @param null|string $classes
     * @return string
     */
    public function open(? string $classes = null) : string
    {
        return '<table class="' . ($classes ?? 'table') . '">';
    }

    /**
     * Closes the table.
     *
     * @return string
     */
    public function close() : string
    {
        return '</table>';
    }

    /**
     * Renders the column headers.
     *
     * @param array $headers
     * @param array $formatters
     * @return string
     */
    public function renderHeaders(array $headers, array $formatters = []) : string
    {
        $html = '<tr>';

        /** @var Header $header */
        foreach ($headers as $header)
        {
            $html .= $header->formatArray($formatters)->print();
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * Displays the table body.
     *
     * @param Collection $data
     *
     * @param array $columns
     * @return string
     */
    public function renderBody(Collection $data, array $columns = []) : string
    {
        $html = '';
        foreach ($data as $row)
        {
            $html .= $this->_renderRow($row, $columns);
        }

        return $html;
    }

    /**
     * Displays a single row.
     *
     * @param Model $rowModel
     *
     * @param array $columns
     * @return string
     */
    private function _renderRow(Model $rowModel, array $columns) : string
    {
        $html = '<tr>';
        /** @var Column $column */
        foreach ($columns as $column)
        {
            $html .= '<td>' . $column->getFormattedValue($rowModel) . '</td>';
        }
        $html .= '</tr>';

        return $html;
    }
}
