<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;

/**
 * Class TranslateHeader
 *
 * @package hamburgscleanest\DataTables\Models
 */
class TranslateHeader implements HeaderFormatter {

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