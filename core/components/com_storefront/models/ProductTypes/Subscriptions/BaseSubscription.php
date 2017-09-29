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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
