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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug;

use Hubzero\Base\Object;
use Hubzero\Debug\Profile\Mark;
use Hubzero\Log\Writer;
use Monolog\Logger as Monolog;

/**
 * Utility class to assist in the process of benchmarking the execution
 * of sections of code to understand where time is being spent.
 */
class Profiler extends Object
{
	/**
	 * The start time.
	 *
	 * @var  integer
	 */
	protected $started = 0;

	/**
	 * The start memory.
	 *
	 * @var  integer
	 */
	protected $memory = 0;

	/**
	 * The prefix to use in the output
	 *
	 * @var  string
	 */
	protected $prefix = '';

	/**
	 * The buffer of profiling messages.
	 *
	 * @var  array
	 */
	protected $events = array();

	/**
	 * Constructor
	 *
	 * @param   string  $prefix  Prefix for mark messages
	 * @return  void
	 */
	public function __construct($prefix = '')
	{
		$this->reset();

		$this->prefix  = $prefix;
	}

	/**
	 * Reset the profiler
	 *
	 * @return  void
	 */
	public function reset()
	{
		$this->started  = $this->now();
		$this->prefix   = '';
		$this->marks    = array();
		$this->memory   = memory_get_usage(true);
	}

	/**
	 * Get the prefix
	 *
	 * @return  string
	 */
	public function label()
	{
		return $this->prefix;
	}

	/**
	 * Output a time mark
	 *
	 * The mark is returned as text enclosed in <div> tags
	 * with a CSS class of 'profiler'.
	 *
	 * @param   string  $label  A label for the time mark
	 * @return  string  Mark enclosed in <div> tags
	 */
	public function mark($label)
	{
		$this->marks[] = new Mark($label, $this->ended(), $this->now());

		return $this;
	}

	/**
	 * Get the current time.
	 *
	 * @return  float  The current time
	 */
	public function now()
	{
		return microtime(true);
	}

	/**
	 * Gets the relative time of the start of the first period.
	 *
	 * @return  int  The time (in milliseconds)
	 */
	public function started()
	{
		return isset($this->marks[0]) ? $this->marks[0]->started() : $this->started;
	}

	/**
	 * Gets the relative time of the end of the last period.
	 *
	 * @return  int  The time (in milliseconds)
	 */
	public function ended()
	{
		$count = count($this->marks);

		return $count ? $this->marks[$count - 1]->ended() : $this->started;
	}

	/**
	 * Gets the duration of the events (including all periods).
	 *
	 * @return  int  The duration (in milliseconds)
	 */
	public function duration()
	{
		$total = 0;

		foreach ($this->marks as $mark)
		{
			$total += $mark->duration();
		}

		return $total;
	}

	/**
	 * Gets the max memory usage of all periods.
	 *
	 * @return  int  The memory usage (in bytes)
	 */
	public function memory()
	{
		$memory = $this->memory;

		foreach ($this->marks as $mark)
		{
			if ($mark->memory() > $memory)
			{
				$memory = $mark->memory();
			}
		}

		return $memory;
	}

	/**
	 * Returns a summary of all timer activity so far
	 *
	 * @return  array
	 */
	public function marks()
	{
		return $this->marks;
	}

	/**
	 * Returns a summary of all timer activity so far
	 *
	 * @return  array
	 */
	public function summary()
	{
		$summary = array(
			'start'   => $this->started(),
			'end'     => $this->ended(),
			'total'   => $this->duration(),
			'memory'  => $this->memory()
		);

		return $summary;
	}

	/**
	 * Log profiler info
	 *
	 * @return  void
	 */
	public function log()
	{
		// <timstamp> <hubname> <ip-address> <app> <url> <query> <memory> <querycount> <timeinqueries> <totaltime>

		// This method is only called once per request so we don't need to
		// seperate logger instance creation from its use

		$logger = new Writer(
			new Monolog(\App::get('config')->get('application_env')),
			\App::get('dispatcher')
		);

		$path = \App::get('config')->get('log_path');

		if (is_dir('/var/log/hubzero-cms'))
		{
			$path = '/var/log/hubzero-cms';
		}

		$logger->useFiles($path . DS . 'cmsprofile.log', 'info', "%datetime% %message%\n", "Y-m-d\TH:i:s.uP", 0640);

		$hubname    = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'unknown';
		$uri        = \Request::path();
		$uri        = strtr($uri, array(" "=>"%20"));
		$ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
		$query      = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'unknown';
		$memory     = memory_get_usage(true);
		$querycount = \App::get('db')->getCount();
		$querytime  = \App::get('db')->getTimer();
		$client     = \App::get('client')->name;
		$time       = microtime(true) - $this->started;

		$logger->info("$hubname $ip $client $uri [$query] $memory $querycount $querytime $time");

		// Now log post data if applicable
		if (\Request::method() == 'POST' && \App::get('config')->get('log_post_data', false))
		{
			$logger = new Writer(
				new Monolog(\App::get('config')->get('application_env')),
				\App::get('dispatcher')
			);

			$logger->useFiles($path . DS . 'cmspost.log', 'info', "%datetime% %message%\n", "Y-m-d\TH:i:s.uP", 0640);

			$post     = json_encode($_POST);
			$referrer = $_SERVER['HTTP_REFERER'];

			// Encrypt for some reasonable level of obscurity
			$key = md5(\App::get('config')->get('secret'));

			// Compute needed iv size and random iv
			$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
			$iv     = mcrypt_create_iv($ivSize, MCRYPT_RAND);

			$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $post, MCRYPT_MODE_CBC, $iv);

			// Prepend iv for decoding later
			$ciphertext = $iv . $ciphertext;

			// Encode the resulting cipher text so it can be represented by a string
			$ciphertextEncoded = base64_encode($ciphertext);

			$logger->info("$uri $referrer $ciphertextEncoded");
		}
	}
}