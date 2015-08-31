<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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