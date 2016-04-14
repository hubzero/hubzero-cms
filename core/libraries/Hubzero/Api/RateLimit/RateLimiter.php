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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\RateLimit;

use Hubzero\Api\RateLimit\Storage\StorageInterface;
use Hubzero\Utility\Date;

/**
 * Rate Limiter
 */
class RateLimiter
{
	/**
	 * Storage object
	 * 
	 * @var  object
	 */
	private $storage;

	/**
	 * Configuration
	 * 
	 * @var  array
	 */
	private $config;

	/**
	 * New rate limiter instance
	 * 
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Options
	 * @return  void
	 */
	public function __construct(StorageInterface $storage, $config = [])
	{
		$this->storage = $storage;
		$this->config  = array_merge([
			'short' => [
				'period' => 1, // 1 minute
				'limit'  => 120 // 120 requests
			],
			'long' => [
				'period' => 1440, // 1 day (in minutes)
				'limit'  => 10000 // 10,000 requests
			]
		], $config);
	}

	/**
	 * Rate limit for application & user
	 * 
	 * @param   int    $applicationId  Application identifier
	 * @param   int    $userId         User identifier
	 * @return  array  Array of rate limit data
	 */
	public function rateLimit($applicationId, $userId)
	{
		// load limit data, creating initial record if doesnt exist
		if (!$data = $this->storage->getRateLimitData($applicationId, $userId))
		{
			$data = $this->createRateLimitData($applicationId, $userId);
		}

		// check if we can reset short expiration
		if (time() > with(new Date($data->expires_short))->toUnix())
		{
			$newShortDate = $this->getNewExpiresDateString('short');
			$this->storage->resetShort($data->id, 0, $newShortDate);
		}

		// check if we can reset long expiration
		if (time() > with(new Date($data->expires_long))->toUnix())
		{
			$newLongDate = $this->getNewExpiresDateString('long');
			$this->storage->resetLong($data->id, 0, $newLongDate);
		}

		// increment data then refetch
		$this->storage->incrementRateLimitData($data->id);

		// refetch record after incrementing
		$data = $this->storage->getRateLimitData($applicationId, $userId);

		// check to see if were over short or long limits
		$data->exceeded_short = false;
		$data->exceeded_long  = false;
		if ($data->count_short >= $data->limit_short)
		{
			$data->exceeded_short = true;
		}
		if ($data->count_long >= $data->limit_long)
		{
			$data->exceeded_long = true;
		}

		// return data
		return $data;
	}

	/**
	 * Create initial limit data
	 * 
	 * @param   int    $applicationId  Application identifier
	 * @param   int    $userId         User identifier
	 * @return  array  Array of rate limit data
	 */
	private function createRateLimitData($applicationId, $userId)
	{
		// data needed to create record
		$ipAddress    = \Request::ip();
		$countShort   = 0;
		$countLong    = 0;
		$limitShort   = $this->config['short']['limit'];
		$limitLong    = $this->config['long']['limit'];
		$created      = with(new Date('now'))->toSql();
		$expiresShort = $this->getNewExpiresDateString('short');
		$expiresLong  = $this->getNewExpiresDateString('long');

		// create initial limit record
		return $this->storage->createRateLimitData(
			$applicationId,
			$userId,
			$ipAddress,
			$limitShort,
			$limitLong,
			$countShort,
			$countLong,
			$expiresShort,
			$expiresLong,
			$created
		);
	}

	/**
	 * Get new expires date string
	 * 
	 * @param   string  $type  Short or long period
	 * @return  string  Date string
	 */
	private function getNewExpiresDateString($type = 'short')
	{
		$modifier = $this->config[$type]['period'];
		return with(new Date('now'))->modify('+' . $modifier . ' MINUTES')->toSql();
	}
}