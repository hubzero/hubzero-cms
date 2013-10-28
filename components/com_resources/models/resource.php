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

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');

/**
 * Information retrieval for items/info linked to a resource
 */
class ResourcesModelResource extends JObject
{
	/**
	 * Resource ID
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid, $revision=null)
	{
		$this->_db = JFactory::getDBO();

		$this->resource = new ResourcesResource($this->_db);
		$this->resource->load($oid);

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$this->params = JComponentHelper::getParams('com_resources');
		$this->params->merge(new $paramsClass($this->resource->params));

		$this->attribs = new $paramsClass($this->resource->attribs);

		if ($this->isTool()) 
		{
			$this->thistool = null;
			$this->curtool  = null;

			$tables = $this->_db->getTableList();
			$table  = $this->_db->getPrefix() . 'tool_version';

			if (in_array($table, $tables)) 
			{
				$tv = new ToolVersion($this->_db);
				//$tv->getToolVersions('', $alltools, $this->resource->alias);

				if ($this->revisions()) //$alltools) 
				{
					foreach ($this->revisions() as $tool)
					{
						// Archive version, if requested
						if (($revision && $tool->revision == $revision && $revision != 'dev') 
						 or ($revision == 'dev' and $tool->state==3)) 
						{
							$this->thistool = $tool;
						}
						// Current version
						if ($tool->state == 1 && (count($this->revisions()) == 1 || (count($this->revisions()) > 1 &&  $this->revisions(1)->version == $tool->version)))
						{
							$this->curtool = $tool;
							$revision = $revision ? $revision : $tool->revision;
						}
						// Dev version
						if (!$revision && count($this->revisions()) == 1 && $tool->state == 3) 
						{
							$this->thistool = $tool;
							$revision = 'dev';
						}
					}

					if (!$this->thistool && !$this->curtool && count($this->revisions()) > 1) 
					{
						// Tool is retired, display latest unpublished version
						$this->thistool = $this->revisions(1);
						$revision = $this->thistool->revision;
					}

					// If the revision is the same as the current version
					if ($this->curtool && $this->thistool && $this->thistool == $this->curtool) 
					{
						// Display default resource page for current version
						$this->thistool = null;
					}
				}

				$tconfig =& JComponentHelper::getParams('com_tools');
				// Replace resource info with requested version
				$tv->compileResource($this->thistool, $this->curtool, $this->resource, $revision, $tconfig);
			}
			$this->revision = $revision;
		}

		$this->type = new ResourcesType($this->_db);
		$this->type->bind($this->types($this->resource->type));
		$this->type->params = new $paramsClass($this->type->params);
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($oid=null, $revision=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$inst = new ResourcesModelResource($oid, $revision);

			$instances[$oid] = $inst;
		}

		return $instances[$oid];
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->resource->id && $this->resource->id > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the resource was deleted
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function deleted()
	{
		if ($this->resource->published == 4) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the resource is a tool or not
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function published()
	{
		if (!$this->exists())
		{
			return false;
		}

		// Make sure the resource is published and standalone
		if ($this->resource->published == 0) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->resource->publish_up 
		 && $this->resource->publish_up != '0000-00-00 00:00:00' 
		 && $this->resource->publish_up >= $now) 
		{
			return false;
		}
		if ($this->resource->publish_down 
		 && $this->resource->publish_down != '0000-00-00 00:00:00' 
		 && $this->resource->publish_down <= $now) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->_authorized)
		{
			$this->_authorize();
		}
		return $this->params->get('access-' . strtolower($action) . '-resource');
	}

	/**
	 * Authorize current user
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	private function _authorize()
	{
		$juser = JFactory::getUser();

		// NOT logged in
		if ($juser->get('guest'))
		{
			// If the resource is published and public
			if ($this->published() && ($this->resource->access == 0 || $this->resource->access == 3))
			{
				// Allow view access
				$this->params->set('access-view-resource', true);
				if ($this->resource->access == 0)
				{
					$this->params->set('access-view-all-resource', true);
				}
			}
			$this->_authorized = true;
			return;
		}

		if ($this->isTool())
		{
			$tconfig = JComponentHelper::getParams('com_tools');

			if (($admingroup = trim($tconfig->get('admingroup', '')))) 
			{
				ximport('Hubzero_User_Helper');
				// Check if they're a member of admin group
				$ugs = Hubzero_User_Helper::getGroups($juser->get('id'));
				if ($ugs && count($ugs) > 0) 
				{
					$admingroup = strtolower($admingroup);
					foreach ($ugs as $ug)
					{
						if (strtolower($ug->cn) == $admingroup) 
						{
							$this->params->set('access-view-resource', true);
							$this->params->set('access-view-all-resource', true);

							$this->params->set('access-admin-resource', true);
							$this->params->set('access-manage-resource', true);

							$this->params->set('access-create-resource', true);
							$this->params->set('access-delete-resource', true);
							$this->params->set('access-edit-resource', true);
							$this->params->set('access-edit-state-resource', true);
							$this->params->set('access-edit-own-resource', true);
							break;
						}
					}
				}
			}
		}
		else
		{
			// Check if they're a site admin (from Joomla)
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				if ($juser->authorize('com_resources', 'manage')) 
				{
					$this->params->set('access-view-resource', true);
					$this->params->set('access-view-all-resource', true);

					$this->params->set('access-admin-resource', true);
					$this->params->set('access-manage-resource', true);

					$this->params->set('access-create-resource', true);
					$this->params->set('access-delete-resource', true);
					$this->params->set('access-edit-resource', true);
					$this->params->set('access-edit-state-resource', true);
					$this->params->set('access-edit-own-resource', true);
				}
			}
			else 
			{
				$this->params->set('access-admin-resource', $juser->authorise('core.admin', null));
				$this->params->set('access-manage-resource', $juser->authorise('core.manage', null));
				if ($this->params->get('access-admin-resource') 
				 || $this->params->get('access-manage-resource'))
				{
					$this->params->set('access-view-resource', true);
					$this->params->set('access-view-all-resource', true);

					$this->params->set('access-create-resource', true);
					$this->params->set('access-delete-resource', true);
					$this->params->set('access-edit-resource', true);
					$this->params->set('access-edit-state-resource', true);
					$this->params->set('access-edit-own-resource', true);
				}
			}
		}

		// If they're not an admin
		if (!$this->params->get('access-admin-resource') 
		 && !$this->params->get('access-manage-resource'))
		{
			// If logged in and resource is published and public or registered
			if ($this->published() && ($this->resource->access == 0 || $this->resource->access == 1))
			{
				// Allow view access
				$this->params->set('access-view-resource', true);
				$this->params->set('access-view-all-resource', true);
			}

			// Check if they're the resource creator
			if ($this->resource->created_by == $juser->get('id')) 
			{
				// Give full access
				$this->params->set('access-view-resource', true);
				$this->params->set('access-view-all-resource', true);

				$this->params->set('access-create-resource', true);
				$this->params->set('access-delete-resource', true);
				$this->params->set('access-edit-resource', true);
				$this->params->set('access-edit-state-resource', true);
				$this->params->set('access-edit-own-resource', true);
			}
			// Listed as a contributor
			else if (in_array($juser->get('id'), $this->contributors('id'))) 
			{
				// Give full access
				$this->params->set('access-view-resource', true);
				$this->params->set('access-view-all-resource', true);

				$this->params->set('access-create-resource', true);
				$this->params->set('access-delete-resource', true);
				$this->params->set('access-edit-resource', true);
				$this->params->set('access-edit-state-resource', true);
				$this->params->set('access-edit-own-resource', true);
			}
			// Check group access
			else if ($this->resource->group_owner) // && ($this->resource->access == 3 || $this->resource->access == 4))
			{
				// For protected resources, make sure users can see abstract
				if ($this->resource->access < 3)
				{
					$this->params->set('access-view-resource', true);
					$this->params->set('access-view-all-resource', true);
				}
				else if ($this->resource->access == 3)
				{
					$this->params->set('access-view-resource', true);
				}

				// Get the groups the user has access to
				ximport('Hubzero_User_Helper');
				$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
				$usersgroups = array();
				if (!empty($xgroups)) 
				{
					foreach ($xgroups as $group)
					{
						if ($group->regconfirmed) 
						{
							$usersgroups[] = $group->cn;
						}
					}
				}

				// Get the groups that can access this resource
				$allowedgroups = $this->resource->getGroups();

				// Find what groups the user has in common with the resource, if any
				$common = array_intersect($usersgroups, $allowedgroups);

				// Check if the user is apart of the group that owns the resource
				// or if they have any groups in common
				if (in_array($this->resource->group_owner, $usersgroups) || count($common) > 0) 
				{
					$this->params->set('access-view-resource', true);
					$this->params->set('access-view-all-resource', true);
					if (!empty($xgroups)) 
					{
						foreach ($xgroups as $group)
						{
							if ($this->resource->group_owner == $group->cn && $group->manager) 
							{
								$this->params->set('access-delete-resource', true);
								$this->params->set('access-edit-resource', true);
								$this->params->set('access-edit-state-resource', true);
								$this->params->set('access-edit-own-resource', true);
								break;
							}
						}
					}
				}
			}
			else
			{
				$this->params->set('access-view-resource', true);
				$this->params->set('access-view-all-resource', true);
			}
		}

		$this->_authorized = true;
	}

	/**
	 * Check if the resource is a tool or not
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function isTool()
	{
		static $isTool;

		if (!isset($isTool))
		{
			$isTool = false;

			$tool = 7;

			if (($type = $this->types('tools')))
			{
				$tool = $type->id;
			}
			if ($this->resource->type == $tool)
			{
				$isTool = true;
			}
		}

		return $isTool;
	}

	/**
	 * Check if the resource is a tool or not
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function inGroup($group=null)
	{
		if ($group !== null)
		{
			if (!is_array($group))
			{
				$group = array($group);
			}
			if (in_array($this->resource->group_owner, $group))
			{
				return true;
			}
		}
		else
		{
			if ($this->resource->group_owner)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function types($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->types))
		{
			$this->types = array();

			$rt = new ResourcesType($this->_db);
			if (($types = $rt->getMajorTypes()))
			{
				foreach ($types as $key => $type)
				{
					if (!$type->alias)
					{
						$types[$key]->alias = $rt->normalize($type->type);
					}
				}
				$this->types = $types;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				foreach ($this->types as $type)
				{
					if ($type->id == $idx)
					{
						return $type;
					}
				}
			}
			else if (is_string($idx))
			{
				$idx = trim($idx);
				foreach ($this->types as $type)
				{
					if ($type->alias == $idx)
					{
						return $type;
					}
				}
			}
			$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
			return false;
		}

		return $this->types;
	}

	/**
	 * Get a list of contributors on this resource
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function contributors($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->contributors))
		{
			$this->contributors = array();

			$sql = "SELECT a.authorid, a.name, n.name AS xname, a.ordering, a.organization AS org, n.organization AS xorg, a.role, n.uidNumber AS id, n.givenName, n.middleName, n.surname
					FROM #__author_assoc AS a 
					LEFT JOIN #__xprofiles AS n ON n.uidNumber=a.authorid
					WHERE a.subtable='resources' 
					AND a.subid=" . $this->resource->id . " 
					ORDER BY ordering, surname, givenName, middleName";

			$this->_db->setQuery($sql);
			if (($results = $this->_db->loadObjectList()))
			{
				$this->contributors = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->contributors[$idx]))
				{
					return $this->contributors[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));
				switch ($idx)
				{
					case 'id':
						$ids = array();
						foreach ($this->contributors as $contributor)
						{
							$ids[] = (int) $contributor->authorid;
						}
						return $ids;
					break;

					case 'name':
						$names = array();
						foreach ($this->contributors as $contributor)
						{
							if ($contributor->surname || $contributor->givenName) 
							{
								$name = stripslashes($contributor->givenName) . ' ';
								if ($contributor->middleName != NULL) 
								{
									$name .= stripslashes($contributor->middleName) . ' ';
								}
								$name .= stripslashes($contributor->surname);
							} 
							else 
							{
								$name = stripslashes($contributor->name);
							}
							$names[] = $name;
						}
						return $names;
					break;

					case 'tool':
						if (!$this->isTool())
						{
							return $this->contributors;
						}
						if (!isset($this->toolauthors))
						{
							$this->toolauthors = array();

							$sql = "SELECT n.uidNumber AS id, t.name AS name, n.name AS xname, n.organization AS xorg, n.givenName, n.givenName AS firstname, n.middleName AS middlename, n.surname, n.surname AS lastname, t.organization AS org, t.*, a.role"
								 . " FROM #__tool_authors AS t JOIN #__xprofiles AS n ON n.uidNumber=t.uid JOIN #__tool_version AS v ON v.id=t.version_id"
								 . " LEFT JOIN #__author_assoc AS a ON a.authorid=t.uid AND a.subtable='resources' AND a.subid=" . $this->resource->id
								 . " WHERE t.toolname='" . $this->resource->alias . "' AND v.state<>3"
								 . " AND t.revision='" . $this->resource->revision . "'"
								 . " ORDER BY t.ordering";
							$this->_db->setQuery($sql);
							if (($cons = $this->_db->loadObjectList())) 
							{
								foreach ($cons as $k => $c)
								{
									if (!$cons[$k]->name) 
									{
										$cons[$k]->name = $cons[$k]->xname;
									}
									if (trim($cons[$k]->org) == '') 
									{
										$cons[$k]->org = $cons[$k]->xorg;
									}
								}
								$this->toolauthors = $cons;
							}
						}
						return $this->toolauthors;
					break;

					default:
						// Roles
						$op = 'is';
						if (substr($idx, 0, 1) == '!')
						{
							$op = 'not';
							$idx = ltrim($idx, '!');
						}

						$res = array();
						foreach ($this->contributors as $contributor)
						{
							switch ($op)
							{
								case 'is':
									if ($contributor->role == $idx)
									{
										$res[] = $contributor;
									}
								break;
								
								case 'not':
									if ($contributor->role != $idx)
									{
										$res[] = $contributor;
									}
								break;
							}
						}
						return $res;
					break;
				}
			}
		}

		return $this->contributors;
	}

	/**
	 * Get a list of contributors on this resource
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function children($idx=null, $limit=0, $start=0, $order='ordering')
	{
		if (!$this->exists()) 
		{
			return array();
		}

		$order = strtolower(trim($order));
		if (!isset($this->children) || (isset($this->children) && $order != 'ordering'))
		{
			$this->children = array();

			$sql = "SELECT r.*, r.logical_type AS logicaltype, t.type AS logicaltitle, rt.type AS typetitle, a.grouping, 
							(SELECT n.surname FROM #__xprofiles AS n, #__author_assoc AS aa WHERE n.uidNumber=aa.authorid AND aa.subtable='resources' AND aa.subid=r.id ORDER BY ordering LIMIT 1) AS author"
				 . " FROM #__resource_types AS rt, #__resources AS r"
				 . " JOIN #__resource_assoc AS a ON r.id=a.child_id"
				 . " LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
				 . " WHERE r.published=1 AND a.parent_id=" . $this->resource->id . " AND r.type=rt.id"
				 . " ORDER BY "; //a.ordering ASC, a.grouping ASC";
			switch ($order)
			{
				case 'ordering': $sql .= "a.ordering ASC, a.grouping ASC";    break;
				case 'date':     $sql .= "r.publish_up DESC";                 break;
				case 'title':    $sql .= "r.title ASC, r.publish_up";         break;
				case 'rating':   $sql .= "r.rating DESC, r.times_rated DESC"; break;
				case 'ranking':  $sql .= "r.ranking DESC";                    break;
				case 'author':   $sql .= "author";                            break;
			}
			/*if ($limit != 0) 
			{
				$sql .= " LIMIT " . (int) $start . "," . (int) $limit;
			}*/

