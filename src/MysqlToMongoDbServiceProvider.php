<?php

namespace ctf0\MysqlToMongoDb;

use Illuminate\Support\ServiceProvider;

class MysqlToMongoDbServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MysqlToMongo::class,
                Commands\MysqlToMongoPivot::class,
                Commands\MysqlToMongoCleanUp::class,
                Commands\MysqlToMongoRelation::class,
                Commands\MysqlToMongoMaintain::class,
            ]);
        }
    }

    /**
     * Register any package services.
     */
    public function register()
    {
    }
}
