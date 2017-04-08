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
    private $_originalName;

    /** @var string */
    public $name;

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
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->_originalName;
    }

    /**
     * @param HeaderFormatter $headerFormatter
     * @param Request $request
     */
    public function format(HeaderFormatter $headerFormatter, Request $request)
    {
        $headerFormatter->format($this, $request);
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
}