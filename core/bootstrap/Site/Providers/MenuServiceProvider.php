<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

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

			$options['db'] = $app->get('db');

			if ($app->has('language.filter'))
			{
				$options['language_filter'] = $app->get('language.filter');
				$options['language']        = $app->get('language')->getTag();
			}

			return $app['menu.manager']->menu($app['client']->name, $options);
		};

		$this->app['menu.params'] = function($app)
		{
			$params = new Registry();

			$menu = $app['menu']->getActive();

			if (is_object($menu))
			{
				$params = $menu->params;
			}
			elseif ($app->has('component'))
			{
				$temp = clone $app['component']->params('com_menus');
				$params->merge($temp);
			}

			return $params;
		};
	}
}
