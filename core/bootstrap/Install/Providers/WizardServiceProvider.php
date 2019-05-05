<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Providers;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Base\Traits\ErrorBag;

/**
 * Wiard service provider
 */
class WizardServiceProvider extends Middleware
{
	use ErrorBag;

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		if ($this->app['config']->get('dbtype')
		 && $this->app['config']->get('user')
		 && $this->app['config']->get('password'))
		{
			$this->app->redirect($this->app['request']->root());
		}

		$referrer = $request->getString('REQUEST_URI', '', 'server');

		if ($request->method() == 'POST' && \Hubzero\Utility\Uri::isInternal($referrer))
		{
			$data = $request->getArray('database', array(), 'post');

			if (!empty($data))
			{
				// Load the defualt config from core
				$config = new \Hubzero\Config\Repository(
					'site',
					new \Hubzero\Config\FileLoader(dirname(__DIR__) . '/config')
				);

				// Apply the POSTed values
				foreach ($data as $key => $value)
				{
					$config->set('database.' . $key, $value);
				}

				$data = $config->toArray();
				$path = PATH_APP . '/config';
				$format = 'php';

				if (!is_dir($path))
				{
					if (!@mkdir($path, 0750))
					{
						$this->setError(sprintf(
							'Failed to create <code>%s</code> directory.',
							$path
						));

						if (!is_writable(PATH_APP))
						{
							$this->setError(sprintf(
								'Path <code>%s</code> is not writable.',
								PATH_APP
							));
						}
					}
				}

				if (!is_writable($path))
				{
					$this->setError(sprintf(
						'Path <code>%s</code> is not writable.',
						$path
					));
				}

				if (!$this->getError())
				{
					// Attempt to write the configuration files
					$writer = new \Hubzero\Config\FileWriter(
						$format,
						$path
					);

					$client = null;

					foreach ($data as $group => $values)
					{
						if (isset($values['secret']))
						{
							$length = 30;
							$x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							$values['secret'] = substr(str_shuffle(str_repeat($x, ceil($length/strlen($x)))), 1, $length);
						}

						if (!$writer->write($values, $group, $client))
						{
							$this->setError(sprintf(
								'Failed to write configuration file <code>%s</code>.',
								$path . '/' . ($client ? $client . '/' : '') . $group . '.' . $format)
							);
						}
					}
				}

				if (!$this->getError())
				{
					$this->app->redirect($request->root());
				}
			}
		}

		$contents = 'Database not configured.';

		$path = dirname(__DIR__) . '/Wizard/index.php';

		ob_start();

		if (file_exists($path))
		{
			$errors = $this->getErrors();

			include_once $path;
		}

		$contents = ob_get_contents();
		ob_end_clean();

		$response->setContent($contents);

		return $response;
	}
}
