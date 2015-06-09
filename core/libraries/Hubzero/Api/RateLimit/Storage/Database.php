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

use JFactory;

/**
 * Mysql rate limit storage class
 */
class Database implements StorageInterface
{
	/**
	 * Database object
	 * 
	 * @var object
	 */
	private $db;

	/**
	 * Create new storage object
	 *
	 * @param   object  $db
	 * @return  void
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Get record by application & user is
	 * 
	 * @param  int     $applicationId  Application id
	 * @param  int     $userId         User identifier
	 * @return void
	 */
	public function getRateLimitData($applicationId, $userId)
	{
		$sql = "SELECT * FROM `#__developer_rate_limit` 
				WHERE `application_id` = " . $this->db->quote($applicationId) . "
				AND `uidNumber` = " . $this->db->quote($userId) . "
				ORDER BY `created` LIMIT 1";
		$this->db->setQuery($sql);
		return $this->db->loadObject();
	}

	/**
	 * Create initial rate limit record
	 * 
	 * @param  int     $applicationId  Application id
	 * @param  int     $userId         User identifier
	 * @param  string  $ip             IP address
	 * @param  int     $limitShort     Short limit
	 * @param  int     $limitLong      Long limit
	 * @param  int     $countShort     Short count
	 * @param  int     $countLong      Long count
	 * @param  string  $expiresShort   Short expiration date string
	 * @param  string  $expiresLong    Long expiration date string
	 * @param  string  $created        Created date string
	 * @return void
	 */
	public function createRateLimitData($applicationId, $userId, $ip, $limitShort, $limitLong, $countShort, $countLong, $expiresShort, $expiresLong, $created)
	{
		$sql = "INSERT INTO `#__developer_rate_limit` (`application_id`, `uidNumber`, `ip`, `limit_short`, `limit_long`, `count_short`, `count_long`, `expires_short`, `expires_long`, `created`)
				VALUES (" . $this->db->quote($applicationId) . ", " . $this->db->quote($userId) . ", " . $this->db->quote($ip) . ", " . $this->db->quote($limitShort) . ", " . $this->db->quote($limitLong) . ", " . $this->db->quote($countShort) . ", " . $this->db->quote($countLong) . ", " . $this->db->quote($expiresShort) . ", " . $this->db->quote($expiresLong) . ", " . $this->db->quote($created) . ")";
		$this->db->setQuery($sql);
		$this->db->query();

		return $this->getRateLimitData($applicationId, $userId);
	}

	/**
	 * Increment rate limit record
	 * 
	 * @param  int  $id         Rate limit record id
	 * @param  int  $increment  Increment amount
	 * @return void
	 */
	public function incrementRateLimitData($id, $increment = 1)
	{
		$sql = "UPDATE `#__developer_rate_limit`
				SET `count_short` = `count_short` + " . $this->db->quote($increment) . ",
				`count_long` = `count_long` + " . $this->db->quote($increment) . "
				WHERE `id` = " . $this->db->quote($id);
		$this->db->setQuery($sql);
		return $this->db->query();
	}

	/**
	 * Reset short count & expiration
	 * 
	 * @param  int     $id       Rate limit record id
	 * @param  int     $toCount  Reset count
	 * @param  string  $toDate   Reset date string
	 * @return void
	 */
	public function resetShort($id, $toCount, $toDate)
	{
		$sql = "UPDATE `#__developer_rate_limit`
				SET `count_short` = " . $this->db->quote($toCount) . ",
				`expires_short` = " . $this->db->quote($toDate) . "
				WHERE `id` = " . $this->db->quote($id);
		$this->db->setQuery($sql);
		return $this->db->query();
	}

	/**
	 * Reset long count & expiration
	 * 
	 * @param  int     $id       Rate limit record id
	 * @param  int     $toCount  Reset count
	 * @param  string  $toDate   Reset date string
	 * @return void
	 */
	public function resetLong($id, $toCount, $toDate)
	{
		$sql = "UPDATE `#__developer_rate_limit`
				SET `count_long` = " . $this->db->quote($toCount) . ",
				`expires_long` = " . $this->db->quote($toDate) . "
				WHERE `id` = " . $this->db->quote($id);
		$this->db->setQuery($sql);
		return $this->db->query();
	}
}