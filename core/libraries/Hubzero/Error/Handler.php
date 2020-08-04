<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * A logger to log errors.
	 *
	 * @var  object
	 */
	protected $logger;

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
	 * @param   object  $logger
	 * @return  void
	 */
	public function __construct(RendererInterface $renderer, $logger = null)
	{
		$this->logger   = $logger;
		$this->renderer = $renderer;
	}

	/**
	 * Set the logger for the handler.
	 *
	 * @param   object  $logger
	 * @return  object  Chainable.
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;

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

		if ($client != 'testing')
		{
			$this->registerShutdownHandler();
		}
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
		if (is_object($this->logger) && !in_array($exception->getCode(), [403, 404]))
		{
			$this->logger->log(
				'error',
				$exception->getMessage(),
				array(
					'code' => $exception->getCode(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine()
				)
			);
		}

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

			if (!in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE)))
			{
				return;
			}

			$this->handleException(new FatalError($message, $type, 0, $file, $line));
		}
	}
}
