<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

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

			$params = array(
				'template'  => $this->app['template']->template,
				'file'      => $file . '.php',
				'directory' => dirname($this->app['template']->path),
				'params'    => $this->app['template']->params
			);

			// Path is <PATH>/[core|app]/templates
			$basepath = dirname(dirname($params['directory']));

			// Strip off the base path
			$path = substr(dirname($params['directory']), strlen($basepath));

			$params['baseurl'] = rtrim(\Request::root(true), '/') . rtrim($path, '/');
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

		if ($this->app['config']->get('MetaVersion', 0))
		{
			$document->setGenerator($document->getGenerator() . ' (' . $this->app->version() . ')');
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
