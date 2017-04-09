<?php

namespace hamburgscleanest\DataTables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class DataTable
 * @package hamburgscleanest\DataTables\Facades
 */
class DataTable extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'datatable'; }
}