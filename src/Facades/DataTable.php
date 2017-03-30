<?php

namespace hamburgscleanest\DataTables\Facades;

use Illuminate\Support\Facades\Facade;

class DataTable extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'datatable'; }
}