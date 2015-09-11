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

namespace Hubzero\Api\RateLimit\Storage;

/**
 * Rate limit storage contract
 */
interface StorageInterface
{
	/**
	 * Get record by application & user is
	 * 
	 * @param   int   $applicationId  Application id
	 * @param   int   $userId         User identifier
	 * @return  void
	 */
	public function getRateLimitData($applicationId, $userId);

	/**
	 * Create initial rate limit record
	 * 
	 * @param   int     $applicationId  Application id
	 * @param   int     $userId         User identifier
	 * @param   string  $ip             IP address
	 * @param   int     $limitShort     Short limit
	 * @param   int     $limitLong      Long limit
	 * @param   int     $countShort     Short count
	 * @param   int     $countLong      Long count
	 * @param   string  $expiresShort   Short expiration date string
	 * @param   string  $expiresLong    Long expiration date string
	 * @param   string  $created        Created date string
	 * @return  void
	 */
	public function createRateLimitData($applicationId, $userId, $ip, $limitShort, $limitLong, $countShort, $countLong, $expiresShort, $expiresLong, $created);

	/**
	 * Increment rate limit record
	 * 
	 * @param   int   $id         Rate limit record id
	 * @param   int   $increment  Increment amount
	 * @return  void
	 */
	public function incrementRateLimitData($id, $increment = 1);

	/**
	 * Reset short count & expiration
	 * 
	 * @param   int     $id       Rate limit record id
	 * @param   int     $toCount  Reset count
	 * @param   string  $toDate   Reset date string
	 * @return  void
	 */
	public function resetShort($id, $toCount, $toDate);

	/**
	 * Reset long count & expiration
	 * 
	 * @param   int     $id       Rate limit record id
	 * @param   int     $toCount  Reset count
	 * @param   string  $toDate   Reset date string
	 * @return  void
	 */
	public function resetLong($id, $toCount, $toDate);
}