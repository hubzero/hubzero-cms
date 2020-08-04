<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Log;

use Hubzero\Events\DispatcherInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Class for writing logs
 */
class Writer
{
	/**
	 * The Monolog logger instance.
	 *
	 * @var  object
	 */
	protected $monolog = null;

	/**
	 * All of the error levels.
	 *
	 * @var  array
	 */
	protected $levels = array(
		'debug',
		'info',
		'notice',
		'warning',
		'error',
		'critical',
		'alert',
		'emergency',
	);

	/**
	 * The event dispatcher instance.
	 *
	 * @var  object
	 */
	protected $dispatcher = null;

	/**
	 * Create a new log writer instance.
	 *
	 * @param   object  $monolog
	 * @param   object  $dispatcher
	 * @return  void
	 */
	public function __construct(MonologLogger $monolog, DispatcherInterface $dispatcher = null)
	{
		$this->monolog = $monolog;

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}
	}

	/**
	 * Call Monolog with the given method and parameters.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	protected function callMonolog($method, $parameters)
	{
		if (is_array($parameters[0]))
		{
			$parameters[0] = json_encode($parameters[0]);
		}

		return call_user_func_array(array($this->monolog, $method), $parameters);
	}

	/**
	 * Register a file log handler.
	 *
	 * @param   string  $path
	 * @param   string  $level
	 * @param   string  $format
	 * @return  void
	 */
	public function useFiles($path, $level = 'debug', $format='', $dateFormat = 'Y-m-d H:i:s', $permissions=null)
	{
		$level = $this->parseLevel($level);

		$handler = new StreamHandler($path, $level, true, $permissions);
		if ($format)
		{
			$handler->setFormatter(new LineFormatter($format, $dateFormat));
		}

		$this->monolog->pushHandler($handler);
	}

	/**
	 * Register a daily file log handler.
	 *
	 * @param   string  $path
	 * @param   int     $days
	 * @param   string  $level
	 * @param   string  $format
	 * @return  void
	 */
	public function useDailyFiles($path, $days = 0, $level = 'debug', $format='', $dateFormat = 'Y-m-d H:i:s', $permissions=null)
	{
		$level = $this->parseLevel($level);

		$handler = new RotatingFileHandler($path, $days, $level, true, $permissions);
		if ($format)
		{
			$handler->setFormatter(new LineFormatter($format, $dateFormat));
		}

		$this->monolog->pushHandler($handler);
	}

	/**
	 * Parse the string level into a Monolog constant.
	 *
	 * @param   string  $level
	 * @return  int
	 */
	protected function parseLevel($level)
	{
		switch (strtolower($level))
		{
			case 'debug':
				return MonologLogger::DEBUG;

			case 'info':
				return MonologLogger::INFO;

			case 'notice':
				return MonologLogger::NOTICE;

			case 'warning':
				return MonologLogger::WARNING;

			case 'error':
				return MonologLogger::ERROR;

			case 'critical':
				return MonologLogger::CRITICAL;

			case 'alert':
				return MonologLogger::ALERT;

			case 'emergency':
				return MonologLogger::EMERGENCY;

			default:
				throw new \InvalidArgumentException('Invalid log level.');
		}
	}

	/**
	 * Register a new callback handler for when
	 * a log event is triggered.
	 *
	 * @param   object  $handler  Closure
	 * @return  void
	 */
	public function listen($handler)
	{
		if (!($this->dispatcher instanceof DispatcherInterface))
		{
			throw new \RuntimeException('Event dispatcher has not been set.');
		}

		$this->dispatcher->addListener($handler, 'onLog');
	}

	/**
	 * Get the underlying Monolog instance.
	 *
	 * @return  object
	 */
	public function getMonolog()
	{
		return $this->monolog;
	}

	/**
	 * Get the event dispatcher instance.
	 *
	 * @return  mixed  object or null
	 */
	public function getEventDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param   object  $dispatcher
	 * @return  void
	 */
	public function setEventDispatcher($dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Fires a log event.
	 *
	 * @param   string  $level
	 * @param   string  $message
	 * @param   array   $context
	 * @return  void
	 */
	protected function triggerLogEvent($level, $message, array $context = array())
	{
		// If the event dispatcher is set, we will pass along the parameters to the
		// log listeners. These are useful for building profilers or other tools
		// that aggregate all of the log messages for a given "request" cycle.
		if ($this->dispatcher instanceof DispatcherInterface)
		{
			$this->dispatcher->trigger('onLog', array($level, $message, $context));
		}
	}

	/**
	 * Dynamically handle error additions.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, $this->levels))
		{
			call_user_func_array(array($this, 'triggerLogEvent'), array_merge(array($method), $parameters));

			$method = 'add' . ucfirst($method);

			return $this->callMonolog($method, $parameters);
		}

		throw new \BadMethodCallException(sprintf('Method [%s] does not exist.', $method));
	}
}
