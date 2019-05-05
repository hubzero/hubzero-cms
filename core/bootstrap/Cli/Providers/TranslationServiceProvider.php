<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Cli\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Language\Translator;

/**
 * Language translation service provider
 */
class TranslationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['language'] = function($app)
		{
			$locale = $app['config']->get('locale', 'en-GB');
			$debug  = $app['config']->get('debug_lang', false);

			return new Translator($locale, $debug, ucfirst($app['client']->name));
		};

		$this->app['language.filter'] = false;
	}

	/**
	 * Add the plugin loader to the event dispatcher.
	 *
	 * @return  void
	 */
	public function boot()
	{
		$translator = $this->app['language'];

		$language = null;

		// If a language was specified it has priority
		if (!$language && $this->app->has('request'))
		{
			$lang = $this->app['request']->getString('language', null);

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect user specified language
		if (!$language && $this->app->has('user'))
		{
			$lang = \User::getParam('api_language');

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect default language
		if (!$language && $this->app->has('component'))
		{
			try
			{
				$params = $this->app['component']->params('com_languages');
			}
			catch (\Hubzero\Database\Exception\QueryFailedException $e)
			{
				$params = new \Hubzero\Config\Registry;
			}

			$language = $params->get(
				$this->app['client']->name,
				$this->app['config']->get('language', 'en-GB')
			);
		}

		// One last check to make sure we have something
		if (!$language || !$translator->exists($language))
		{
			$lang = $this->app['config']->get('language', 'en-GB');

			if ($translator->exists($lang))
			{
				$language = $lang;
			}
		}

		if ($language)
		{
			$translator->setLanguage($language);
		}

		$boot = DS . 'bootstrap' . DS . $this->app['client']->name;

		$translator->load('lib_hubzero', PATH_APP . $boot, null, false, true) ||
		$translator->load('lib_hubzero', dirname(__DIR__), null, false, true);
	}
}
