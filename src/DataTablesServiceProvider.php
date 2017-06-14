<?php

namespace hamburgscleanest\DataTables;

use hamburgscleanest\DataTables\Facades\DataTable as DataTableFacade;
use hamburgscleanest\DataTables\Helpers\SessionHelper;
use hamburgscleanest\DataTables\Helpers\TableRenderer;
use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Models\DataTable;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * Class DataTablesServiceProvider
 * @package hamburgscleanest\DataTables
 */
class DataTablesServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->_registerDataTable();
        $this->_registerSessionHelper();
        $this->_registerUrlHelper();
        $this->_registerTableRenderer();

        $this->_setDataTableAlias();
    }

    private function _registerDataTable(): void
    {
        $this->app->bind('datatable', function () {
            return new DataTable();
        });
    }

    private function _registerSessionHelper(): void
    {
        $this->app->singleton('session_helper', function () {
            return new SessionHelper();
        });
    }

    private function _registerUrlHelper(): void
    {
        $this->app->singleton('url_helper', function () {
            return new UrlHelper();
        });
    }

    private function _registerTableRenderer(): void
    {
        $this->app->singleton('table_renderer', function () {
            return new TableRenderer();
        });
    }

    private function _setDataTableAlias(): void
    {
        $this->app->booting(function () {
            AliasLoader::getInstance()->alias('DataTable', DataTableFacade::class);
        });
    }
}