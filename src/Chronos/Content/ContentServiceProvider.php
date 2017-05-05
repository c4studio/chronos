<?php

namespace Chronos\Content;

use Chronos\Content\Models\ContentType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // require routes
        if (!$this->app->routesAreCached()) {
            $this->app['router']->group(['middleware' => 'web', 'namespace' => 'Chronos\Content\Http\Controllers', 'prefix' => 'admin'], function () {
                require __DIR__ . '/routes/web.php';
            });
            $this->app['router']->group(['middleware' => App::environment('local', 'staging') ? 'api' : ['auth:api', 'bindings'], 'namespace' => 'Chronos\Content\Api\Controllers', 'prefix' => 'api'], function () {
                require __DIR__ . '/routes/api.php';
            });
        }

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // load translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'chronos.content');

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

        // register menu
        $this->updateMenu();
    }



    /**
     * Updates menu.
     */
    protected function updateMenu()
    {
        $menu = \Menu::get('ChronosMenu');
        $submenu = $menu->add(trans('chronos.content::menu.Content'), null)
            ->prepend('<span class="icon c4icon-pencil-3"></span>')
            ->data('order', 100)->data('permissions', ['view_content_types', 'view_media']);

        if (Schema::hasTable('content_types')) {
            $types = ContentType::orderBy('name')->get();
            if ($types) {
                foreach ($types as $k => $type) {
                    $submenu->add($type->name, ['route' => ['chronos.content', 'type' => $type->id]])
                        ->data('order', 100 + $k * 5)->data('permissions', ['view_content_type_' . $type->id]);
                }
            }
        }
        $submenu->add(trans('chronos.content::menu.Media'), ['route' => 'chronos.content.media'])
                ->data('order', 800)->data('permissions', ['view_media']);
        $submenu->add(trans('chronos.content::menu.Content types'), ['route' => 'chronos.content.types'])
                ->data('order', 900)->data('permissions', ['view_content_types']);
    }

}