<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Html\Editor;
use Hubzero\Facades\User;

/**
 * Editor service provider
 */
class EditorServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['editor'] = function ($app) {
            $global = $app['config']->get('editor');

            $editor = User::getParam('editor', $global);

            if (!$app['plugin']->isEnabled('editors', $editor)) {
                $editor = $global;
                if (!$app['plugin']->isEnabled('editors', $editor)) {
                    $editor = 'none';
                }
            }

            $app['config']->set('editor', $editor);

            return new Editor($editor);
        };
    }
}
