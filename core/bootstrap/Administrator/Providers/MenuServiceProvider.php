<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Menu\Manager;
use Hubzero\Base\ServiceProvider;
use Hubzero\Config\Registry;

/**
 * Menu service provider
 */
class MenuServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['menu.manager'] = function($app)
		{
			return $manager = new Manager();
		};

		$this->app['menu'] = function($app)
		{
			$options = [
				'language_filter' => null,
				'language'        => null,
				'access'          => \User::getAuthorisedViewLevels()
			];

			return $app['menu.manager']->menu($app['client']->name, $options);
		};

		$this->app['menu.params'] = function($app)
		{
			$params = new Registry();

			$menu = $app['menu']->getActive();

			if (is_object($menu))
			{
				$params->parse($menu->params);
			}
			else if ($app->has('component'))
			{
				$temp = clone $app['component']->params('com_menus');
				$params->merge($temp);
			}

			return $params;
		};
	}
}
