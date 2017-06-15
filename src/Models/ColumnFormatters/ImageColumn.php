<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;

/**
 * Class ImageColumn
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
class ImageColumn implements ColumnFormatter {

    /** @var string */
    private $_classes;

    /** @var string */
    private $_fallback;

    /**
     * ImageColumn constructor.
     * @param null|string $fallback
     * @param null|string $classes
     */
    public function __construct(? string $fallback = null, ? string $classes = 'image-column')
    {
        $this->_fallback = $fallback;
        $this->_classes = $classes;
    }

    /**
     * @param string $column
     * @return string
     */
    public function format(string $column) : string
    {
        if (!\file_exists($column))
        {
            return $this->_fallback ?? '';
        }

        return $this->_renderImage($column);
    }

    /**
     * @param string $path
     * @return string
     */
    private function _renderImage(string $path) : string
    {
        return '<img src="' . $path . '" class="' . $this->_classes . '"/>';
    }

    /**
     * Add styling to the image.
     *
     * @param string $classes
     * @return ImageColumn
     */
    public function classes(string $classes) : ImageColumn
    {
        $this->_classes = $classes;

        return $this;
    }

    /**
     * Set a fallback value which is used when the image is not found.
     *
     * @param string $fallback
     * @return ImageColumn
     */
    public function fallback(string $fallback) : ImageColumn
    {
        $this->_fallback = $fallback;

        return $this;
    }
}