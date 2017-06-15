<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters\Adapters\Icon;

/**
 * Class IconAdapter
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
interface IconAdapter {

    /**
     * Return the icon,
     * e.g. FontAwesome: <i class="fa fa-{$iconName}"></i>
     *
     * @param string $iconName
     * @return string
     */
    public function format(string $iconName) : string;
}