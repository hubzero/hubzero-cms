<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication access
 */
class Access extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_access', 'publication_version_id', $db);
	}

	/**
	 * Load entry
	 *
	 * @param   integer  $vid       pub version id
	 * @param   integer  $group_id  group id
	 * @return  object or FALSE
	 */
	public function loadEntry($vid = null, $group_id = null)
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid || !$group_id)
		{
			return false;
		}

		return parent::load(array(
			'publication_version_id' => (int) $vid,
			'group_id'               => (int) $group_id
		));
	}

	/**
	 * Check record existence
	 *
	 * @param   integer  $vid       pub version id
	 * @param   integer  $group_id  group id
	 * @return  mixed    integer or null
	 */
	public function existsEntry($vid = null, $group_id = null)
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid || !$group_id)
		{
			return false;
		}

		$this->_db->setQuery("SELECT publication_version_id FROM $this->_tbl
			WHERE publication_version_id=" . $this->_db->quote($vid) . "
			AND group_id=" . $this->_db->quote($group_id));
		return $this->_db->loadResult();
	}

	/**
	 * Get groups
	 *
	 * @param   integer  $vid       pub version id
	 * @param   integer  $pid       pub id
	 * @param   string   $version   version name or number
	 * @param   string   $sysgroup  name of system group
	 * @return  object
	 */
	public function getGroups($vid = null, $pid = null, $version = 'default', $sysgroup = '')
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid && !$pid)
		{
			return false;
		}

		$query  = "SELECT A.group_id, X.cn, X.description, X.published as regconfirmed FROM $this->_tbl as A ";
		$query.= " JOIN #__xgroups AS X ON X.gidNumber = A.group_id ";
		if ($vid)
		{
			$query .= "WHERE A.publication_version_id=" . $this->_db->quote($vid);
		}
		else
		{
			$query .= " JOIN #__publication_versions AS V ON V.id=A.publication_version_id ";
			$query .= " WHERE V.publication_id=" . $this->_db->quote($pid);
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
		}
		if ($sysgroup)
		{
			$query.= " AND X.cn !=" . $this->_db->quote($sysgroup);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Save groups
	 *
	 * @param   integer  $vid       pub version id
	 * @param   array    $groups
	 * @param   string   $sysgroup  name of system group
	 * @return  integer
	 */
	public function saveGroups($vid = null, $groups = '', $sysgroup = 0)
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}

		// Clean up
		$this->deleteGroups($vid, $sysgroup);

		$add = array();
		$saved = 0;
		if ($groups)
		{
			$add = explode(',', $groups);
		}
		if ($sysgroup)
		{
			$add[] = $sysgroup;
		}

		foreach ($add as $a)
		{
			$a = trim($a);
			if ($a == '')
			{
				continue;
			}

			$group = \Hubzero\User\Group::getInstance($a);
			$gid = $group ? $group->get('gidNumber') : 0;

			if (!$gid or $this->existsEntry($vid, $gid))
			{
				continue;
			}
			else
			{
				$query = "INSERT INTO $this->_tbl (publication_version_id,group_id)
				          VALUES($this->_db->quote($vid), $this->_db->quote($gid))";
				$this->_db->setQuery($query);
				if ($this->_db->query())
				{
					$saved++;
				}
			}

		}

		return $saved;
	}

	/**
	 * Delete groups
	 *
	 * @param   integer  $vid       pub version id
	 * @param   string   $sysgroup  name of system group
	 * @return  boolean
	 */
	public function deleteGroups($vid = null, $sysgroup = 0)
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);
		$query.= $sysgroup ? " AND group_id !=" . $this->_db->quote($sysgroup) : "";
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
