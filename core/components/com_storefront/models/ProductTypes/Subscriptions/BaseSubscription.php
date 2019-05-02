<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class BaseSubscription
{
	/**
	 * Constructor
	 *
	 * @param   integer  $pId
	 * @param   integer  $uId
	 * @return  void
	 */
	public function __construct($pId, $uId)
	{
		$this->pId = $pId;
		$this->uId = $uId;
		$this->_db = App::get('db');
	}

	/**
	 * Get expiration info. Since this is a base class it gets the expiration from the default fallback place. Most
	 * subscription model products will override this method to get the info from the right place
	 *
	 * @return  Object
	 */
	public function getExpiration()
	{
		$now = Date::of('now')->toSql();

		$sql = 'SELECT `crtmExpires`,
				IF(`crtmExpires` < ' . $this->_db->quote($now) . ', 0, 1) AS `crtmActive`
				FROM `#__cart_memberships` m
				LEFT JOIN `#__cart_carts` c ON m.`crtId` = c.`crtId`
				WHERE m.`pId` = ' . $this->_db->quote($this->pId) . ' AND c.`uidNumber` = ' . $this->_db->quote($this->uId);
		$this->_db->setQuery($sql);

		$membershipInfo = $this->_db->loadAssoc();
		return $membershipInfo;
	}

	/**
	 * Set expiration date. Since this is a base class it sets the expiration in the default fallback place. Most
	 * subscription model products will override this method to set the info at the right place
	 *
	 * @param   string  $expires  Date/Time, Expiration time, SQL format
	 * @return 	void
	 */
	public function setExpiration($expires)
	{
		$sql = 'INSERT INTO `#__cart_memberships` SET
				`crtmExpires` = ' . $this->_db->quote($expires) . ',
				`pId` = ' . $this->_db->quote($this->pId) . ',
				`crtId` = (SELECT crtId FROM `#__cart_carts` WHERE `uidNumber` = ' . $this->_db->quote($this->uId) . ')
				ON DUPLICATE KEY UPDATE
				`crtmExpires` = ' . $this->_db->quote($expires) . '';

		$this->_db->setQuery($sql);
		$this->_db->query();
	}
}
