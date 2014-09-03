<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Error
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class to assist in the process of benchmarking the execution
 * of sections of code to understand where time is being spent.
 *
 * @package     Joomla.Platform
 * @subpackage  Error
 * @since       11.1
 */
class JProfiler extends JObject
{
	/**
	 * @var    integer  The start time.
	 * @since  11.1
	 */
	protected $_start = 0;

	/**
	 * @var    string  The prefix to use in the output
	 * @since  11.1
	 */
	protected $_prefix = '';

	/**
	 * @var    array  The buffer of profiling messages.
	 * @since  11.1
	 */
	protected $_buffer = null;

	/**
	 * @var    float
	 * @since  11.1
	 */
	protected $_previous_time = 0.0;

	/**
	 * @var    float
	 * @since  11.1
	 */
	protected $_previous_mem = 0.0;

	/**
	 * @var    boolean  Boolean if the OS is Windows.
	 * @since  11.1
	 */
	protected $_iswin = false;

	/**
	 * @var    array  JProfiler instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   string  $prefix  Prefix for mark messages
	 *
	 * @since  11.1
	 */
	public function __construct($prefix = '')
	{
		$this->_start = $this->getmicrotime();
		$this->_prefix = $prefix;
		$this->_buffer = array();
		$this->_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
	}

	/**
	 * Returns the global Profiler object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $prefix  Prefix used to distinguish profiler objects.
	 *
	 * @return  JProfiler  The Profiler object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($prefix = '')
	{
		if (empty(self::$instances[$prefix]))
		{
			self::$instances[$prefix] = new JProfiler($prefix);
		}

		return self::$instances[$prefix];
	}

	/**
	 * Output a time mark
	 *
	 * The mark is returned as text enclosed in <div> tags
	 * with a CSS class of 'profiler'.
	 *
	 * @param   string  $label  A label for the time mark
	 *
	 * @return  string  Mark enclosed in <div> tags
	 *
	 * @since   11.1
	 */
	public function mark($label)
	{
		$current = self::getmicrotime() - $this->_start;
		if (function_exists('memory_get_usage'))
		{
			$current_mem = memory_get_usage() / 1048576;
			$mark = sprintf(
				'<code>%s %.3f seconds (<span class="tm">+%.3f</span>); %0.2f MB (<span class="mmry">%s%0.3f</span>) - <span class="msg">%s</span></code>',
				$this->_prefix,
				$current,
				$current - $this->_previous_time,
				$current_mem,
				($current_mem > $this->_previous_mem) ? '+' : '', $current_mem - $this->_previous_mem,
				$label
			);
		}
		else
		{
			$mark = sprintf('<code>%s %.3f seconds (<span class="tm">+%.3f</span>) - <span class="msg">%s</span></code>', $this->_prefix, $current, $current - $this->_previous_time, $label);
		}

		$this->_previous_time = $current;
		$this->_previous_mem = $current_mem;
		$this->_buffer[] = $mark;

		return $mark;
	}

	/**
	 * Get the current time.
	 *
	 * @return  float The current time
	 *
	 * @since   11.1
	 */
	public static function getmicrotime()
	{
		list ($usec, $sec) = explode(' ', microtime());

		return ((float) $usec + (float) $sec);
	}

	/**
	 * Get information about current memory usage.
	 *
	 * @return  integer  The memory usage
	 *
	 * @link    PHP_MANUAL#memory_get_usage
	 * @since   11.1
	 */
	public function getMemory()
	{
		if (function_exists('memory_get_usage'))
		{
			return memory_get_usage();
		}
		else
		{
			// Initialise variables.
			$output = array();
			$pid = getmypid();

			if ($this->_iswin)
			{
				// Windows workaround
				@exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
				if (!isset($output[5]))
				{
					$output[5] = null;
				}
				return substr($output[5], strpos($output[5], ':') + 1);
			}
			else
			{
				@exec("ps -o rss -p $pid", $output);
				return $output[1] * 1024;
			}
		}
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.  Marks are strings as per {@link JProfiler::mark()}.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function getBuffer()
	{
		return $this->_buffer;
	}

	public function log()
	{
		// <timstamp> <hubname> <ip-address> <app> <url> <query> <memory> <querycount> <timeinqueries> <totaltime>

		// This method is only called once per request so we don't need to
		// seperate logger instance creation from its use

		$logger = new \Hubzero\Log\Writer(
				new \Monolog\Logger(\JFactory::getConfig()->getValue('config.application_env')), 
				\JDispatcher::getInstance()
			);

		$path = \JFactory::getConfig()->getValue('config.log_path');

		if (is_dir('/var/log/hubzero-cms'))
		{
			$path = '/var/log/hubzero-cms';
		}

		$logger->useFiles($path . '/cmsprofile.log', 'info', "%datetime% %message%\n", "Y-m-d\TH:i:s.uP");

		$hubname = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'unknown';
		$uri = JURI::getInstance()->getPath();
		$uri = strtr($uri, array(" "=>"%20"));
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
		$query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'unknown'; 
		$memory = memory_get_usage(true);
		$db = \JFactory::getDBO();
		$querycount = $db->getCount();
		$querytime = $db->timer;
		$client = \JApplicationHelper::getClientInfo(\JFactory::getApplication()->getClientId())->name;
		$time = microtime(true) - $this->_start;

		$logger->info("$hubname $ip $client $uri [$query] $memory $querycount $querytime $time");
	}}
