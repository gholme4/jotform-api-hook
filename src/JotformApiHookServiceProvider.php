<?php

namespace JotformApiHook;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Setting;
use Voyager;

class JotformApiHookServiceProvider extends ServiceProvider
{

    static public $exportedSpreadsheetFilters = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load blade templates
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'jotform_api');

        try {
            // Add jotform API key Voyager setting
            $setting = Setting::firstOrNew(['key' => 'admin.jotform_api_key']);
            if (!$setting->exists) {
                $setting->fill([
                    'display_name' => 'Jotform API Key',
                    'value'        => '',
                    'details'      => '',
                    'type'         => 'text',
                    'order'        => 1,
                    'group'        => 'Admin',
                ])->save();
            }
        }
        catch(\Exception $e) {

        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app(Dispatcher::class)->listen('voyager.menu.display', function ($menu) {
            $this->addThemeMenuItem($menu);
        });


        app(Dispatcher::class)->listen('voyager.admin.routing', function ($router) {
            $this->addThemeRoutes($router);
        });
    
    }

    /**
     * Admin theme routes.
     *
     * @param $router
     */
    
    public function addThemeRoutes($router)
    {
        $namespacePrefix = '\\JotformApiHook\\Http\\Controllers\\';
      
        $router->get('jotform-api', ['uses' => $namespacePrefix.'JotformApiController@index', 'as' => 'jotform_api.index']);
        $router->get('jotform-api/form/{formId}', ['uses' => $namespacePrefix.'JotformApiController@form', 'as' => 'jotform_api.form']);
        $router->get('jotform-api/form/{formId}/export', ['uses' => $namespacePrefix.'JotformApiController@exportFormSubmissions', 'as' => 'jotform_api.export_form_submissions']);
        $router->get('jotform-api/forms', ['uses' => $namespacePrefix.'JotformApiController@forms', 'as' => 'jotform_api.forms']);
        $router->get('jotform-api/submission/{submissionId}', ['uses' => $namespacePrefix.'JotformApiController@submission', 'as' => 'jotform_api.submission']);
     
    }

    /**
     * Adds the Theme icon to the admin menu.
     *
     * @param TCG\Voyager\Models\Menu $menu
     */
    public function addThemeMenuItem(Menu $menu)
    {
        if ($menu->name == 'admin') {
            $formsUrl = route('voyager.jotform_api.forms', [], false);

            $menuItem = $menu->items->where('url', $formsUrl)->first();

            if (is_null($menuItem)) {

                $indexMenuItem = MenuItem::create([
                    'menu_id' => $menu->id,
                    'url' => $formsUrl,
                    'title' => 'Jotform API',
                    'target' => '_self',
                    'icon_class' => 'voyager-file-text',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 99,
                ]);

                $menu->items->add(MenuItem::create([
                    'menu_id' => $menu->id,
                    'url' => $formsUrl,
                    'title' => 'Forms',
                    'target' => '_self',
                    'icon_class' => 'voyager-check',
                    'color' => null,
                    'parent_id' => $indexMenuItem->id,
                    'order' => 99,
                ]));

                $menu->items->add($indexMenuItem);

                $this->ensurePermissionExist();
                return redirect()->back();
            }
        }
    }

    /**
     * Add Permissions for Jotfrom API  if they do not exist yet.
     *
     * @return none
     */
    protected function ensurePermissionExist()
    {
        $permission = Permission::firstOrNew([
            'key' => 'jotform_api',
            'table_name' => 'admin',
        ]);
        if (!$permission->exists) {
            $permission->save();
            $role = Role::where('name', 'admin')->first();
            if (!is_null($role)) {
                $role->permissions()->attach($permission);
            }
        }
    }

     /**
     *
     * @param callable $callback
     */
    static function filterExportedSpreadsheet($callback) {
        array_push(self::$exportedSpreadsheetFilters, $callback); 
    }

}