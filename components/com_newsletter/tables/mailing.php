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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$sql = "SELECT nm.id AS mailing_id, n.name AS newsletter_name, n.tracking AS newsletter_tracking, nm.date AS mailing_date
				FROM {$this->_tbl} AS nm, #__newsletters AS n
				WHERE nm.deleted=0
				AND nm.nid=n.id
				ORDER BY nm.date DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}