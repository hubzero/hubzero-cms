<?php 

namespace Hubzero\Log;

use JDispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Writer 
{
	/**
	 * The Monolog logger instance.
	 *
	 * @var \Monolog\Logger
	 */
	protected $monolog;

	/**
	 * All of the error levels.
	 *
	 * @var array
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
	 * @var \Hubzero\Events\Dispacher
	 */
	protected $dispatcher;

	/**
	 * Create a new log writer instance.
	 *
	 * @param  \Monolog\Logger  $monolog
	 * @param  \Hubzero\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(MonologLogger $monolog, JDispatcher $dispatcher = null)
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
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
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
	 * @param  string  $path
	 * @param  string  $level
	 * @param  string  $format
	 * @return void
	 */
	public function useFiles($path, $level = 'debug', $format='')
	{
		$level = $this->parseLevel($level);

		$handler = new StreamHandler($path, $level);
		if ($format)
		{
			$handler->setFormatter(new LineFormatter($format, 'Y-m-d H:i:s'));
		}

		$this->monolog->pushHandler($handler);
	}

	/**
	 * Register a daily file log handler.
	 *
	 * @param  string  $path
	 * @param  int     $days
	 * @param  string  $level
	 * @param  string  $format
	 * @return void
	 */
	public function useDailyFiles($path, $days = 0, $level = 'debug', $format='')
	{
		$level = $this->parseLevel($level);

		$handler = new RotatingFileHandler($path, $days, $level);
		if ($format)
		{
			$handler->setFormatter(new LineFormatter($format, 'Y-m-d H:i:s'));
		}

		$this->monolog->pushHandler($handler);
	}

	/**
	 * Parse the string level into a Monolog constant.
	 *
	 * @param  string  $level
	 * @return int
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
				throw new \InvalidArgumentException(\JText::_('Invalid log level.'));
		}
	}

	/**
	 * Register a new callback handler for when
	 * a log event is triggered.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function listen($handler)
	{
		if (!isset($this->dispatcher))
		{
			throw new \RuntimeException(\JText::_('Events dispatcher has not been set.'));
		}

		$this->dispatcher->register('onLog', $handler);
	}

	/**
	 * Get the underlying Monolog instance.
	 *
	 * @return \Monolog\Logger
	 */
	public function getMonolog()
	{
		return $this->monolog;
	}

	/**
	 * Get the event dispatcher instance.
	 *
	 * @return \Hubzero\Events\Dispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Hubzero\Events\Dispatcher
	 * @return void
	 */
	public function setEventDispatcher(JDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Fires a log event.
	 *
	 * @param  string  $level
	 * @param  string  $message
	 * @param  array   $context
	 * @return void
	 */
	protected function triggerLogEvent($level, $message, array $context = array())
	{
		// If the event dispatcher is set, we will pass along the parameters to the
		// log listeners. These are useful for building profilers or other tools
		// that aggregate all of the log messages for a given "request" cycle.
		if (isset($this->dispatcher))
		{
			\JPluginHelper::importPlugin('system');
			$this->dispatcher->trigger('onLog', array($level, $message, $context));
		}
	}

	/**
	 * Dynamically handle error additions.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, $this->levels))
		{
			call_user_func_array(array($this, 'triggerLogEvent'), array_merge(array($method), $parameters));

			$method = 'add' . ucfirst($method);

			return $this->callMonolog($method, $parameters);
		}

		throw new \BadMethodCallException(\JText::sprintf('Method [%s] does not exist.', $method));
	}
}
