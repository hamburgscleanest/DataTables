<?php

namespace hamburgscleanest\DataTables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SessionHelper
 * @package hamburgscleanest\DataTables\Facades
 */
class SessionHelper extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'session_helper'; }
}