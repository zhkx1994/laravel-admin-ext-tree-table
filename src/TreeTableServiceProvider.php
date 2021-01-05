<?php

namespace Zhkx1994\TreeTable;

use Encore\Admin\Facades\Admin;
use Illuminate\Support\ServiceProvider;

class TreeTableServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(TreeTable $extension)
    {
        if (! TreeTable::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'treetable');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/zhkx1994/laravel-admin-ext-tree-table')],
                'treetable'
            );
        }

        $this->app->booted(function () {
            TreeTable::routes(__DIR__.'/../routes/web.php');
        });

    }
}
