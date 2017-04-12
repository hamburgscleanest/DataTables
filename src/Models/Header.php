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

        $relation = $column->getRelation();
        if ($relation !== null)
        {
            $aggregate = $relation->aggregate;
            $this->_attributeName = ($aggregate !== 'first' ? ($aggregate . '_') : '') . $relation->name . '_' . $this->_attributeName;
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
     * @return Header
     */
    public function formatArray(array $headerFormatters, Request $request): Header
    {
        foreach ($headerFormatters as $formatter)
        {
            $this->format($formatter, $request);
        }

        return $this;
    }

    /**
     * @param HeaderFormatter $headerFormatter
     * @param Request $request
     * @return Header
     */
    public function format(HeaderFormatter $headerFormatter, Request $request): Header
    {
        $headerFormatter->format($this, $request);

        return $this;
    }

    /**
     * @return string
     */
    public function print(): string
    {
        return '<th>' . $this->name . '</th>';
    }
}