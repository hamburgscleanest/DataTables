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
    public $name;

    /** @var string */
    private $_attributeName;

    /**
     * Header constructor.
     */
    public function __construct(Column $column)
    {
        $this->_attributeName = $column->getName();
        if ($column->isRelation())
        {
            $aggregate = $column->getAggregate();
            $this->_attributeName = ($aggregate !== 'first' ? ($aggregate . '_') : '') . $column->getRelation() . '_' . $this->_attributeName;
        }

        $this->name = $this->_attributeName;
    }

    /**
     * Get the original attribute name.
     *
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->_attributeName;
    }

    /**
     * @param array $headerFormatters
     * @param Request $request
     */
    public function formatArray(array $headerFormatters, Request $request)
    {
        foreach ($headerFormatters as $formatter)
        {
            $this->format($formatter, $request);
        }
    }

    /**
     * @param HeaderFormatter $headerFormatter
     * @param Request $request
     */
    public function format(HeaderFormatter $headerFormatter, Request $request)
    {
        $headerFormatter->format($this, $request);
    }
}