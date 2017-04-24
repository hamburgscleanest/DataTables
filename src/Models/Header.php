<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;

/**
 * Class Header
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Header {

    /** @var string */
    public $key;

    /** @var string */
    private $_attributeName;

    /**
     * Header constructor.
     * @param string $columnKey
     */
    public function __construct(string $columnKey)
    {
        $this->_attributeName = $this->key = $columnKey;
    }

    /**
     * Get the original attribute name.
     *
     * @return string
     */
    public function getAttributeName() : string
    {
        return $this->_attributeName;
    }

    /**
     * @param array $headerFormatters
     * @return Header
     */
    public function formatArray(array $headerFormatters) : Header
    {
        foreach ($headerFormatters as $formatter)
        {
            $this->format($formatter);
        }

        return $this;
    }

    /**
     * @param HeaderFormatter $headerFormatter
     * @return Header
     */
    public function format(HeaderFormatter $headerFormatter) : Header
    {
        $headerFormatter->format($this);

        return $this;
    }

    /**
     * @return string
     */
    public function print() : string
    {
        return '<th>' . $this->key . '</th>';
    }
}