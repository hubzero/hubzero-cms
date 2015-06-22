<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Language;

use Hubzero\Base\ServiceProvider;

/**
 * Language translation service provider
 */
class TranslationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['language'] = function($app)
		{
			$locale = $app['config']->get('locale', 'en-GB');
			$debug  = $app['config']->get('debug_lang', false);

			return new Translator($locale, $debug, $app['client']->name);
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
		if (!$language && $this->app->has('request') && $this->app->isSite())
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
			$lang = \User::getParam($this->app['client']->alias . '_language');

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect browser language
		if (!$language && $this->app->has('browser') && $this->app->isSite())
		{
			$lang = $translator->detectLanguage();

			if ($lang && $translator->exists($lang))
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

		$boot = DS . 'bootstrap' . DS . \App::get('client')->name;

		$translator->load('lib_joomla', PATH_APP . DS . 'app' . $boot, null, false, true) ||
		$translator->load('lib_joomla', PATH_CORE . $boot, null, false, true);
	}
}