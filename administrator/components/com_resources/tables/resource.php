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
 * Table class for resources
 */
class ResourcesResource extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id               = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $title            = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $type             = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $logical_type     = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $introtext        = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $fulltxt         = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $footertext       = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created          = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by       = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modified         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by      = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $published        = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_up       = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_down     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $access           = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $hits             = NULL;

	/**
	 * varchar(200)
	 * 
	 * @var string
	 */
	var $path             = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $checked_out      = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $checked_out_time = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $standalone       = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $group_owner      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $group_access     = NULL;

	/**
	 * decimal(2,1)
	 * 
	 * @var integer
	 */
	var $rating           = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $times_rated      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params           = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $attribs          = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $alias            = NULL;

	/**
	 * float
	 * 
	 * @var integer
	 */
	var $ranking          = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resources', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Resource alias
	 * @return     boolean True on success
	 */
	public function load($oid=NULL)
	{
		if ($oid === NULL) 
		{
			return false;
		}
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE `alias`=" . $this->_db->Quote($oid));
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
	 * Load a record by alias and bind to $this
	 * 
	 * @param      string $oid Resource alias
	 * @return     boolean True on success
	 */
	public function loadAlias($oid=NULL)
	{
		return $this->load($oid);
	}

	/**
	 * Load a record by alias and bind to $this
	 * 
	 * @param      string $oid Resource alias
	 * @return     boolean True on success
	 */
	public function loadByFile($oid=NULL, $parent_id=null)
	{
		if ($oid === NULL || $parent_id === NULL) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT r.id FROM $this->_tbl AS r LEFT JOIN #__resource_assoc AS a ON a.child_id=r.id WHERE (r.`path`=" . $this->_db->Quote($oid) . " OR r.`path` LIKE '%/" . $this->_db->getEscaped($oid) . "') AND r.`standalone`=0 AND a.parent_id=" . $this->_db->Quote($parent_id));
		if ($result = $this->_db->loadResult()) 
		{
			return true;
		} 
		return false;
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '') 
		{
			$this->setError('Your Resource must contain a title.');
			return false;
		}
		return true;
	}

	/**
	 * Get the title of a resource type
	 * 
	 * @param      integer $which Get type or logical type
	 * @return     string
	 */
	public function getTypeTitle($which=0)
	{
		if ($which) 
		{
			$type = $this->logical_type;
		} 
		else 
		{
			$type = $this->type;
		}
		$this->_db->setQuery("SELECT type FROM #__resource_types WHERE id=" . $this->_db->Quote($type));
		$title = $this->_db->loadResult();
		return ($title) ? $title : '';
	}

	/**
	 * Get the groups allowed to access a resource
	 * 
	 * @return     array
	 */
	public function getGroups()
	{
		if ($this->group_access != '') 
		{
			$this->group_access = trim($this->group_access);
			$this->group_access = substr($this->group_access, 1, (strlen($this->group_access)-2));
			$allowedgroups = explode(';', $this->group_access);
		} 
		else 
		{
			$allowedgroups = array();
		}

		if (!empty($this->group_owner)) 
		{
			$allowedgroups[] = $this->group_owner;
		}

		return $allowedgroups;
	}

	/**
	 * Calculate the rating for a resource
	 * 
	 * @return     void
	 */
	public function calculateRating()
	{
		$this->_db->setQuery("SELECT rating FROM #__resource_ratings WHERE resource_id=". $this->_db->Quote($this->id));
		$ratings = $this->_db->loadObjectList();

		$totalcount = count($ratings);
		$totalvalue = 0;

		// Add the ratings up
		foreach ($ratings as $item)
		{
			$totalvalue = $totalvalue + $item->rating;
		}

		// Find the average of all ratings
		$newrating = ($totalcount > 0) ? $totalvalue / $totalcount : 0;

		// Round to the nearest half
		$newrating = ($newrating > 0) ? round($newrating*2)/2 : 0;

		// Update page with new rating
		$this->rating = $newrating;
		$this->times_rated = $totalcount;
	}

	/**
	 * Update the rating for a record
	 * 
	 * @return     boolean True on success
	 */
	public function updateRating()
	{
		$this->_db->setQuery("UPDATE $this->_tbl SET rating=" . $this->_db->Quote($this->rating) . ", times_rated=" . $this->_db->Quote($this->times_rated) . " WHERE id=" . $this->_db->Quote($this->id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete records associated with a resource
	 * 
	 * @param      integer $id Resource ID
	 * @return     boolean True on success
	 */
	public function deleteExistence($id=NULL)
	{
		if (!$id) 
		{
			$id = $this->id;
		}

		// Delete child associations
		$this->_db->setQuery("DELETE FROM #__resource_assoc WHERE child_id=" . $this->_db->Quote($id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Delete parent associations
		$this->_db->setQuery("DELETE FROM #__resource_assoc WHERE parent_id=" . $this->_db->Quote($id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Delete tag associations
		$this->_db->setQuery("DELETE FROM #__tags_object WHERE tbl='resources' AND objectid=" . $this->_db->Quote($id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Delete ratings
		$this->_db->setQuery("DELETE FROM #__resource_ratings WHERE resource_id=" . $this->_db->Quote($id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$juser =& JFactory::getUser();
		$now = date('Y-m-d H:i:s', time());

		$query  = "";
		if (isset($filters['tag']) && $filters['tag'] != '') 
		{
			$query .= "FROM #__tags_object AS RTA ";
			$query .= "INNER JOIN #__tags AS TA ON RTA.tagid = TA.id AND RTA.tbl='resources', $this->_tbl AS C ";
		} 
		else 
		{
			$query .= "FROM $this->_tbl AS C ";
		}
		$query .= "LEFT JOIN #__resource_types AS t ON C.type=t.id ";
		$query .= "LEFT JOIN #__resource_types AS lt ON C.logical_type=lt.id ";
		
		if (isset($filters['type']) && $filters['type'] == 'tools' && isset($filters['toolState'])) 
		{
			$query .= "LEFT JOIN #__tool AS T ON C.alias=T.toolname ";
		}
		
		$query .= "WHERE C.published=1 AND C.standalone=1 ";
		if (isset($filters['type']) && $filters['type'] != '') 
		{
			if ($filters['type'] == 'nontools') 
			{
				$query .= "AND C.type!=7 ";
			} 
			else 
			{
				if ($filters['type'] == 'tools') 
				{
					$filters['type'] = 7;
				}
				$query .= "AND C.type=" . $this->_db->Quote($filters['type']) . " ";
				
				if (isset($filters['toolState'])) 
				{
					$query .= "AND T.state=" . $this->_db->Quote($filters['toolState']) . " ";
				}
			}
		} 
		else 
		{
			$query .= "AND C.type!=8 ";
		}
		if (isset($filters['minranking']) && $filters['minranking'] != '' && $filters['minranking'] > 0) 
		{
			$query .= "AND C.ranking > " . $this->_db->Quote($filters['minranking']) . " ";
		}
		$query .= "AND (C.publish_up = '0000-00-00 00:00:00' OR C.publish_up <= " . $this->_db->Quote($now) . ") ";
		$query .= "AND (C.publish_down = '0000-00-00 00:00:00' OR C.publish_down >= " . $this->_db->Quote($now) . ") AND ";
		if (isset($filters['access']) && $filters['access'] == 'public') 
		{
			$query .= "(C.access=0 OR C.access=3) ";
		} 
		else if (!$juser->get('guest')) 
		{
			ximport('Hubzero_User_Profile');
			$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
			$xgroups = (is_object($profile)) ? $profile->getGroups('all') : array();
			if ($xgroups != '') 
			{
				$usersgroups = $this->getUsersGroups($xgroups);
				if (count($usersgroups) > 1) 
				{
					$groups = implode("','", $usersgroups);
				} 
				else 
				{
					$groups = count($usersgroups) ? $usersgroups[0] : '';
				}
				$query .= "(C.access=0 OR C.access=1 OR C.access=3 OR (C.access=4 AND (C.group_owner IN ('" . $groups . "') ";
				foreach ($usersgroups as $group)
				{
					$query .= " OR C.group_access LIKE '%;" . $this->_db->getEscaped($group) . ";%'";
				}
				$query .= "))) ";
			} 
			else 
			{
				$query .= "(C.access=0 OR C.access=1 OR C.access=3) ";
			}
		} 
		else 
		{
			$query .= "(C.access=0 OR C.access=3) ";
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
				$words = array();
				$ws = explode(' ', $filters['search']);
				foreach ($ws as $w)
				{
					$w = trim($w);
					if (strlen($w) > 2) 
					{
						$words[] = $w;
					}
				}
				$text = implode(' +', $words);
				$text = addslashes($text);
				$text2 = str_replace('+', '', $text);

				$query .= "AND ((MATCH(C.title) AGAINST ('+$text -\"$text2\"') > 0) OR"
						 //. " (MATCH(au.givenName,au.surname) AGAINST ('+$text -\"$text2\"') > 0) OR "
						 . " (MATCH(C.introtext,C.fulltxt) AGAINST ('+$text -\"$text2\"') > 0)) ";
		}
		if (isset($filters['tag']) && $filters['tag'] != '') 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
			$tagging = new ResourcesTags($this->_db);
			$tags = $tagging->_parse_tags($filters['tag']);

			$query .= "AND RTA.objectid=C.id AND TA.tag IN ('" . implode("','", $tags) . "')"; //" OR TA.alias IN ('" . implode("','", $tags) . "'))";
			$query .= " GROUP BY C.id HAVING uniques=" . count($tags);
		}
		$query .= " ORDER BY ";
		if (isset($filters['sortby'])) 
		{
			switch ($filters['sortby'])
			{
				case 'date':
				case 'date_published':   $query .= 'publish_up DESC';      break;
				case 'date_created':    $query .= 'created DESC';          break;
				case 'date_modified':    $query .= 'modified DESC';        break;
				case 'title':   $query .= 'title ASC, publish_up';         break;
				case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
				case 'ranking': $query .= "ranking DESC";                  break;
				case 'random':  $query .= "RAND()";                        break;
			}
		}

		return $query;
	}

	/**
	 * Get a list of group aliases for a user where group membership is confirmed
	 * 
	 * @param      array $groups User's groups
	 * @return     array
	 */
	public function getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) 
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) 
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to build query from
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array(), $admin=false)
	{
		$query = $this->buildQuery($filters, $admin);

		$sql  = "SELECT C.id";
		$sql .= (isset($filters['tag']) && $filters['tag'] != '') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " . $query : " " . $query;

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to build query from
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array(), $admin=false)
	{
		$sql  = "SELECT C.id, C.title, C.type, C.introtext, C.fulltxt, C.created, C.created_by, C.modified, C.published, C.publish_up, C.standalone, C.access, C.hits, C.rating, C.times_rated, C.params, C.alias, C.ranking, t.type AS typetitle, lt.type AS logicaltitle";
		$sql .= (isset($filters['tag']) && $filters['tag'] != '') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$sql .= $this->buildQuery($filters, $admin);
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query based off of filters
	 * Used by plugins only
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildPluginQuery($filters=array())
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		$rt = new ResourcesType($database);

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$searchquery = $filters['search'];
			$phrases = $searchquery->searchPhrases;
		}
		if (isset($filters['select']) && $filters['select'] == 'count') 
		{
			if (isset($filters['tags'])) 
			{
				$query = "SELECT count(f.id) FROM (SELECT r.id, COUNT(DISTINCT t.tagid) AS uniques ";
			} 
			else 
			{
				$query = "SELECT count(DISTINCT r.id) ";
			}
		} 
		else 
		{
			$query = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext AS itext, r.fulltxt AS ftext, r.published AS state, r.created, r.created_by, r.modified, r.publish_up, r.params, 
					CONCAT('index.php?option=com_resources&id=', r.id) AS href, 'resources' AS section, rt.type AS area, r.type AS category, r.rating, r.times_rated, r.ranking, r.access ";
			if (isset($filters['tags'])) 
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			if (isset($filters['search']) && $filters['search'] != '') 
			{
				if (!empty($phrases)) 
				{
					$exactphrase = addslashes('"' . $phrases[0] . '"');
					$query .= ", ("
							//. "  MATCH(r.introtext,r.fulltxt) AGAINST ('$exactphrase' IN BOOLEAN MODE) + r.ranking +"
							. "  MATCH(r.introtext,r.fulltxt) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  MATCH(au.givenName,au.surname) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  MATCH(r.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
							. ") AS relevance ";
				} 
				else 
				{
					$words = array();
					if (count($searchquery->searchWords) > 0) 
					{
						$ws = $searchquery->searchWords;
						foreach ($ws as $w)
						{
							if (strlen($w) > 2) 
							{
								$words[] = $w;
							}
						}
					}
					$text = implode(' +', $words);
					$text = addslashes($text);

					$text2 = str_replace('+', '', $text);

					$query .= ", ("
							//. "  MATCH(r.introtext,r.fulltxt) AGAINST ('+$text -\"$text2\"') + r.ranking +"
							. "  MATCH(r.introtext,r.fulltxt) AGAINST ('+$text -\"$text2\"') +"
							. "  MATCH(au.givenName,au.surname) AGAINST ('+$text -\"$text2\"') +"
							. "  MATCH(r.title) AGAINST ('+$text -\"$text2\"') + "
							//. "  CASE WHEN LOWER(r.title) LIKE '%$text2%' THEN 5 ELSE 0 END +"
							//. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND LOWER(r.title) NOT LIKE '%lecture%') THEN 10 ELSE 0 END +"
							. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%') THEN 10 ELSE 0 END +"
							//. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND LOWER(r.title) NOT LIKE '%lecture%') THEN 10 ELSE 0 END"
							. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND r.type=6) THEN 50 ELSE 0 END"
							//. "  CASE WHEN (SELECT COUNT(*) FROM #__resource_assoc AS ras WHERE ras.child_id=r.id) > 0 THEN -95 ELSE 0 END"
							. ") AS relevance ";
				}
			}
			if (isset($filters['sortby']) && ($filters['sortby'] == 'usage' || $filters['sortby'] == 'users')) 
			{
				$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=r.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
			}
			if (isset($filters['sortby']) && $filters['sortby'] == 'jobs') 
			{
				$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=r.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
			}
		}
		$query .= "FROM $this->_tbl AS r ";
		$query .= "LEFT JOIN " . $rt->getTableName() . " AS rt ON r.type=rt.id ";
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$query .= "LEFT JOIN #__author_assoc AS aus ON aus.subid=r.id AND aus.subtable='resources' 
						LEFT JOIN #__xprofiles AS au ON aus.authorid=au.uidNumber ";
		}
		if (isset($filters['author'])) 
		{
			$query .= "LEFT JOIN #__author_assoc AS aa ON aa.subid=r.id AND aa.subtable='resources' ";
		}
		if (isset($filters['favorite'])) 
		{
			$query .= ", #__xfavorites AS xf ";
		}
		if (isset($filters['tag'])) 
		{
			$query .= ", #__tags_object AS t, #__tags AS tg ";
		}
		if (isset($filters['tags'])) 
		{
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE r.standalone=1 ";
		if ($juser->get('guest') || (isset($filters['authorized']) && !$filters['authorized'])) 
		{
			$query .= "AND r.published=1 ";
		}
		else
		{
			$query .= "AND r.published!=4 ";
		}
		if (isset($filters['author'])) 
		{
			$query .= "AND (aa.authorid=" . $this->_db->Quote(intval($filters['author'])) . ") "; // "' OR r.created_by=". $filters['author'] .") "; - SS - globalHub #622 - Mourad was the creator of a bunch of resources he was not listed as a contributor to in jos_author_assoc, making his profile page look wildly incorrect
		}
		if (isset($filters['favorite'])) 
		{
			$query .= "AND xf.uid=" . $this->_db->Quote($filters['favorite']) . " AND r.id=xf.oid AND xf.tbl='resources' ";
		}
		if (isset($filters['tag'])) {
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid=tg.id AND (tg.tag='" . $this->_db->getEscaped($filters['tag']) . "' OR tg.alias='" . $this->_db->getEscaped($filters['tag']) . "') ";
		}
		if (isset($filters['tags'])) 
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid IN (" . $ids . ") ";
		}
		if (isset($filters['type']) && $filters['type'] != '') 
		{
			$query .= "AND r.type=" . $this->_db->Quote($filters['type']) . " ";
		}

		if (isset($filters['group']) && $filters['group'] != '') 
		{
			$query .= "AND (r.group_owner='" . $this->_db->getEscaped($filters['group']) . "' OR r.group_access LIKE '%;" . $this->_db->getEscaped($filters['group']) . ";%') ";
			if (!$filters['authorized']) 
			{
				switch ($filters['access'])
				{
					case 'public':    $query .= "AND r.access = 0 ";  break;
					case 'protected': $query .= "AND r.access = 3 ";  break;
					case 'private':
					case 'all':
					default:          $query .= "AND r.access != 4 "; break;
				}
			} 
			else 
			{
				switch ($filters['access'])
				{
					case 'public':    $query .= "AND r.access = 0 ";  break;
					case 'protected': $query .= "AND r.access = 3 ";  break;
					case 'private':   $query .= "AND r.access = 4 ";  break;
					case 'all':
					default:          $query .= ""; break;
				}
			}
		} 
		else 
		{
			if (!$juser->get('guest')) 
			{
				if (!isset($filters['usergroups'])) 
				{
					ximport('Hubzero_User_Profile');
					$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
					$xgroups = $profile->getGroups('all');
				} 
				else 
				{
					$xgroups = $filters['usergroups'];
				}
				if (isset($filters['author'])) 
				{
					//$query .= "AND (r.access=0 OR r.access=1 OR r.access=3) ";
				} 
				else if ($xgroups != '') 
				{
					$usersgroups = $this->getUsersGroups($xgroups);
					if (count($usersgroups) > 1) 
					{
						$groups = implode("','", $usersgroups);
					} 
					else 
					{
						$groups = count($usersgroups) ? $usersgroups[0] : '';
					}
					$query .= "AND (r.access=0 OR r.access=1 OR r.access=3 OR (r.access=4 AND (r.group_owner IN ('" . $groups . "') ";
					$query .= " OR r.created_by=" . $this->_db->Quote($juser->get('id'));
					$query .= "))) ";
				} 
				else 
				{
					$query .= "AND (r.access=0 OR r.access=1 OR r.access=3) ";
				}
			} 
			else 
			{
				$query .= "AND (r.access=0 OR r.access=3) ";
			}
		}

		if (isset($filters['now'])) 
		{
			$query .= "AND (r.publish_up = '0000-00-00 00:00:00' OR r.publish_up <= " . $this->_db->Quote($filters['now']) . ") ";
			$query .= "AND (r.publish_down = '0000-00-00 00:00:00' OR r.publish_down >= " . $this->_db->Quote($filters['now']) . ") ";
		}
		if (isset($filters['startdate'])) 
		{
			$query .= "AND r.publish_up > " . $this->_db->Quote($filters['startdate']) . " ";
		}
		if (isset($filters['enddate'])) 
		{
			$query .= "AND r.publish_up < " . $this->_db->Quote($filters['enddate']) . " ";
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			if (!empty($phrases)) 
			{
				$exactphrase = addslashes('"' . $phrases[0] . '"');
				$query .= "AND ((MATCH(r.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
						 . " (MATCH(au.givenName,au.surname) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR "
						 . " (MATCH(r.introtext,r.fulltxt) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0)) ";
			} 
			else 
			{
				$words = array();
				if (count($searchquery->searchWords) > 0) 
				{
					$ws = $searchquery->searchWords;
					foreach ($ws as $w)
					{
						if (strlen($w) > 2) 
						{
							$words[] = $w;
						}
					}
				}
				$text = implode(' +', $words);
				$text = addslashes($text);
				$text2 = str_replace('+', '', $text);

				$query .= "AND ((MATCH(r.title) AGAINST ('+$text -\"$text2\"') > 0) OR"
						 . " (MATCH(au.givenName,au.surname) AGAINST ('+$text -\"$text2\"') > 0) OR "
						 . " (MATCH(r.introtext,r.fulltxt) AGAINST ('+$text -\"$text2\"') > 0)) ";
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') 
			{
				$query .= "GROUP BY r.id ";
			}
		}
		if (isset($filters['tags'])) 
		{
			$query .= " GROUP BY r.id HAVING uniques=" . count($filters['tags']) . " ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count') 
		{
			if (isset($filters['sortby'])) 
			{
				if (isset($filters['groupby'])) 
				{
					$query .= "GROUP BY r.id ";
				}
				$query .= "ORDER BY ";
				switch ($filters['sortby'])
				{
					case 'date':    $query .= 'publish_up DESC';               break;
					case 'title':   $query .= 'title ASC, publish_up';         break;
					case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
					case 'ranking': $query .= "ranking DESC";                  break;
					case 'relevance': $query .= "relevance DESC";              break;
					case 'users':
					case 'usage':   $query .= "users DESC";              break;
					case 'jobs':   $query .= "jobs DESC";              break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') 
			{
				$query .= " LIMIT " . (int) $filters['limitstart'] . "," . (int) $filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count') 
		{
			if (isset($filters['tags'])) 
			{
				$query .= ") AS f";
			}
		}

		return $query;
	}

	/**
	 * Get record count
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getItemCount($filters=array())
	{
		$sql = "SELECT count(*) 
				FROM $this->_tbl AS r 
				WHERE r.standalone=1";
		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			$sql .= " AND r.published=" . $this->_db->Quote($filters['status']);
		}
		if (isset($filters['type']) && $filters['type'])
		{
			$sql .= " AND r.type=" . $this->_db->Quote($filters['type']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$sql .= " AND (LOWER(r.title) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%'";
			if (is_numeric($filters['search']))
			{
				$sql .= " OR r.id=" . $this->_db->getEscaped(strtolower($filters['search']));
			}
			$sql .= ")";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getItems($filters=array())
	{
		$gname = 'g.title';
		$gtbl = '#__viewlevels AS g ON g.id = (r.access + 1)';
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$gname = 'g.name';
			$gtbl = '#__groups AS g ON g.id = r.access';
		}
		$sql = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, CASE WHEN r.modified != '0000-00-00 00:00:00' THEN r.modified ELSE r.created END AS modified, r.access, 
				r.published, r.publish_up, r.publish_down, r.checked_out, r.checked_out_time, r.params, u.name AS editor, 
				$gname AS groupname, t.type AS typetitle, 
				(SELECT count(*) FROM #__resource_assoc AS ra WHERE ra.parent_id=r.id) AS children 
				FROM $this->_tbl AS r 
				LEFT JOIN #__users AS u ON u.id = r.checked_out 
				LEFT JOIN $gtbl
				LEFT JOIN #__resource_types AS t ON r.type=t.id
				WHERE r.standalone=1";
		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			$sql .= " AND r.published=" . $this->_db->Quote($filters['status']);
		}
		if (isset($filters['type']) && $filters['type'])
		{
			$sql .= " AND r.type=" . $this->_db->Quote($filters['type']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$sql .= " AND (LOWER(r.title) LIKE '%" . $this->_db->getEscaped($filters['search']) . "%'";
			if (is_numeric($filters['search']))
			{
				$sql .= " OR r.id=" . $this->_db->Quote($filters['search']);
			}
			$sql .= ")";
		}
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
			$filters['sort_Dir'] = 'DESC';
		}
		$sql .= " ORDER BY " . $filters['sort'] . " " . strtoupper($filters['sort_Dir']);
		if (isset($filters['limit']) && $filters['limit'] && $filters['limit'] != 'all')
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count of child resources
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getItemChildrenCount($filters=array())
	{
		if (isset($filters['parent_id']) && $filters['parent_id'] > 0)
		{
			$sql = "SELECT count(*) 
					FROM $this->_tbl AS r, 
					#__resource_assoc AS ra 
					WHERE ra.child_id=r.id AND ra.parent_id=" . $this->_db->Quote($filters['parent_id']);
		}
		else
		{
			$sql = "SELECT count(*) 
					FROM $this->_tbl AS r 
					WHERE standalone!=1 
					AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		}

		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			$sql .= " AND r.published=" . $filters['status'];
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$sql .= " AND (LOWER(r.title) LIKE '%" . $this->_db->getEscaped($filters['search']) . "%'";
			if (is_numeric($filters['search']))
			{
				$sql .= " OR r.id=" . $this->_db->Quote($filters['search']);
			}
			$sql .= ")";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get records of child resources
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getItemChildren($filters=array())
	{
		$gname = 'g.title';
		$gtbl = '#__viewlevels AS g ON g.id = (r.access + 1)';
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$gname = 'g.name';
			$gtbl = '#__groups AS g ON g.id = r.access';
		}

		if (isset($filters['parent_id']) && $filters['parent_id'] > 0)
		{
			$sql  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
						r.publish_up, r.publish_down, r.path, r.checked_out, r.checked_out_time, r.standalone, u.name AS editor, $gname AS groupname, 
						lt.type AS logicaltitle, ra.*, gt.type as grouptitle, t.type AS typetitle, NULL as position, 
						(SELECT count(*) FROM #__resource_assoc AS rraa WHERE rraa.child_id=r.id AND rraa.parent_id!=" . $this->_db->Quote($filters['parent_id']) . ") AS multiuse
						FROM #__resource_types AS t, 
						$this->_tbl AS r
						LEFT JOIN #__users AS u ON u.id = r.checked_out
						LEFT JOIN $gtbl
						LEFT JOIN #__resource_types AS lt ON lt.id=r.logical_type, 
						#__resource_assoc AS ra 
						LEFT JOIN #__resource_types AS gt ON gt.id=ra.grouping
						WHERE r.type=t.id AND ra.child_id=r.id AND ra.parent_id=" . $this->_db->Quote($filters['parent_id']);
		}
		else
		{
			$sql  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
						r.publish_up, r.publish_down, r.checked_out, r.checked_out_time, r.path, r.standalone, u.name AS editor, $gname AS groupname, 
						t.type AS typetitle, NULL as logicaltitle
						FROM $this->_tbl AS r
						LEFT JOIN #__users AS u ON u.id = r.checked_out
						LEFT JOIN $gtbl
						LEFT JOIN #__resource_types AS t ON t.id=r.type
						WHERE r.standalone!=1 
						AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		}
		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			$sql .= " AND r.published=" . $this->_db->Quote($filters['status']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$sql .= " AND (LOWER(r.title) LIKE '%" . $this->_db->getEscaped($filters['search']) . "%'";
			if (is_numeric($filters['search']))
			{
				$sql .= " OR r.id=" . $this->_db->Quote($filters['search']);
			}
			$sql .= ")";
		}
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
			$filters['sort_Dir'] = 'DESC';
		}
		$sql .= " ORDER BY " . $filters['sort'] . " " . strtoupper($filters['sort_Dir']);
		if (isset($filters['limit']) && $filters['limit'] && $filters['limit'] != 'all')
		{
			$sql .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}

