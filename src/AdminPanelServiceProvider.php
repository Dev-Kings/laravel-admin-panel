<?php

namespace DevKings\LaravelAdminPanel;

use Illuminate\Support\ServiceProvider;

class AdminPanelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ .'/admin-routes.php');

        $this->loadViewsFrom(__DIR__ .'/resources/views', 'admin-panel');
    }
}
