<?php

namespace hamburgscleanest\DataTables\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface HeaderFormatter
 * @package hamburgscleanest\DataTables\Interfaces
 */
interface HeaderFormatter {

    /**
     * Format the given header.
     * For example add a link to sort by this header/column.
     *
     * @param string $header
     * @param Request $request
     * @return
     */
    public function format(string &$header, Request $request);
}