<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\RateLimit\Storage;

/**
 * Database rate limit storage class
 */
class Database implements StorageInterface
{
	/**
	 * Database object
	 *
	 * @var  object
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
	 * @param   int   $applicationId  Application id
	 * @param   int   $userId         User identifier
	 * @return  void
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
	 * @param   int   $id         Rate limit record id
	 * @param   int   $increment  Increment amount
	 * @return  void
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
	 * @param   int     $id       Rate limit record id
	 * @param   int     $toCount  Reset count
	 * @param   string  $toDate   Reset date string
	 * @return  void
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
	 * @param   int     $id       Rate limit record id
	 * @param   int     $toCount  Reset count
	 * @param   string  $toDate   Reset date string
	 * @return  void
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
