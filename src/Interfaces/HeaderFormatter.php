<?php

namespace hamburgscleanest\DataTables\Interfaces;

use hamburgscleanest\DataTables\Models\Header;

/**
 * Interface HeaderFormatter
 * @package hamburgscleanest\DataTables\Interfaces
 */
interface HeaderFormatter {

    /**
     * Format the given header.
     * For example add a link to sort by this header/column.
     *
     * @param Header $header
     * @return void
     */
    public function format(Header $header) : void;
}