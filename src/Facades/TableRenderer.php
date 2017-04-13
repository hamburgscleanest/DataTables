<?php

namespace hamburgscleanest\DataTables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TableRenderer
 * @package hamburgscleanest\DataTables\Facades
 */
class TableRenderer extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'table_renderer'; }
}