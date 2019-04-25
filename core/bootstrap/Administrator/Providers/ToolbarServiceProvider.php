<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Html\Toolbar;

/**
 * Toolbar service provider
 */
class ToolbarServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->registerToolbar();

		$this->registerSubmenu();
	}

	/**
	 * Register the toolbar.
	 *
	 * @return  void
	 */
	public function registerToolbar()
	{
		$this->app['toolbar'] = function($app)
		{
			return new Toolbar('toolbar');
		};
	}

	/**
	 * Register the submenu.
	 *
	 * @return  void
	 */
	public function registerSubmenu()
	{
		$this->app['submenu'] = function($app)
		{
			return new Toolbar('submenu');
		};
	}
}
