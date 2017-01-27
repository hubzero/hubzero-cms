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
 * @package	  hubzero-cms
 * @author	  Dwight McKay <mckay@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Helpers;

use Exception;
use App;
use stdClass;

require_once "Predis/Autoloader.php";

/**
 * Usage verb class
 */

class Verb
{
	/**
	 * Submit a Celery job via direct job request insertion into Redis.
	 *
	 * @return	int
	 */

	public static function celerySubmit($params)
	{
		# this function does NO checking on inputs. Good luck!

		# grab the arguments. this function has variable arguments.
		# it is assumed that the first argument is the metrics function Celery is to
		# execute, while the remaining arguments are assumed to be integer arguments
		# to be passed to the metrics Celery function.

		\Predis\Autoloader::register();

		$args2pass = array_slice($params, 1);
		for ($i = 0; $i < count($args2pass); $i++) {
			$args2pass[$i] = (int) $args2pass[$i];
		}

		$task = array(
			"task" => "Metrics.metrics_base.{$params[0]}",
			"args" => $args2pass,
		);

		$task["id"] = sha1(json_encode($task["args"]).time());

		$bodyTask = array(
			"body" => base64_encode(json_encode($task)),
			"headers" => new stdClass,
			"content-type" => "application/json",
			"properties" => array(
				"body_encoding" => "base64",
				"delivery_info" => array(
					"priority" => 0,
					"routing_key" => "default",
					"exchange" => "default",
				),
				"delivery_tag" => sha1(base64_encode(json_encode($task)).time()),
				"delivery_mode" => 2,
			),
			"content-encoding" => "utf-8",
		);

	# new Predis\Client

		try {
			$redis = new \Predis\Client(array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"password" => \Config::get('redis_password'),
				"database" => 9,
			));
		}
		catch (Exception $e) {
			die($e->getMessage());
		}

# debugging...
#		 echo "celery-task-meta-".$task["id"]."\n";

		$redis->lPush('celery', json_encode($bodyTask));
	}

	public static function metricsVerb($params)
	{
		# grab the arguments. this function has variable arguments.
		# params should be an array in the same form that calling the function from the command line should yield
		# [verb, arg1, arg2, arg3, ...]

		# the first argument is assumed to be the metrics "verb" being requested.
		# the remaining argument are assumed to be the arguments to the "verb".

		\Predis\Autoloader::register();

		$verbName = $params[0];
		# ...and hookup with redis
		try {
			$redis = new \Predis\Client(array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"password" => \Config::get('redis_password'),
				"database" => 8,
			));
			}
		catch (Exception $e) {
			die($e->getMessage());
		}

		
		# pull in verb information, checking that verb is available and correct in the process
		$verb = $redis->hgetall($verbName);
		if (!$verb)
			return -500; # verb not found

		# pull in the remaining arguments
		if (count($params) != $verb['args']) {
			return -599; # incorrect number of arguments
		}

		# compose the query
		$query = "{$verbName}_";
		for ($i = 1; $i < count($params); $i++) {
			$query = $query."{$params[$i]}";
		}

		# Validation test - parameters
		$verbDate = new \Datetime("{$params[1]}-{$params[2]}-01");

		# Grab the list of databases needed by this verb and calculate the minimum date range
		# over which a calculation can be performed.

		$minDate = new \Datetime("1970-1-1");
		$maxDate = new \Datetime("3000-1-1");

		foreach (explode(',', $verb['databases']) as $db) {
			$dbInfo = $redis->hgetall($db);
			$dbMin = new \Datetime($dbInfo['minDate']);
			$dbMax = new \Datetime($dbInfo['maxDate']);
			if ($dbMin > $minDate)
				$minDate = $dbMin;
			if ($dbMax < $maxDate)
				$maxDate = $dbMax;
		};

		if (($verbDate < $minDate) or ($verbDate > $maxDate))
			return -501; # parameter out of range

		# Validation test - is there an answer for this query in the cache?
		# if not, kick off a backend job to calculate one.

		$result = $redis->hgetall($query);
		if (!$result) {
			self::celerySubmit($params);
			return -401; # result unavailable, calculating result now, come back later
		};

		# Validation test - software version
		# If the result's software version number does not match the verb's version number, re-compute

		if ($result['software'] != $verb['version']) {
			self::celerySubmit($params);
			return -402; # result out of date, calculating updated result now, come back later
		};

		# Validation test - database version
		# if any of the results' database version number(s) do not match the database(s)'s version number(s), re-compute

		foreach (explode(',', $verb['databases']) as $db) {
			$dbInfo = $redis->hgetall($db);
			if ($dbInfo['version'] != $result[$db]) {
				self::celerySubmit($params);
				return -402; # result out of date, calculating updated result now, come back later
			};
		};

		# It's all good. Increment the popularity counter and return the cached value.

		$redis->incr('pop_${query}');
		return $result['value'];
	}

	public static function getDateRange($verbName)
	{
		\Predis\Autoloader::register();

		# ...and hookup with redis
		try
		{
			$redis = new \Predis\Client(array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"password" => \Config::get('redis_password'),
				"database" => 8,
			));
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}

		
		# pull in verb information, checking that verb is available and correct in the process
		$verb = $redis->hgetall($verbName);
		if (!$verb)
			return -500; # verb not found

		# Grab the list of databases needed by this verb and calculate the minimum date range
		# over which a calculation can be performed.

		$minDate = new \Datetime("1970-1-1");
		$maxDate = new \Datetime("3000-1-1");

		foreach (explode(',', $verb['databases']) as $db)
		{
			$dbInfo = $redis->hgetall($db);
			$dbMin = new \Datetime($dbInfo['minDate']);
			$dbMax = new \Datetime($dbInfo['maxDate']);
			if ($dbMin > $minDate)
				$minDate = $dbMin;
			if ($dbMax < $maxDate)
				$maxDate = $dbMax;
		
		}
		return [$minDate, $maxDate];
	}

}
