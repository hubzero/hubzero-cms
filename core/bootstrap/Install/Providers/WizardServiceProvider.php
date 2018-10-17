<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			//return $response;
		}

		$referrer = $request->getVar('REQUEST_URI', '', 'server');

		if ($request->method() == 'POST' && \Hubzero\Utility\Uri::isInternal($referrer))
		{
			$data = $request->getVar('database', array(), 'post');

			if (!empty($data))
			{
				// Load the defualt config from core
				$config = new \Hubzero\Config\Repository(
					'site',
					new \Hubzero\Config\FileLoader(dirname(__DIR__) . DS . 'config')
				);

				// Apply the POSTed values
				foreach ($data as $key => $value)
				{
					$config->set('database.' . $key, $value);
				}

				$data = $config->toArray();
				$path = PATH_APP . DS . 'config';
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
						if (!$writer->write($values, $group, $client))
						{
							$this->setError(sprintf(
								'Failed to write configuration file <code>%s</code>.',
								$path . DS . ($client ? $client . DS : '') . $group . '.' . $format)
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
