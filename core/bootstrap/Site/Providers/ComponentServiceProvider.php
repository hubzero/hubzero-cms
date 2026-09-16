<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Component\Loader;
use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Component loader service provider
 */
class ComponentServiceProvider extends Middleware
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['component'] = function ($app) {
            return new Loader($app);
        };
    }

    /**
     * Handle request in HTTP stack
     *
     * @param   object  $request  HTTP Request
     * @return  mixed
     */
    public function handle(Request $request)
    {
        $response = $this->next($request);

        if (!$this->app->runningInConsole()) {
            $component = $request->getCmd('option');

            if (!$component) {
                // A page is allowed to name no component when its menu item
                // does: the item's type is 'none', the template draws the page
                // itself, and the component buffer is left empty for it. The
                // front page is the usual case, but the item form lets any
                // such page be shown in the menu, and a page the menu links to
                // has to answer. Every other address without a component is
                // still a page that does not exist - including the front page
                // of a hub with no home menu item at all, which has not asked
                // for anything and should say so.
                $menu   = $this->app->has('menu') ? $this->app['menu'] : null;
                $active = $menu ? $menu->getActive() : null;
                $drawn  = is_object($active) && (!empty($active->home) || $active->type == 'none');

                if (!$drawn) {
                    $this->app->abort(404, 'Component not found.');
                }
            } else {
                $contents = $this->app['component']->render($component);

                $response->setContent($contents);
            }

            $this->app['dispatcher']->trigger('system.onAfterDispatch');

            if ($this->app->has('profiler')) {
                $this->app['profiler'] ? $this->app['profiler']->mark('afterDispatch') : null;
            }
        }

        return $response;
    }
}
