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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for primary stories
 */
class PrimaryStory extends \JTable
{
	/**
	 * Newsletter Primary Story Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_primary_story', 'id', $db);
	}

	/**
	 * Get Primary Stories
	 *
	 * @param   integer  $newsletterId  Newsletter Id
	 * @return  array
	 */
	public function getStories($newsletterId)
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";

		if ($newsletterId)
		{
			$sql .= " AND nid=" . $this->_db->quote($newsletterId);
		}

		$sql .= " ORDER BY `order`";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get Highest Story Order
	 *
	 * @param   integer  $newsletterId  Newsletter Id
	 * @return 	integer
	 */
	public function _getCurrentHighestOrder($newsletterId)
	{
		$sql = "SELECT `order` FROM {$this->_tbl} WHERE deleted=0 AND nid=" . $this->_db->quote($newsletterId) . " ORDER BY `order` DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}