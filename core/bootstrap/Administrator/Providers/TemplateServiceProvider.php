<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Template\Loader;

/**
 * Template loader service provider
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

			$options['style'] = \User::getParam('admin_style', $options['style']);

			return new Loader($app, $options);
		};

		$this->app['template'] = function ($app)
		{
			$loader = $app['template.loader'];

			if ($style = $app['request']->getVar('templateStyle', 0))
			{
				$loader->setStyle($style);
			}

			return $loader->load();
		};
	}
}
