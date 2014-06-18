<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class NewsletterMailinglist extends JTable
{
	/**
	 * Mailing List ID
	 *
	 * @var int(11)
	 */
	var $id 		= NULL;

	/**
	 * Mailing List Name
	 *
	 * @var varchar(150)
	 */
	var $name 		= NULL;

	/**
	 * Mailing List Dec
	 *
	 * @var text
	 */
	var $description = NULL;

	/**
	 * Mailing List Private
	 *
	 * @var int(11)
	 */
	var $private 	= NULL;

	/**
	 * Mailing List Deleted
	 *
	 * @var int(11)
	 */
	var $deleted 	= NULL;


	/**
	 * Newsletter Mailing List Constructor
	 *
	 * @param 	$db		Database Object
	 * @return 	void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_mailinglists', 'id', $db );

		//set up the assoc table
		$this->_tbl_assoc = '#__newsletter_mailinglist_emails';
		$this->_tbl_assoc_key = 'id';
	}


	/**
	 * Newsletter Mailing List Save Check method
	 *
	 * @return 	boolean
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError('Newsletter mailing list must have a name.');
			return false;
		}

		return true;
	}


	/**
	 * Get Mailing Lists
	 *
	 * @param 	$id		Mailing List Id
	 * @return 	array
	 */
	public function getLists( $id = null, $privacy = null )
	{
		$sql = "SELECT
					ml.*,
					(SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle WHERE mle.mid=ml.id AND mle.status='active') as active_count,
					(SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle WHERE mle.mid=ml.id) as total_count
				FROM {$this->_tbl} AS ml
				WHERE ml.deleted=0";

		//do we have a specific status
		if (strtolower($privacy) == 'private')
		{
			$sql .= " AND ml.private=1";
		}
		else if (strtolower($privacy) == 'public')
		{
			$sql .= " AND ml.private=0";
		}

		//do we have an id
		if ($id)
		{
			$sql .= " AND ml.id=" . $this->_db->quote( $id );
			$this->_db->setQuery( $sql );
			return $this->_db->loadObject();
		}
		else
		{
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Get number of emails in list
	 * @param  [type] $filters
	 * @return [type]
	 */
	public function getListEmailsCount($filters)
	{
		// are we loading default list
		if (isset($filters['lid']) && $filters['lid'] == -1)
		{
			return count($this->_getHubMailingList());
		}

		$sql = "SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle";
		$wheres = array();

		if (isset($filters['lid']))
		{
			$wheres[] = "mle.mid=" . $this->_db->quote( $filters['lid'] );
		}

		if (isset($filters['status']))
		{
			$wheres[] = "mle.status=" . $this->_db->quote( $filters['status'] );
		}

		if (count($wheres) > 0)
		{
			$sql .= " WHERE " . implode(' AND ', $wheres);
		}

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}


	/**
	 * Get Mailing List Emails
	 *
	 * @param 	$id		Mailing List Id
	 * @return 	array
	 */
	public function getListEmails( $mailinglistId, $key = null, $filters = array() )
	{
		//make sure we have a mailing list
		if (!$mailinglistId)
		{
			return;
		}

		//are we loading default list
		if ($mailinglistId == '-1')
		{
			$list = $this->_getHubMailingList();
			if (isset($filters['select']))
			{
				return array_keys($list);
			}
			return $list;
		}

		// default select
		$select = "mle.*, (SELECT reason FROM #__newsletter_mailinglist_unsubscribes AS u
				WHERE mle.email=u.email AND mle.mid=u.mid) AS unsubscribe_reason";

		// specific select
		if (isset($filters['select']))
		{
			$select = $filters['select'];
		}

		//get list of emails
		$sql = "SELECT {$select}
				FROM {$this->_tbl_assoc} AS mle
				WHERE mle.mid=" . $this->_db->quote( $mailinglistId );

		//do we have a status
		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			 $sql .= " AND mle.status=" . $this->_db->quote( $filters['status'] );
		}

		//do we have an order filter
		if (isset($filters['sort']) && $filters['sort'] != '')
		{
			$sql .= " ORDER BY mle." . $filters['sort'];
		}
		else
		{
			$sql .= " ORDER BY mle.id";
		}

		// limit and start
		if (isset($filters['limit']))
		{
			$start = (isset($filters['start'])) ? $filters['start'] : 0;
			$sql .= " LIMIT " . $start . ", " . $filters['limit'];
		}

		$this->_db->setQuery( $sql );

		if (isset($filters['select']))
		{
			return $this->_db->loadResultArray();
		}

		return $this->_db->loadObjectList( $key );
	}


	/**
	 * Get Mailing List Emails
	 *
	 * @param 	$id		Mailing List Id
	 * @return 	array
	 */
	public function getListsForEmail( $email, $key = null, $status = 'all' )
	{
		if (!$email)
		{
			return;
		}

		//get lists that member belongs to
		$sql = "SELECT mle.id AS id, ml.id as mailinglistid, ml.name, ml.description, mle.status, mle.confirmed
				FROM {$this->_tbl} AS ml, {$this->_tbl_assoc} AS mle
				WHERE ml.id=mle.mid
				AND ml.deleted=0
				AND mle.email=" . $this->_db->quote( $email );

		//do we have a status
		if ($status != 'all')
		{
			 $sql .= " AND mle.status=" . $this->_db->quote( $status );
		}

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList( $key );
	}


	/**
	 * Get default hub members list
	 *
	 * @return 	array 	Email List
	 */
	private function _getHubMailingList()
	{
		$sql = "SELECT DISTINCT email FROM #__xprofiles WHERE emailConfirmed = '1' AND mailPreferenceOption > '0'";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('email');
	}
}
