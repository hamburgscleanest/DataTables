<?php

namespace hamburgscleanest\DataTables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class UrlHelper
 * @package hamburgscleanest\DataTables\Facades
 */
class UrlHelper extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string { return 'url_helper'; }
}