<?php

namespace hamburgscleanest\DataTables\Interfaces;

/**
 * Interface ColumnFormatter
 * @package hamburgscleanest\DataTables\Interfaces
 */
interface ColumnFormatter {

    /**
     * @param string $column
     * @return string
     */
    public function format(string $column): string;
}