			$this->_db->setQuery($sql);
			if (($results = $this->_db->loadObjectList()))
			{
				$this->children = $results;
			}
		}

		/*$args = func_get_args();
		if (count($args) <= 0)
		{
			return $this->children;
		}
		$idx = $args[0];*/

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->children[$idx]))
				{
					return $this->children[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));
				switch ($idx)
				{
					case 'id':
						$ids = array();
						foreach ($this->children as $child)
						{
							$ids[] = (int) $child->id;
						}
						return $ids;
					break;

					case 'standalone':
						/*switch ($order)
						{
							case 'ordering': $key = 'ordering';   break;
							case 'date':     $key = 'publish_up'; break;
							case 'title':    $key = 'title';      break;
							case 'rating':   $key = 'rating';     break;
							case 'ranking':  $key = 'ranking';    break;
							case 'author':   $key = 'author';     break;
						}
						$res = array();*/
						foreach ($this->children as $child)
						{
							if ($child->standalone) 
							{
								$res[] = $child;
							}
						}
						if ($limit != 0) 
						{
							return array_slice($res, $start, $limit);
						}
						return $res;
					break;

					case 'notstandalone':
					case '!standalone':
						$res = array();
						foreach ($this->children as $child)
						{
							if (!$child->standalone) 
							{
								$res[] = $child;
							}
						}
						return $res;
					break;

					default:
						return $this->children;
					break;
				}
			}
		}

		return $this->children;
	}

	/**
	 * Get a list of parents of this resource
	 *   Accepts either a numeric array index
	 *   If index, it'll return the entry matching that index in the list
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function parents($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->parents))
		{
			$this->parents = array();

			$sql = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext, r.footertext, r.type, r.logical_type AS logicaltype, 
					r.created, r.published, r.publish_up, r.path, r.standalone, r.hits, r.rating, r.times_rated, r.params, r.ranking,
					t.type AS logicaltitle, rt.type AS typetitle 
					FROM #__resource_types AS rt, #__resources AS r 
					JOIN #__resource_assoc AS a ON r.id=a.parent_id 
					LEFT JOIN #__resource_types AS t ON r.logical_type=t.id 
					WHERE r.published=1 AND a.child_id=" . $this->resource->id . " AND r.type=rt.id 
					ORDER BY a.ordering, a.grouping";
			$this->_db->setQuery($sql);
			if (($results = $this->_db->loadObjectList()))
			{
				$this->parents = $results;
			}
		}

		if ($idx !== null && is_numeric($idx))
		{
			if (isset($this->parents[$idx]))
			{
				return $this->parents[$idx];
			}
			else
			{
				$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
				return false;
			}
		}

		return $this->parents;
	}

	/**
	 * Get a list of tags on this resource
	 *   Accepts either a numeric array index or a string [id, raw]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or raw tags
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function tags($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->tags))
		{
			$this->tags = array();

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');

			$rt = new ResourcesTags($this->_db);
			if (($results = $rt->getTags($this->resource->id, 0, 0, $this->access('manage')))) // get_tags_on_object
			{
				$this->tags = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->tags[$idx]))
				{
					return $this->tags[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_string($idx))
			{
				switch (strtolower($idx))
				{
					case 'id':
						$ids = array();
						foreach ($this->tags as $tag)
						{
							$ids[] = (int) $tag->id;
						}
						return $ids;
					break;

					case 'raw':
						$raw = array();
						foreach ($this->tags as $tag)
						{
							$raw[] = stripslashes($tag->raw_tag);
						}
						return $raw;
					break;

					default:
						return $this->tags;
					break;
				}
			}
		}

		return $this->tags;
	}

	/**
	 * Get citations on a resource
	 *   Accepts a numeric array index
	 * 
	 * @param      integer $idx Index value
	 * @return     array
	 */
	public function citations($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->citations))
		{
			$this->citations = array();

			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

			$cc = new CitationsCitation($this->_db);

			if ($results = $cc->getCitations('resource', $this->resource->id))
			{
				$this->citations = $results;
			}
		}

		if ($idx !== null && is_numeric($idx))
		{
			if (isset($this->citations[$idx]))
			{
				return $this->citations[$idx];
			}
			else
			{
				$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
				return false;
			}
		}

		return $this->citations;
	}

	/**
	 * Get a list of parents of this resource
	 *   Accepts either a numeric array index
	 *   If index, it'll return the entry matching that index in the list
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function revisions($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->revisions))
		{
			$this->revisions = array();

			$alltools = array();
			$tv = new ToolVersion($this->_db);
			$tv->getToolVersions('', $alltools, $this->resource->alias);

			if ($alltools) 
			{
				$this->revisions = $alltools;
			}
		}

		if ($idx !== null && is_numeric($idx))
		{
			if (isset($this->revisions[$idx]))
			{
				return $this->revisions[$idx];
			}
			else if (is_string($idx))
			{
				switch (strtolower($idx))
				{
					case 'current':
						$curtool = null;
						foreach ($this->revisions as $tool)
						{
							// Current version
							if ($tool->state == 1 
							 && (count($this->revisions) == 1 || (count($this->revisions) > 1 && $this->revisions[1]->version == $tool->version)))
							{
								$curtool = $tool;
								break; // No need to go further
							}
						}
						return $curtool;
					break;

					case 'dev':
						$devtool = null;
						foreach ($this->revisions as $tool)
						{
							// Current version
							if ($tool->state == 3) 
							{
								$devtool = $tool;
								break; // No need to go further
							}
						}
						return $devtool;
					break;

					default:
						$rtool = null;
						foreach ($this->revisions as $tool)
						{
							if ($tool->revision == $idx)
							{
								$rtool = $tool;
								break; // No need to go further
							}
						}
						return $rtool;
					break;
				}
			}
		}

		return $this->revisions;
	}
}
