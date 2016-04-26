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

use Hubzero\Document\Manager;
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
			$type = ($type == 'none' ? 'raw' : $type);

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

			if ($this->app['config']->get('offline')) // && !$user->authorise('core.login.offline'))
			{
				$file = 'offline';

				$response->headers->set('Status', '503 Service Temporarily Unavailable', 'true');
			}

			$params = array(
				'template'  => $this->app['template']->template,
				'file'      => $file . '.php',
				'directory' => dirname($this->app['template']->path),
				'params'    => $this->app['template']->params
			);
			$params['baseurl'] = rtrim(\Request::root(true), '/') . rtrim(substr(dirname($params['directory']), strlen(PATH_ROOT)), '/');
		}

		if (!$document->getTitle())
		{
			$document->setTitle($this->app['config']->get('sitename'));
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

		$document->setBase(htmlspecialchars($request->current()));

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
