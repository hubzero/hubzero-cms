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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Resources table class for stats
 */
class ResourcesStats extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       = NULL;

	/**
	 * varchar(250)
	 *
	 * @var string
	 */
	var $resid    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $restype  = NULL;

	/**
	 * Description for 'users'
	 *
	 * @var unknown
	 */
	var $users    = NULL;

	/**
	 * Description for 'jobs'
	 *
	 * @var unknown
	 */
	var $jobs     = NULL;

	/**
	 * Description for 'avg_wall'
	 *
	 * @var unknown
	 */
	var $avg_wall = NULL;

	/**
	 * Description for 'tot_wall'
	 *
	 * @var unknown
	 */
	var $tot_wall = NULL;

	/**
	 * Description for 'avg_cpu'
	 *
	 * @var unknown
	 */
	var $avg_cpu  = NULL;

	/**
	 * Description for 'tot_cpu'
	 *
	 * @var unknown
	 */
	var $tot_cpu  = NULL;

	/**
	 * Description for 'datetime'
	 *
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'period'
	 *
	 * @var unknown
	 */
	var $period   = NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(JText::_('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load data for a resource and given period
	 *
	 * @param      integer $resid  Resource ID
	 * @param      integer $period Time period
	 * @param      string  $dthis  YYYY-MM
	 * @return     boolean True on success, False on error
	 */
	public function loadStats($resid=NULL, $period=NULL, $dthis=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT *
				FROM $this->_tbl
				WHERE datetime=" . $this->_db->quote($dthis . "-01 00:00:00") . " AND period=" . $this->_db->Quote($period) . " AND resid=" . $this->_db->Quote($resid);

		$this->_db->setQuery($sql);

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
}

/**
 * Resources table class for tool stats
 */
class ResourcesStatsTools extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var unknown
	 */
	var $id       = NULL;

	/**
	 * varchar(250)
	 *
	 * @var unknown
	 */
	var $resid    = NULL;

	/**
	 * int(11)
	 *
	 * @var unknown
	 */
	var $restype  = NULL;

	/**
	 * Description for 'users'
	 *
	 * @var unknown
	 */
	var $users    = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $sessions    = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $simulations = NULL;

	/**
	 * Description for 'jobs'
	 *
	 * @var unknown
	 */
	var $jobs     = NULL;

	/**
	 * Description for 'avg_wall'
	 *
	 * @var unknown
	 */
	var $avg_wall = NULL;

	/**
	 * Description for 'tot_wall'
	 *
	 * @var unknown
	 */
	var $tot_wall = NULL;

	/**
	 * Description for 'avg_cpu'
	 *
	 * @var unknown
	 */
	var $avg_cpu  = NULL;

	/**
	 * Description for 'tot_cpu'
	 *
	 * @var unknown
	 */
	var $tot_cpu  = NULL;

	/**
	 * Description for 'avg_view'
	 *
	 * @var unknown
	 */
	var $avg_view = NULL;

	/**
	 * Description for 'tot_view'
	 *
	 * @var unknown
	 */
	var $tot_view = NULL;

	/**
	 * Description for 'avg_cpus'
	 *
	 * @var unknown
	 */
	var $avg_cpus = NULL;

	/**
	 * Description for 'tot_cpus'
	 *
	 * @var unknown
	 */
	var $tot_cpus = NULL;

	/**
	 * Description for 'datetime'
	 *
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'period'
	 *
	 * @var unknown
	 */
	var $period   = NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(JText::_('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load data for a resource and given period
	 *
	 * @param      integer $resid  Resource ID
	 * @param      integer $period Time period
	 * @param      string  $dthis  YYYY-MM
	 * @return     boolean True on success, False on error
	 */
	public function loadStats($resid=NULL, $period=NULL, $dthis=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT id, users, sessions, simulations, jobs, avg_wall, tot_wall, avg_cpu, tot_cpu, avg_view, tot_view, avg_wait, tot_wait, avg_cpus, tot_cpus, period, LEFT(datetime,7) as datetime
				FROM $this->_tbl
				WHERE datetime=" . $this->_db->quote($dthis . "-00 00:00:00") . " AND period=" . $this->_db->Quote($period) . " AND resid=" . $this->_db->Quote($resid);

		$this->_db->setQuery($sql);

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
}

/**
 * resources table class for tool top stats
 */
class ResourcesStatsToolsTop extends JTable
{
	/**
	 * tinyint(4) Primary key
	 *
	 * @var unknown
	 */
	var $top    = NULL;

	/**
	 * varchar(128)
	 *
	 * @var unknown
	 */
	var $name   = NULL;

	/**
	 * tinyint(4)
	 *
	 * @var unknown
	 */
	var $valfmt = NULL;

	/**
	 * tinyint(4)
	 *
	 * @var unknown
	 */
	var $size   = NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools_tops', 'top', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('Your entry must have a name.'));
			return false;
		}
		return true;
	}
}

/**
 * Resources table class for tool top value stats
 */
class ResourcesStatsToolsTopvals extends JTable
{
	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $id    = NULL;

	/**
	 * tinyint(4)
	 *
	 * @var unknown
	 */
	var $top   = NULL;

	/**
	 * tinyint(4)
	 *
	 * @var unknown
	 */
	var $rank  = NULL;

	/**
	 * varchar(255)
	 *
	 * @var unknown
	 */
	var $name  = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $value = NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools_topvals', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('Your entry must have a name.'));
			return false;
		}
		return true;
	}

	/**
	 * Get top countries for a resource
	 *
	 * @param      integer $id  Resource Id
	 * @param      integer $top Top value
	 * @return     mixed False on error, Array on success
	 */
	public function getTopCountryRes($id=NULL, $top=NULL)
	{
		if ($id == NULL)
		{
			$id = $this->id;
		}
		if ($id == NULL)
		{
			return false;
		}
		if ($top == NULL)
		{
			$top = $this->top;
		}
		if ($top == NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE id=" . $this->_db->Quote($id) . " AND top=" . $this->_db->Quote($top) . " ORDER BY rank");
		return $this->_db->loadObjectList();
	}
}

/**
 * Resources table class for tool user stats
 */
class ResourcesStatsToolsUsers extends JTable
{
	/**
	 * int(20) Primary key
	 *
	 * @var unknown
	 */
	var $id          = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $resid       = NULL;

	/**
	 * int(11)
	 *
	 * @var unknown
	 */
	var $restype     = NULL;

	/**
	 * varchar(32)
	 *
	 * @var unknown
	 */
	var $user        = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $sessions    = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $simulations = NULL;

	/**
	 * int(20)
	 *
	 * @var unknown
	 */
	var $jobs        = NULL;

	/**
	 * double
	 *
	 * @var unknown
	 */
	var $tot_wall    = NULL;

	/**
	 * double
	 *
	 * @var unknown
	 */
	var $tot_cpu     = NULL;

	/**
	 * double
	 *
	 * @var unknown
	 */
	var $tot_view    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var unknown
	 */
	var $datetime    = NULL;

	/**
	 * tinyint(4)
	 *
	 * @var unknown
	 */
	var $period      = NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools_users', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(JText::_('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Get top users for a resource
	 *
	 * @param      integer $resid  Resource ID
	 * @param      integer $period Time period
	 * @param      string  $dthis  YYYY-MM
	 * @param      integer $top    Top value
	 * @return     array
	 */
	public function getTopUsersRes($resid, $dthis, $period, $top)
	{
		$sql = "SELECT u.name, s.user, u.email, u.organization, s.jobs, s.sessions, s.simulations, s.tot_wall, s.tot_cpu, s.tot_view
				FROM $this->_tbl AS s, user AS u
				WHERE u.user = s.user AND s.datetime=" . $this->_db->quote($dthis . "-00") . " AND s.period=" . $this->_db->Quote($period) . " AND s.resid=" . $this->_db->Quote($resid) . "
				ORDER BY s.jobs DESC limit 25";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}

/**
 * Resources table class for cluster stats
 */
class ResourcesStatsClusters extends JTable
{
	/**
	 * bigint(20) Primary key
	 *
	 * @var unknown
	 */
	var $id       		= NULL;

	/**
	 * varchar(255)
	 *
	 * @var unknown
	 */
	var $cluster  		= NULL;

	/**
	 * varchar(32)
	 *
	 * @var unknown
	 */
	var $username 		= NULL;

	/**
	 * int(11)
	 *
	 * @var unknown
	 */
	var $uidNumber 		= NULL;

	/**
	 * Dvarchar(80)
	 *
	 * @var unknown
	 */
	var $toolname 		= NULL;

	/**
	 * int(11)
	 *
	 * @var unknown
	 */
	var $resid   		= NULL;

	/**
	 * varchar(255)
	 *
	 * @var unknown
	 */
	var $clustersize  	= NULL;

	/**
	 * datetime
	 *
	 * @var unknown
	 */
	var $cluster_start 	= NULL;

	/**
	 * datetime
	 *
	 * @var unknown
	 */
	var $cluster_end 	= NULL;

	/**
	 * varchar(255)
	 *
	 * @var unknown
	 */
	var $instituion 	= NULL;

	/**
	 * Description for 'users'
	 *
	 * @var unknown
	 */
	var $users		= NULL;

	/**
	 * Description for 'classes'
	 *
	 * @var unknown
	 */
	var $classes 	= NULL;

	/**
	 * Construct
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_clusters', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(JText::_('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load data for a resource
	 *
	 * @param      integer $resid  Resource ID
	 * @return     boolean True on success, False on error
	 */
	public function loadStats($resid=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT COUNT(DISTINCT uidNumber, username) AS users, COUNT(DISTINCT cluster) AS classes
				FROM $this->_tbl
				WHERE resid=" . $this->_db->Quote($resid);

		$this->_db->setQuery($sql);

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
}
