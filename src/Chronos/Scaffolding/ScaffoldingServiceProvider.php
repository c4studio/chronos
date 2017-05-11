<?php

namespace Chronos\Scaffolding;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ScaffoldingServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        // require routes
        if (!$this->app->routesAreCached()) {
            $this->app['router']->group(['middleware' => 'web', 'namespace' => 'Chronos\Scaffolding\Http\Controllers', 'prefix' => 'admin'], function () {
                require __DIR__ . '/routes/web.php';
            });
            $this->app['router']->group(['middleware' => App::environment('local', 'staging') ? 'api' : ['auth:api', 'bindings'], 'namespace' => 'Chronos\Scaffolding\Api\Controllers', 'prefix' => 'api'], function () {
                require __DIR__ . '/routes/api.php';
            });
        }

        // publish config
        $this->publishes([
            __DIR__ . '/config/chronos.php' => config_path('chronos.php'),
        ], 'config');

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // load translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'chronos.scaffolding');

        // load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'chronos');

        // publish views so they can be overridden
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/chronos'),
        ], 'views');

        // publish assets
        $this->publishes([
            __DIR__ . '/assets/' => public_path('chronos'),
        ], 'public');

        // register gates
        $this->registerGates($gate);

        // register menu
        $this->registerMenu();
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // register custom ExceptionHandler
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Chronos\Scaffolding\App\Exceptions\Handler::class
        );

        // default package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/defaults.php', 'chronos'
        );
    }



    /**
     * Register gates.
     */
    protected function registerGates($gate)
    {
        if (class_exists('Chronos\Scaffolding\Models\Permission') && Schema::hasTable('roles')) {
            $permissions = Chronos\Scaffolding\Models\Permission::all();
            foreach ($permissions as $permission) {
                $gate->define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        }
    }

    /**
     * Register menu and share it with all views.
     */
    protected function registerMenu()
    {
        \Menu::make('ChronosMenu', function($menu) {
//            $menu->add(trans('chronos.scaffolding::menu.Dashboard'), ['route' => 'chronos.dashboard'])
//                ->prepend('<span class="icon c4icon-dashboard"></span>')
//                ->data('order', 1)->data('permissions', ['view_dashboard']);

            $users_menu = $menu->add(trans('chronos.scaffolding::menu.Users'), null)
                ->prepend('<span class="icon c4icon-user-3"></span>')
                ->data('order', 800)->data('permissions', ['view_roles', 'edit_permissions']);
            $users_menu->add(trans('chronos.scaffolding::menu.Roles'), ['route' => 'chronos.users.roles'])
                ->data('order', 810)->data('permissions', ['view_roles']);
            $users_menu->add(trans('chronos.scaffolding::menu.Permissions'), ['route' => 'chronos.users.permissions'])
                ->data('order', 820)->data('permissions', ['edit_permissions']);


            $settings_menu = $menu->add(trans('chronos.scaffolding::menu.Settings'), null)
                ->prepend('<span class="icon c4icon-sliders-1"></span>')
                ->data('order', 900)->data('permissions', ['edit_settings', 'edit_access_tokens', 'edit_image_styles']);
            $settings_menu->add(trans('chronos.scaffolding::menu.Access tokens'), ['route' => 'chronos.settings.access_tokens'])
                ->data('order', 910)->data('permissions', ['edit_access_tokens']);
            $settings_menu->add(trans('chronos.scaffolding::menu.Image styles'), ['route' => 'chronos.settings.image_styles'])
                ->data('order', 910)->data('permissions', ['view_image_styles']);
        });

        \View::composer('*', function($view) {
            $view->with('chronos_menu', \Menu::get('ChronosMenu')->sortBy('order'));
        });
    }

}