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
 * Table class for Newsletter mailings
 */
class Mailing extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct( '#__newsletter_mailings', 'id', $db );
	}

	/**
	 * Get either a list or single mailing
	 *
	 * @param   integer  $id
	 * @param   integer  $nid
	 * @return  mixed
	 */
	public function getMailings($id = null, $nid = null)
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";

		if ($id)
		{
			$sql .= " AND id=" . $id;
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			if (isset($nid))
			{
				$sql .= " AND nid=" . $this->_db->quote($nid);
			}

			$sql .= " ORDER BY date DESC";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Get newletters
	 *
	 * @param   integer  $id
	 * @param   integer  $nid
	 * @return  mixed
	 */
	public function getMailingNewsletters()
	{
		$sql = "SELECT nm.id AS mailing_id, n.name AS newsletter_name, n.tracking AS newsletter_tracking, nm.date AS mailing_date, n.autogen AS autogen
				FROM {$this->_tbl} AS nm, #__newsletters AS n
				WHERE nm.deleted=0
				AND nm.nid=n.id
				ORDER BY nm.date DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
