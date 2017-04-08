<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use hamburgscleanest\DataTables\Models\Header;
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
     * @param Header $header
     * @param Request $request
     */
    public function format(Header $header, Request $request)
    {
        $headerAttributeName = $header->getOriginalName();

        if (isset($this->_translations[$headerAttributeName]))
        {
            $header->name = $this->_translations[$headerAttributeName];
        }
    }
}