<?php

namespace hamburgscleanest\DataTables\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface ColumnFormatter
 * @package hamburgscleanest\DataTables\Interfaces
 */
interface ColumnFormatter {

    /**
     * @param Model $rowModel
     * @param string $column
     * @return string
     */
    public function format(Model $rowModel, string $column) : string;
}