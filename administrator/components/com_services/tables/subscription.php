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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for service subscription
 */
class Subscription extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_points_subscriptions', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->uid)
		{
			$this->setError(JText::_('Entry must have a user ID.'));
			return false;
		}

		if (!$this->serviceid)
		{
			$this->setError(JText::_('Entry must have a service ID.'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $id         Entry ID
	 * @param   integer  $oid        User ID
	 * @param   integer  $serviceid  Service ID
	 * @param   array    $status     List of statuses
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadSubscription($id = NULL, $oid=NULL, $serviceid = NULL, $status = array(0, 1, 2))
	{
		if ($id == 0 or  ($oid === NULL && $serviceid === NULL))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE ";
		if ($id)
		{
			$query .= "id='$id' ";
		}
		else if ($oid && $serviceid)
		{
			$query .= "uid='$oid' AND serviceid='$serviceid' ";
		}
		$query .= " AND status IN (" . implode(",", $status) . ")";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Cancel a subscription
	 *
	 * @param   integer  $subid      Subscription ID
	 * @param   integer  $refund     Refund amount
	 * @param   integer  $unitsleft  Units left
	 * @return  boolean  True on success, False on error
	 */
	public function cancelSubscription($subid = NULL, $refund=0, $unitsleft=0)
	{
		if ($subid === NULL )
		{
			return false;
		}

		// status quo if now money back is expected
		$unitsleft = $refund ? $unitsleft : 0;

		$query = "UPDATE $this->_tbl SET status='2', pendingpayment='$refund', pendingunits='$unitsleft' WHERE id='$subid'" ;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get a count of records
	 *
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $admin    Is admin?
	 * @return  integer
	 */
	public function getSubscriptionsCount($filters=array(), $admin=false)
	{
		$filters['exlcudeadmin'] = 1;
		$filter = $this->buildQuery( $filters, $admin );

		$sql = "SELECT count(*) FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid $filter";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 *
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $admin    Is admin?
	 * @return  array
	 */
	public function getSubscriptions($filters, $admin=false)
	{
		$filter = $this->buildQuery( $filters, $admin );
		$filters['exlcudeadmin'] = 1;

		$sql  = "SELECT u.*, s.title, s.category, s.unitprice, s.currency, s.unitsize, s.unitmeasure, s.minunits, s.maxunits ";
		$sql .= " FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid ";
		$sql .= $this->buildQuery( $filters, $admin );
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a subscription
	 *
	 * @param   integer  $id  User ID
	 * @return  mixed
	 */
	public function getSubscription($id)
	{
		if ($id === NULL)
		{
			return false;
		}

		$sql  = "SELECT u.*, s.id as serviceid, s.title, s.category, s.unitprice, s.pointsprice, s.currency, s.unitsize, s.unitmeasure, s.minunits, s.maxunits, e.companyLocation, e.companyName, e.companyWebsite ";
		$sql .= " FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid ";
		$sql .= " JOIN #__jobs_employers as e ON e.uid=u.uid ";
		$sql .= " WHERE u.id='$id' ";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		$result = $result ? $result[0] : NULL;
		return $result;
	}

	/**
	 * Build a query statement
	 *
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $admin    Is admin?
	 * @return  string   SQL
	 */
	public function buildQuery($filters=array(), $admin=false)
	{
		$query = "WHERE 1=1 ";
		if (isset($filters['filterby']))
		{
			switch ($filters['filterby'])
			{
				case 'pending':   $query .= "AND (u.status=0 OR u.pendingpayment > 0 OR u.pendingunits > 0) "; break;
				case 'cancelled': $query .= "AND u.status=2 ";  break;
				default:          $query .= ''; break;
			}
		}

		if (isset($filters['exlcudeadmin']))
		{
			$query .= "AND u.uid!=1 ";
		}

		$query .= " ORDER BY ";
		if (isset($filters['sortby']))
		{
			switch ($filters['sortby'])
			{
				case 'date':
				case 'date_added':   $query .= 'u.added DESC';    break;
				case 'date_expires': $query .= 'u.expires DESC';  break;
				case 'date_updated': $query .= 'u.updated DESC';  break;
				case 'category':     $query .= 's.category DESC'; break;
				case 'status':       $query .= 'u.status ASC';    break;
				case 'pending':
				default:  $query .= 'u.pendingunits DESC, u.pendingpayment DESC, u.status ASC, u.updated DESC ';   break;
			}
		}

		return $query;
	}

	/**
	 * Generate a code
	 *
	 * @param   integer  $minlength   Minimum length
	 * @param   integer  $maxlength   Maximum length
	 * @param   integer  $usespecial  Use special characters?
	 * @param   integer  $usenumbers  Use numbers?
	 * @param   integer  $useletters  Use letters?
	 * @return  string   Return description (if any) ...
	 */
	public function generateCode($minlength = 6, $maxlength = 6, $usespecial = 0, $usenumbers = 1, $useletters = 1)
	{
		$key = '';
		$charset = '';

		if ($useletters) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];

		return $key;
	}

	/**
	 * Get remaining
	 *
	 * @param   string   $type          Type
	 * @param   object   $subscription  Subscription object
	 * @param   integer  $maxunits      Maximum units
	 * @param   mixed    $unitsize      Unit size
	 * @return  mixed
	 */
	public function getRemaining($type='unit', $subscription = NULL, $maxunits = 24, $unitsize=1)
	{
		if ($subscription === NULL)
		{
			return false;
		}

		$current_time = time();

		$limits    = array();
		$starttime = $subscription->added;
		$lastunit  = 0;
		$today     = JFactory::getDate(time() - (24 * 60 * 60))->toSql();

		for ($i = 0; $i < $maxunits; $i++)
		{
			$starttime = JFactory::getDate(strtotime("+".$unitsize."month", strtotime($starttime)))->format('Y-m-d');
			$limits[$i] = $starttime;
		}

		for ($j = 0; $j < count($limits); $j++)
		{
			if (strtotime($current_time) < strtotime($limits[$j]))
			{
				$lastunit = $j + 1;
				if ($type == 'unit')
				{
					$remaining = $subscription->units - $lastunit;
					$refund    = $remaining > 0 ? $remaining : 0;
					return ($remaining);
				}
			}
		}
	}
}

