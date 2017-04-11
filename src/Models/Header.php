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
    private $_originalName;

    /**
     * Header constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->_originalName = $name;
        $this->name = $name;
    }

    /**
     * @param Column $column
     * @return Header
     */
    public static function createFromColumn(Column $column): Header
    {
        return new static($column->name);
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->_originalName;
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