<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;
use function str_replace;

/**
 * Class TranslatableHeader
 *
 * @package hamburgscleanest\DataTables\Models
 */
class TranslatableHeader implements HeaderFormatter {


    /**
     * Format the given header.
     * For example add a link to sort by this header/column.
     *
     * @param string $header
     * @param Request $request
     * @return
     */
    public function format(string &$header, Request $request)
    {
        // TODO: Implement format() method.
    }
}