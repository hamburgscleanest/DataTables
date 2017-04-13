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
class DataTablesServiceProvider extends ServiceProvider {

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
        $this->app->bind('datatable', function($app)
        {
            return new DataTable($app->request);
        });

        $this->app->singleton('session_helper', function($app)
        {
            return new SessionHelper($app->request);
        });

        $this->app->singleton('url_helper', function()
        {
            return new UrlHelper();
        });

        $this->app->singleton('table_renderer', function($app)
        {
            return new TableRenderer($app->request);
        });

        $this->app->booting(function()
        {
            AliasLoader::getInstance()->alias('DataTable', DataTableFacade::class);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['datatable'];
    }
}