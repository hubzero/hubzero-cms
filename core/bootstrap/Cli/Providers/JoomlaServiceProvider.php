<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Cli\Providers;

use Hubzero\Base\ServiceProvider;

/**
 * Joomla handler service provider
 * 
 * This loads in the core Joomla framework and instantiates
 * the base application class.
 */
class JoomlaServiceProvider extends ServiceProvider
{
	/**
	 * Register the exception handler.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if (!defined('JDEBUG'))
		{
			define('JDEBUG', $this->app['config']->get('debug'));
		}
		if (!defined('JPROFILE'))
		{
			define('JPROFILE', $this->app['config']->get('debug') || $this->app['config']->get('profile'));
		}

		require_once PATH_CORE . DS . 'libraries' . DS . 'import.php';
		require_once PATH_CORE . DS . 'libraries' . DS . 'cms.php';

		jimport('joomla.application.menu');
		jimport('joomla.environment.uri');
		jimport('joomla.utilities.utility');
		jimport('joomla.event.dispatcher');
		jimport('joomla.utilities.arrayhelper');

		$app = \JFactory::getApplication('cli');
	}
}
