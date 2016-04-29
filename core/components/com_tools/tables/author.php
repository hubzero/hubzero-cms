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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Components\Tools\Helpers\Utils;
use User;
use Lang;

/**
 * Table class for tool authors
 */
class Author extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_authors', 'version_id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->version_id)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_AUTHOR_NO_VERSIONID'));
			return false;
		}

		if (!$this->uid)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_AUTHOR_NO_UID'));
			return false;
		}

		$this->toolname = trim($this->toolname);
		if (!$this->toolname)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_AUTHOR_NO_TOOLNAME'));
			return false;
		}
		if (!$this->revision)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_AUTHOR_NO_REVISION'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getToolContributions'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolContributions($uid)
	{
		if (!$uid)
		{
			return false;
		}
		$sql = " SELECT f.toolname FROM #__tool as f "
				. "JOIN #__tool_groups AS g ON f.id=g.toolid AND g.role=1 "
				. "JOIN #__xgroups AS xg ON g.cn=xg.cn "
				. "JOIN #__xgroups_managers AS m ON xg.gidNumber=m.gidNumber AND uidNumber=" . $this->_db->quote($uid);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();

	}

	/**
	 * Get the first author's name on a resource
	 *
	 * @param      integer $rid Resource ID
	 * @return     string
	 */
	public function getFirstAuthor($rid = 0)
	{
		$query  = "SELECT x.name FROM #__xprofiles x ";
		$query .= " JOIN #__author_assoc AS aa ON x.uidNumber=aa.authorid AND aa.subid= " . $this->_db->quote($rid) . " AND aa.subtable='resources' ";
		$query .= " ORDER BY aa.ordering ASC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getAuthorsDOI'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $rid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getAuthorsDOI($rid = 0)
	{
		$query  = "SELECT x.name FROM #__xprofiles x ";
		$query .= " JOIN #__author_assoc AS aa ON x.uidNumber=aa.authorid AND aa.subid= " . $this->_db->quote($rid) . " AND aa.subtable='resources' ";
		$query .= " ORDER BY aa.ordering ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of the authors on a tool
	 *
	 * @param      string  $version  Tool version
	 * @param      integer $rid      Resource ID
	 * @param      string  $toolname Tool name
	 * @param      string  $revision Tool revision
	 * @param      array   $authors  Author list
	 * @return     array
	 */
	public function getToolAuthors($version='', $rid=0, $toolname='', $revision='', $authors=array())
	{
		if ($version == 'dev' && $rid)
		{
			$query = "SELECT authorid as uidNumber FROM #__author_assoc WHERE subid= " . $this->_db->quote($rid) . " AND subtable='resources' ORDER BY ordering";
			$this->_db->setQuery($query);
			$authors = $this->_db->loadObjectList();
		}
		else
		{
			$query  = "SELECT DISTINCT a.uid as uidNumber ";
			$query .= "FROM #__tool_authors as a  ";
			if ($version == 'current' && $toolname)
			{
				$objV = new Version($this->_db);
				$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
				if ($rev)
				{
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname=" . $this->_db->quote($toolname) . " AND a.revision=" . $this->_db->quote($rev);
				}
				else
				{
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname=" . $this->_db->quote($toolname) . " AND v.state=1 ORDER BY v.revision DESC";
				}
			}
			else if (is_numeric($version))
			{
				$query .= "WHERE a.version_id=" . $this->_db->quote($version) . " ORDER BY a.ordering";
			}
			else if ($toolname && $revision)
			{
				$query .= "WHERE a.toolname=" . $this->_db->quote($toolname) . " AND a.revision=" . $this->_db->quote($revision) . " ORDER BY a.ordering";
			}
			else if (is_object($version))
			{
				$query .= "WHERE a.version_id=" . $this->_db->quote($version->id) . " ORDER BY a.ordering";
			}
			else if (isset($version[0]) && is_object($version[0]))
			{
				$query .= "WHERE a.version_id=" . $this->_db->quote($version[0]->id) . " ORDER BY a.ordering";
			}
			else
			{
				return null;
			}

			$this->_db->setQuery($query);
			$authors = $this->_db->loadObjectList();
		}
		return $authors;
	}

	/**
	 * Save a list of authors
	 *
	 * @param      array   $authors  List of authors to add
	 * @param      string  $version  Tool version
	 * @param      integer $rid      Resource ID
	 * @param      integer $revision Revision number
	 * @param      string  $toolname Tool name
	 * @return     boolean False if errors, True if not
	 */
	public function saveAuthors($authors, $version='dev', $rid=0, $revision=0, $toolname='')
	{
		if (!$rid)
		{
			return false;
		}

		if ($authors)
		{
			$authors = Utils::transform($authors, 'uidNumber');
		}

		$dev_authors = $this->getToolAuthors('dev', $rid);
		$dev_authors = Utils::transform($dev_authors, 'uidNumber');

		if ($dev_authors && $version == 'dev')
		{
			// update
			$to_delete = array_diff($current_authors, $authors);
			if ($to_delete)
			{
				foreach ($to_delete as $del)
				{
					$query = "DELETE FROM #__author_assoc  WHERE authorid=" . $this->_db->quote($del) . " AND subid=" . $this->_db->quote($rid) . " AND subtable='resources'";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}

		// add new authors
		if ($version == 'dev')
		{
			// development version is updated
			$to_delete = array_diff($dev_authors, $authors);

			$rc = new \Components\Resources\Tables\Contributor($this->_db);
			$rc->subtable = 'resources';
			$rc->subid = $rid;

			if ($to_delete)
			{
				foreach ($to_delete as $del)
				{
					$query = "DELETE FROM #__author_assoc  WHERE authorid=" . $this->_db->quote($del) . " AND subid=" . $this->_db->quote($rid) . " AND subtable='resources'";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
			// Get the last child in the ordering
			$order = $rc->getLastOrder($rid, 'resources');
			$order = $order + 1; // new items are always last

			foreach ($authors as $authid)
			{
				// Check if they're already linked to this resource
				$rc->loadAssociation($authid, $rid, 'resources');
				if (!$rc->authorid)
				{
					$xprofile = User::getInstance($authid);

					// New record
					$rc->authorid = $authid;
					$rc->ordering = $order;
					$rc->name = addslashes($xprofile->get('name'));
					$rc->organization = addslashes($xprofile->get('organization'));
					$rc->createAssociation();

					$order++;
				}
			}
		}
		else if ($dev_authors)
		{
			// new version is being published, transfer data from author_assoc
			$i=0;

			foreach ($dev_authors as $authid)
			{
				// Do we have name/org info in previous version?
				$query  = "SELECT name, organization FROM #__tool_authors ";
				$query .= "WHERE toolname=" . $this->_db->quote($toolname) . " AND uid=" . $this->_db->quote($authid) . " AND revision < " . $this->_db->quote($revision);
				$query .= " AND name IS NOT NULL AND organization IS NOT NULL ";
				$query .= " ORDER BY revision DESC LIMIT 1";
				$this->_db->setQuery($query);
				$info = $this->_db->loadObjectList();
				if ($info)
				{
					$name         = $info[0]->name;
					$organization = $info[0]->organization;
				}
				else
				{
					$xprofile = User::getInstance($authid);

					$name         = $xprofile->get('name');
					$organization = $xprofile->get('organization');
				}

				$query = "INSERT INTO $this->_tbl (toolname, revision, uid, ordering, version_id, name, organization) VALUES ('" . $toolname . "','" . $revision . "','" . $authid . "','" . $i . "', '" . $version . "', '" . addslashes($name) . "', '" . addslashes($organization) . "')";
				$this->_db->setQuery($query);
				if (!$this->_db->query())
				{
					return false;
				}
				$i++;
			}
		}

		return true;
	}

	/**
	 * Check the author's name
	 * Ensures the individual name fields are filled in
	 *
	 * @param      integer $id User ID
	 * @return     void
	 */
	private function _author_check($id)
	{
		$xprofile = User::getInstance($id);
		if ($xprofile->get('givenName') == ''
		 && $xprofile->get('middleName') == ''
		 && $xprofile->get('surname') == '')
		{
			$bits = explode(' ', $xprofile->get('name'));
			$xprofile->set('surname', array_pop($bits));
			if (count($bits) >= 1)
			{
				$xprofile->set('givenName', array_shift($bits));
			}
			if (count($bits) >= 1)
			{
				$xprofile->set('middleName', implode(' ', $bits));
			}
		}
	}
}
