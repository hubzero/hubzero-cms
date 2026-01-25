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

	private function _loglevel($error_level)
	{

		switch($error_level)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				return("error");

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_DEPRECATED:
			case E_USER_WARNING:
			case E_USER_DEPRECATED:
				return("warning");

			case E_NOTICE:
				return("notice");

		}
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
		$trace = (new \Exception())->getTraceAsString();
		$trace = "    " . str_replace("\n","\n    ",$trace);

		$loglevel = $this->_loglevel($level);

		$url = "    [";
		$url .=  isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] . "://" : '<null>://';
		$url .=  isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '<null>';
		$url .=  isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/<null>';
		$url .= "]";

		$logmsg = "$message in $file on line $line\n" . $url . "\n" . $trace;

		if (is_object($this->logger))
		{
			$this->logger->log($loglevel, $logmsg);
		}
		else
		{
			error_log(" cms.$loglevel " . $logmsg);
		}

		if (error_reporting() & $level)
		{
			$exception = new ErrorException($message, 0, $level, $file, $line);

			$this->renderer->render($exception);

			exit; // failsafe, render() should exit
		}

		return true;
	}

	/**
	 * Handle an uncaught exception.
	 *
	 * @param   object  $exception
	 * @return  void
	 */
	public function handleException($exception)
	{
		
		if ($exception->getCode() < 400 || $exception->getCode() > 499)
		{
			$trace = $exception->getTraceAsString();
			$trace = "    " . str_replace("\n","\n    ",$trace);

			$loglevel = 'error';

			$url = "    [";
			$url .=  isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] . "://" : '<null>://';
			$url .=  isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '<null>';
			$url .=  isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/<null>';
			$url .= "]";

			$logmsg = "Uncaught " . get_class($exception) . ": " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n" . $url . "\n" . $trace;

			if (is_object($this->logger))
			{
				$this->logger->log($loglevel, $logmsg);
			}
			else
			{
				error_log(" cms.$loglevel " . $logmsg);
			}
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
