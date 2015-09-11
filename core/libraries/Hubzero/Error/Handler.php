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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error;

use Hubzero\Error\Exception\FatalErrorException as FatalError;
use ErrorException;

/**
 * Error Handling Class
 *
 * Inspired, in part, by Laravel's Exception Handler
 * http://laravel.com
 */
class Handler
{
	/**
	 * Indicates if the application is in debug mode.
	 *
	 * @var  bool
	 */
	protected $debug;

	/**
	 * The exception renderer.
	 *
	 * @var  object
	 */
	protected $renderer;

	/**
	 * Create a new error handler instance.
	 *
	 * @param   object  $renderer
	 * @param   bool    $debug
	 * @return  void
	 */
	public function __construct(RendererInterface $renderer, $debug = true)
	{
		$this->debug    = $debug;
		$this->renderer = $renderer;
	}

	/**
	 * Set the debug level for the handler.
	 *
	 * @param   bool    $debug
	 * @return  object  Chainable.
	 */
	public function setDebug($debug)
	{
		$this->debug = $debug;

		return $this;
	}

	/**
	 * Set the render,
	 *
	 * @param   object  $exception
	 * @return  object  Chainable.
	 */
	public function setRenderer(RendererInterface $renderer)
	{
		$this->renderer = $renderer;

		return $this;
	}

	/**
	 * Determine if we are running in the console.
	 *
	 * @return  bool
	 */
	public function runningInConsole()
	{
		return php_sapi_name() == 'cli';
	}

	/**
	 * Register the exception / error handlers for the application.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function register($client)
	{
		$this->registerErrorHandler();

		$this->registerExceptionHandler();

		if ($client != 'testing') $this->registerShutdownHandler();
	}

	/**
	 * Register the PHP error handler.
	 *
	 * @return void
	 */
	protected function registerErrorHandler()
	{
		set_error_handler(array($this, 'handleError'));
	}

	/**
	 * Register the PHP exception handler.
	 *
	 * @return void
	 */
	protected function registerExceptionHandler()
	{
		set_exception_handler(array($this, 'handleException'));
	}

	/**
	 * Register the PHP shutdown handler.
	 *
	 * @return void
	 */
	protected function registerShutdownHandler()
	{
		register_shutdown_function(array($this, 'handleShutdown'));
	}

	/**
	 * Handle a PHP error for the application.
	 *
	 * @param   int     $level
	 * @param   string  $message
	 * @param   string  $file
	 * @param   int     $line
	 * @param   array   $context
	 * @throws  \ErrorException
	 */
	public function handleError($level, $message, $file = '', $line = 0, $context = array())
	{
		if (error_reporting() & $level)
		{
			throw new ErrorException($message, 0, $level, $file, $line);
		}
	}

	/**
	 * Handle an uncaught exception.
	 *
	 * @param   object  $exception
	 * @return  void
	 */
	public function handleException($exception)
	{
		return $this->renderer->render($exception);
	}

	/**
	 * Handle the PHP shutdown event.
	 *
	 * @return  void
	 */
	public function handleShutdown()
	{
		$error = error_get_last();

		// If an error has occurred that has not been displayed, we will create a fatal
		// error exception instance and pass it into the regular exception handling
		// code so it can be displayed back out to the developer for information.
		if (!is_null($error))
		{
			extract($error);

			if (!in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) return;

			$this->handleException(new FatalError($message, $type, 0, $file, $line));
		}
	}
}
