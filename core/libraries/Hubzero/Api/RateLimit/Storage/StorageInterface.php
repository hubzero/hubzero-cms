<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
