<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Toolbar service provider
 */
class DocumentServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['document'] = function($app)
		{
			$raw  = $app['request']->getBool('no_html');
			$type = $app['request']->getWord('format', $raw ? 'raw' : 'html');

			$options = array(
				'charset'   => 'utf-8',
				'lineend'   => 'unix',
				'tab'       => "\t",
				'language'  => $app['language']->getTag(),
				'direction' => $app['language']->isRTL() ? 'rtl' : 'ltr'
			);

			$manager = new Manager();
			$manager->setType($type)
					->setLanguage($options['language'])
					->setCharset($options['charset'])
					->setDirection($options['direction'])
					->setTab($options['tab'])
					->setLineEnd($options['lineend']);

			return $manager;
		};
	}

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		$document = $this->app['document'];

		$params = array();

		// Set meta tags
		if ($document->getType() == 'html')
		{
			if (!$document->getMetaData('keywords'))
			{
				$document->setMetaData('keywords', $this->app['config']->get('MetaKeys'));
			}
			$document->setMetaData('rights', $this->app['config']->get('MetaRights'));

			$file = $request->getCmd('tmpl', 'index');
			if (!$this->app['config']->get('offline') && $file == 'offline')
			{
				$file = 'index';
			}

			if (!$this->app->isAdmin() && $this->app['config']->get('offline')) // && !$user->authorise('core.login.offline'))
			{
				$file = 'offline';

				$response->headers->set('Status', '503 Service Temporarily Unavailable', 'true');
			}

			$params = array(
				'template'  => $this->app['template']->template,
				'file'      => $file . '.php',
				'directory' => ($this->app['template']->protected ? PATH_CORE : PATH_APP) . DS . 'templates',
				'params'    => $this->app['template']->params
			);
			$params['baseurl'] = rtrim(\Request::root(true), '/') . rtrim(substr(dirname($params['directory']), strlen(PATH_ROOT)), '/');
		}

		if (!$document->getTitle())
		{
			$document->setTitle($this->app['config']->get('sitename'));
		}
		if ($this->app->isAdmin())
		{
			$document->setTitle($this->app['config']->get('sitename') . ' - ' . $this->app['language']->txt('JADMINISTRATION'));
		}

		if (!$document->getDescription())
		{
			$document->setDescription($this->app['config']->get('MetaDesc'));
		}

		/*
		// Get language
		$lang_code = Lang::getTag();
		$languages = Lang::getLanguages('lang_code');

		// Set metadata
		if (isset($languages[$lang_code]) && $languages[$lang_code]->metakey)
		{
			$document->setMetaData('keywords', $languages[$lang_code]->metakey);
		}
		else
		{
			$document->setMetaData('keywords', $this->app['config']->get('MetaKeys'));
		}

		$document->setTitle($params->get('page_title'));
		$document->setDescription($params->get('page_description'));
		*/

		if ($this->app['config']->get('MetaVersion', 0))
		{
			$document->setGenerator($document->getGenerator() . ' (' . $this->app->version() . ')');
		}

		if ($this->app->isSite())
		{
			$document->setBase(htmlspecialchars($request->current()));
		}

		$document->setBuffer($response->getContent(), 'component');
		$document->parse($params);

		$caching = false;
		if ($this->app['config']->get('caching', 2) == 2 && !\User::get('id'))
		{
			$caching = true;
		}
		$this->app['dispatcher']->trigger('system.onBeforeRender');

		$response->setContent($document->render($caching, $params));

		$this->app['dispatcher']->trigger('system.onAfterRender');

		if ($profiler = $this->app['profiler'])
		{
			$profiler->mark('onAfterRender');
		}

		return $response;
	}
}