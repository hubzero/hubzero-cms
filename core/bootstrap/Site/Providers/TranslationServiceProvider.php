<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

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
		$path = PATH_APP . DS . 'bootstrap' . DS . strtolower($this->app['client']->name);

		// If a language was specified it has priority
		if (!$language && $this->app->has('request'))
		{
			$lang = $this->app['request']->getString('language', null);

			if ($lang && $translator->exists($lang, $path))
			{
				$language = $lang;
			}
		}

		// Detect cookie language
		if (!$language) // && $this->app->has('language.filter'))
		{
			$lang = $this->app['request']->getString($this->app->hash('language'), null, 'cookie');

			if ($lang && $translator->exists($lang, $path))
			{
				$language = $lang;
			}
		}

		// Detect user specified language
		if (!$language && $this->app->has('user'))
		{
			$lang = \User::getParam($this->app['client']->alias . '_language');

			if ($lang && $translator->exists($lang, $path))
			{
				$language = $lang;
			}
		}

		// Detect browser language
		if (!$language && $this->app->has('browser'))
		{
			$lang = $translator->detectLanguage();

			if ($lang && $translator->exists($lang, $path))
			{
				$language = $lang;
			}
		}

		// Detect default language
		if (!$language && $this->app->has('component'))
		{
			$params = $this->app['component']->params('com_languages');

			$language = $params->get(
				$this->app['client']->name,
				$this->app['config']->get('language', 'en-GB')
			);
		}

		// One last check to make sure we have something
		if (!$language || !$translator->exists($language, $path))
		{
			$lang = $this->app['config']->get('language', 'en-GB');

			if ($translator->exists($lang, $path))
			{
				$language = $lang;
			}
		}

		if ($language)
		{
			$translator->setLanguage($language);
		}

		$translator->load('lib_joomla', $path, null, false, true) ||
		$translator->load('lib_joomla', PATH_CORE . DS . 'bootstrap' . DS . ucfirst($this->app['client']->name), null, false, true);
	}
}
