<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Http\Request;

/**
 * Class TranslateHeader
 *
 * @package hamburgscleanest\DataTables\Models\HeaderFormatters
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
     */
    public function format(Header $header) : void
    {
        $headerAttributeName = $header->getAttributeName();

        if (isset($this->_translations[$headerAttributeName]))
        {
            $header->key = $this->_translations[$headerAttributeName];
        }
    }
}