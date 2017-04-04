<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use function str_replace;

/**
 * Class TranslatableHeader
 *
 * @package hamburgscleanest\DataTables\Models
 */
class TranslatableHeader implements HeaderFormatter {

    /** @var array */
    private $_translations;

    /**
     * TranslatableHeader constructor.
     * @param array $translations
     */
    public function __construct(array $translations)
    {
        $this->_translations = $translations;
    }

    /**
     * Format the given header.
     * For example add a link to sort by this header/column.
     *
     * @param array $header
     * @param Request $request
     */
    public function format(array &$header, Request $request)
    {
        if (isset($this->_translations[$header['attribute']]))
        {
            $header['name'] = $this->_translations[$header['attribute']];
        }
    }
}