<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Template\Loader;

/**
 * Component loader service provider
 */
class TemplateServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['template.loader'] = function ($app)
		{
			$options = [
				'path_app'  => PATH_APP . DS . 'templates',
				'path_core' => PATH_CORE . DS . 'templates',
				'style'     => 0,
				'lang'      => ''
			];

			return new Loader($app, $options);
		};

		$this->app['template'] = function ($app)
		{
			$loader = $app['template.loader'];

			if ($app->has('menu'))
			{
				$menu = $app['menu'];

				if (!($item = $menu->getActive()))
				{
					$item = $menu->getItem($app['request']->getInt('Itemid', 0));
				}

				if (is_object($item))
				{
					$loader->setStyle($item->template_style_id);
				}

				if ($app->has('language.filter'))
				{
					$loader->setLang($app['language']->getTag());
				}
			}

			if ($style = $app['request']->getVar('templateStyle', 0))
			{
				$loader->setStyle($style);
			}

			return $loader->load();
		};
	}
}
