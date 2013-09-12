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
 * Table class for tags
 */
class TagsTableTag extends JTable
{
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * string(100)
	 * 
	 * @var string
	 */
	var $tag         = NULL;

	/**
	 * string(100)
	 * 
	 * @var string
	 */
	var $raw_tag     = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $description = NULL;

	/**
	 * tinyint(3)
	 * 
	 * @var integer
	 */
	var $admin       = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tags', 'id', $db);
	}

	/**
	 * Load a database row and populate this object with results
	 * Uses unique tag string as identifier
	 * 
	 * @param      string $oid Tag
	 * @return     boolean True if tag found and loaded
	 */
	public function loadTag($oid=NULL)
	{
		if ($oid === NULL) 
		{
			return false;
		}

		$oid = $this->normalize($oid);

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE tag=" . $this->_db->Quote($oid) . " LIMIT 1"); //raw_tag='$oid' OR 
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->_db->setQuery("SELECT t.* FROM $this->_tbl AS t JOIN #__tags_substitute AS s ON s.tag_id=t.id WHERE s.tag=" . $this->_db->Quote($oid) . " LIMIT 1");
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
	 * Delete a tag and associated content
	 * 
	 * @param      integer $oid Tag ID
	 * @return     boolean True on success, false if errors
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = intval($oid);
		}

		$data = new TagsTableTag($this->_db);
		$data->load($this->$k);
		$comment = '';
		if ($data->tag)
		{
			$comment = new stdClass;
			foreach ($this->getProperties() as $key => $property)
			{
				$comment->$key = $property;
			}
			$comment = json_encode($comment);
		}

		$query = 'DELETE FROM #__tags_object WHERE tagid = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query = 'DELETE FROM #__tags_substitute WHERE tag_id = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query = 'DELETE FROM #__tags_group WHERE tagid = ' . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$result = parent::delete($oid);
		if ($result)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');
			$log = new TagsTableLog($this->_db);
			$log->log($oid, 'tag_deleted', $comment);
		}

		return $result;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * @param     boolean $updateNulls If false, null object variables are not updated
	 * @return    null|string null if successful otherwise returns and error message
	 */
	public function store($updateNulls=false)
	{
		$k = $this->_tbl_key;
		if ($this->$k)
		{
			$action = 'tag_edited';
		}
		else
		{
			$action = 'tag_created';
		}

		$result = parent::store($updateNulls);
		if ($result)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');
			$log = new TagsTableLog($this->_db);
			$log->log($this->$k, $action);
		}
		return $result;
	}

	/**
	 * Check if a tag already exists
	 * 
	 * @return     boolean True if tag exists, false if not
	 */
	public function checkExistence()
	{
		// First see if the tag exists.
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE tag=" . $this->_db->Quote($this->tag) . " OR raw_tag=" . $this->_db->Quote($this->raw_tag) . " LIMIT 1");
		$id = $this->_db->loadResult();
		// We have an ID = tag exist
		if ($id > 0) 
		{
			return true;
		}
		// Tag doesn't exist
		return false;
	}

	/**
	 * Get the number of times a tag was used
	 * 
	 * @param      integer $tagid Tag ID
	 * @return     integer
	 */
	public function getUsage($tagid=NULL)
	{
		if (!$tagid) 
		{
			$tagid = $this->id;
		}
		if (!$tagid) 
		{
			return null;
		}

		$to = new TagsTableObject($this->_db);
		return $to->getCount($tagid);
	}

	/**
	 * Get the number of tags on an object
	 * 
	 * @param      integer $tagid    Tag ID
	 * @param      integer $objectid Object ID
	 * @param      string  $tbl      Object type
	 * @return     integer
	 */
	public function getUsageForObject($tagid=null, $objectid=null, $tbl=null)
	{
		$to = new TagsTableObject($this->_db);
		return $to->getCountForObject($tagid, $objectid, $tbl);
	}

	/**
	 * Validate data
	 * 
	 * @return     True if data is valid
	 */
	public function check()
	{
		$this->raw_tag = trim($this->raw_tag);
		if (!$this->raw_tag) 
		{
			$this->setError(JText::_('You must enter a tag.'));
			return false;
		}

		$this->tag = $this->normalize($this->raw_tag);

		return true;
	}

	/**
	 * Normalize a raw tag
	 * Strips all non-alphanumeric characters
	 * 
	 * @param      string $tag Raw tag
	 * @return     string
	 */
	public function normalize($tag)
	{
		//echo htmlentities($tag, ENT_COMPAT, 'UTF-8'); die();
		//echo htmlspecialchars($tag, ENT_COMPAT, 'UTF-8'); die();
		//$tag = utf8_decode($tag);
		$transliterationTable = array(
			'á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 
			'ḃ' => 'b', 'Ḃ' => 'B', 
			'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 
			'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 
			'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 
			'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 
			'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 
			'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 
			'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 
			'ĵ' => 'j', 'Ĵ' => 'J', 
			'ķ' => 'k', 'Ķ' => 'K', 
			'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 
			'ṁ' => 'm', 'Ṁ' => 'M', 
			'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 
			'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 
			'ṗ' => 'p', 'Ṗ' => 'P', 
			'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 
			'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 
			'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 
			'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 
			'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 
			'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 
			'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 
			'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 
			'а' => 'a', 'А' => 'a', 'б' => 'b', 
			'Б' => 'b', 'в' => 'v', 'В' => 'v', 
			'г' => 'g', 'Г' => 'g', 'д' => 'd', 
			'Д' => 'd', 'е' => 'e', 'Е' => 'e', 
			'ё' => 'e', 'Ё' => 'e', 'ж' => 'zh', 
			'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 
			'и' => 'i', 'И' => 'i', 'й' => 'j', 
			'Й' => 'j', 'к' => 'k', 'К' => 'k', 
			'л' => 'l', 'Л' => 'l', 'м' => 'm', 
			'М' => 'm', 'н' => 'n', 'Н' => 'n', 
			'о' => 'o', 'О' => 'o', 'п' => 'p', 
			'П' => 'p', 'р' => 'r', 'Р' => 'r', 
			'с' => 's', 'С' => 's', 'т' => 't', 
			'Т' => 't', 'у' => 'u', 'У' => 'u', 
			'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 
			'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 
			'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 
			'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 
			'ъ' => '', 'Ъ' => '', 'ы' => 'y', 
			'Ы' => 'y', 'ь' => '', 'Ь' => '', 
			'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 
			'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja'
		);
		$tag = str_replace(array_keys($transliterationTable), array_values($transliterationTable), $tag);
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     string SQL
	 */
	public function buildQuery($filters)
	{
		if (isset($filters['count']) && $filters['count']) 
		{
			$query = "SELECT count(*)";
		} 
		else 
		{
			$query = "SELECT " . (isset($filters['search']) && $filters['search'] != '' ? "DISTINCT " : "") . "t.id, t.tag, t.raw_tag, t.admin, 
						(SELECT COUNT(*) FROM #__tags_object AS tt WHERE tt.tagid=t.id) AS total, 
						(SELECT COUNT(*) FROM #__tags_substitute AS s WHERE s.tag_id=t.id) AS substitutes";
		}
		$tj = new TagsTableObject($this->_db);

		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['by']) && $filters['by'] == 'user') 
		{
			$query .= " JOIN " . $tj->getTableName() . " AS tj ON t.id=tj.tagid AND t.raw_tag NOT LIKE 'tool:%' AND t.raw_tag NOT LIKE 'resource:%'";
		}
		else if (isset($filters['scope']) || isset($filters['scope_id'])) 
		{
			$query .= " JOIN " . $tj->getTableName() . " AS tj ON t.`id`=tj.`tagid`";
		}

		$where = array();

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$query .= " LEFT JOIN #__tags_substitute AS sb ON sb.tag_id=t.id";
			// Used to also query using unfiltered search text agains the rawtag and the tag.
			// Figured this was safer
			$where[] = "(LOWER(t.`raw_tag`) LIKE '" . $this->_db->getEscaped($filters['search']) . "%' OR LOWER(sb.`raw_tag`) LIKE '" . $this->_db->getEscaped($filters['search']) . "%')";
		} 
		if (isset($filters['by'])) 
		{
			switch ($filters['by'])
			{
				case 'user':  $where[] = "`admin`=0"; break;
				case 'admin': $where[] = "`admin`=1"; break;
				case 'all':
				default:  break;
			}
		}
		if (isset($filters['tagger_id']) && $filters['tagger_id']) 
		{
			$where[] = "tj.`taggerid`=" . $this->_db->Quote((int) $filters['tagger_id']);
		}
		if (isset($filters['scope']) && $filters['scope']) 
		{
			$where[] = "tj.`tbl`=" . $this->_db->Quote((string) $filters['scope']);
		}
		if (isset($filters['scope_id']) && $filters['scope_id']) 
		{
			$where[] = "tj.`objectid`=" . $this->_db->Quote((int) $filters['scope_id']);
		}
		if (isset($filters['admin']) && $filters['admin'] !== null) 
		{
			$where[] = "t.`admin`=" . $this->_db->Quote((int) $filters['admin']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '') 
		{
			if ($filters['sortby'] == 'total') 
			{
				$query .= " ORDER BY " . $filters['sortby'] . " DESC";
			} 
			else 
			{
				$query .= " ORDER BY t." . $filters['sortby'];
			}
		} 
		else 
		{
			if (isset($filters['sort']) && $filters['sort'] != '' && (!isset($filters['count']) || !$filters['count'])) 
			{
				$filters['sort_Dir'] = (isset($filters['sort_Dir']) && $filters['sort_Dir']) ? $filters['sort_Dir'] : "ASC";
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
			}
			else 
			{
				$query .= " ORDER BY t.raw_tag ASC";
			}
		}

		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') 
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit']  = 0;
		$filters['count']  = true;
		$filters['sortby'] = '';

		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadObjectList();
	}

	/**
	 * Get tags for an object
	 * 
	 * @param      string  $tbl      Object type
	 * @param      integer $state    Object state
	 * @param      integer $objectid Object ID
	 * @return     array
	 */
	public function getCloud($tbl='', $state=0, $objectid=0)
	{
		$tj = new TagsTableObject($this->_db);

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, COUNT(*) as count
				FROM $this->_tbl AS t 
				INNER JOIN " . $tj->getTableName() . " AS rt ON (rt.tagid = t.id) AND rt.tbl=" . $this->_db->Quote($tbl) . " ";
		if (isset($objectid) && $objectid) 
		{
			$sql .= "WHERE rt.objectid=" . $this->_db->Quote($objectid) . " ";
		}
		switch ($state)
		{
			/*case 0:
				$sql .= (isset($objectid) && $objectid) ? "AND (t.state=1 OR t.state=0) " : "WHERE (t.state=1 OR t.state=0) ";
			break;*/
			case 1:
				$sql .= "";
			break;
			case 0:
			default:
				$sql .= (isset($objectid) && $objectid) ? "AND t.admin=0 " : "WHERE t.admin=0 ";
			break;
		}
		$sql .= "GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Search for tags containing the search string
	 * 
	 * @param      array $filters Params for building the query
	 * @return     array
	 */
	public function getAutocomplete($filters=array())
	{
		$query = "SELECT DISTINCT t.id, t.tag, t.raw_tag 
					FROM $this->_tbl AS t 
					LEFT JOIN #__tags_substitute AS s ON s.tag_id=t.id
					WHERE";
		if (isset($filters['admin']) && $filters['admin']) 
		{
			$query .= "";
		} 
		else 
		{
			$query .= " admin=0 AND";
		}
		$query .= " (LOWER(t.raw_tag) LIKE '" . $this->_db->getEscaped($filters['search']) . "%' OR LOWER(s.raw_tag) LIKE '" . $this->_db->getEscaped($filters['search']) . "%') 
					ORDER BY t.raw_tag ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of all tags
	 * 
	 * @param      boolean $authorized Administrator?
	 * @return     array
	 */
	public function getAllTags($authorized=false)
	{
		if (!$authorized) 
		{
			$filter = "WHERE admin=0 ";
		} 
		else 
		{
			$filter = "";
		}

		$query = "SELECT id, tag, raw_tag, admin, COUNT(*) as tcount 
				FROM $this->_tbl $filter 
				GROUP BY raw_tag 
				ORDER BY raw_tag ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the top used tags
	 * 
	 * @param      integer $limit           Number of tags to get
	 * @param      string  $tbl             Object type
	 * @param      string  $order           Sort results by
	 * @param      integer $exclude_private Exclude private objects?
	 * @return     array
	 */
	public function getTopTags($limit=25, $tbl='', $order='tcount DESC', $exclude_private=1)
	{
		$tj = new TagsTableObject($this->_db);

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, tj.tagid, tj.objectid, COUNT(tj.tagid) AS tcount ";
		$sql .= "FROM $this->_tbl AS t  ";
		$sql .= "JOIN " . $tj->getTableName() . " AS tj ON t.id=tj.tagid ";
		if ($exclude_private) 
		{
			$sql .= " LEFT JOIN #__resources AS R ON R.id=tj.objectid AND tj.tbl='resources' ";
			$sql .= " LEFT JOIN #__wiki_page AS P ON P.id=tj.objectid AND tj.tbl='wiki' ";
			$sql .= " LEFT JOIN #__xprofiles AS XP ON XP.uidNumber=tj.objectid AND tj.tbl='xprofiles' ";
		}
		$sql .= "WHERE t.id=tj.tagid AND t.admin=0 ";
		if ($tbl) 
		{
			$sql .= "AND tj.tbl=" . $this->_db->Quote($tbl) . " ";
		} 
		else 
		{
			$sql .= ($exclude_private) ? " AND ((tj.tbl='resources' AND R.access!=4 AND R.published=1) OR (tj.tbl='wiki' AND P.access=0 AND P.state<2) OR (tj.tbl='xprofiles' AND XP.public=0) OR tj.tbl NOT IN ('xprofiles','wiki','resources','wishlist','support')) " : "";
		}
		$sql .= "GROUP BY tagid ";
		$sql .= "ORDER BY $order ";
		$sql .= "LIMIT $limit";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get recently used tags
	 * 
	 * @param      integer $limit Number of tags to find
	 * @param      string  $order Sort results by
	 * @return     array
	 */
	public function getRecentTags($limit=25, $order='taggedon DESC', $exclude_private=1)
	{
		$tj = new TagsTableObject($this->_db);

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, tj.taggedon, COUNT(tj.tagid) AS tcount ";
		$sql .= "FROM $this->_tbl AS t  ";
		$sql .= "JOIN " . $tj->getTableName() . " AS tj ON t.id=tj.tagid AND t.raw_tag NOT LIKE 'tool:%' AND t.raw_tag NOT LIKE 'resource:%' ";
		if ($exclude_private) 
		{
			$sql.= "LEFT JOIN #__resources AS R ON R.id=tj.objectid AND tj.tbl='resources' ";
			$sql.= "LEFT JOIN #__wiki_page AS P ON P.id=tj.objectid AND tj.tbl='wiki' ";
			$sql.= "LEFT JOIN #__xprofiles AS XP ON XP.uidNumber=tj.objectid AND tj.tbl='xprofiles' ";
		}
		$sql.= "WHERE t.id=tj.tagid AND t.admin=0 ";
		if ($exclude_private) 
		{
			$sql .= "AND (
				(tj.tbl='resources' AND R.access!=4) OR 
				(tj.tbl='wiki' AND P.access=0) OR 
				(tj.tbl='xprofiles' AND XP.public=0) OR 
				(tj.tbl NOT IN ('xprofiles', 'wiki', 'resources', 'wishlist', 'support'))
				) ";
		}
		$sql .= "GROUP BY raw_tag
				ORDER BY $order LIMIT " . (int) $limit;

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get tags related to another tag
	 * Relation is based off of number of uses on same objects
	 * 
	 * @param      integer $id    ID of tag to find relations to
	 * @param      integer $limit Number of tags to find
	 * @return     mixed Array of tags if results found, null if not
	 */
	public function getRelatedTags($id=null, $limit=25)
	{
		if (!$id) 
		{
			$id = $this->id;
		}
		if (!$id) 
		{
			return null;
		}

		$this->_db->setQuery("SELECT objectid, tbl FROM #__tags_object WHERE tagid=" . $this->_db->Quote($id));
		$objs = $this->_db->loadObjectList();
		if ($objs) 
		{
			$sql = "SELECT t.* FROM $this->_tbl AS t, #__tags_object AS tg WHERE t.id=tg.tagid AND tg.tagid != " . $this->_db->Quote($id) . " AND t.admin=0 AND (";
			$s = array();
			foreach ($objs as $obj)
			{
				$s[] = "(tg.objectid=" . $this->_db->Quote($obj->objectid) . " AND tg.tbl=" . $this->_db->Quote($obj->tbl) . ")";
			}
			$sql .= implode(" OR ", $s);
			$sql .= ") GROUP BY t.id LIMIT " . $limit;

			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		} 
		else 
		{
			return null;
		}
	}

	/**
	 * Get all the substitutions for this tag
	 * 
	 * @param      integer $tag_id   Tag ID
	 * @param      boolean $asString Return results as string?
	 * @param      integer $offset   Record offset
	 * @param      integer $limit    Number of records to return (returns all if less than 1)
	 * @return     mixed Array by default, string if $asString is set to true
	 */
	public function getSubstitutions($tag_id=null, $asString=false, $offset=0, $limit=0)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->id;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'substitute.php');

		$subs = new TagsTableSubstitute($this->_db);
		if ($asString)
		{
			return $subs->getRecordString($tag_id, $offset, $limit);
		}
		return $subs->getRecords($tag_id, $offset, $limit);
	}

	/**
	 * Get all the substitutions for this tag
	 * 
	 * @param      integer $tag_id   Tag ID
	 * @param      boolean $asString Return results as string?
	 * @param      integer $offset   Record offset
	 * @param      integer $limit    Number of records to return (returns all if less than 1)
	 * @return     mixed Array by default, string if $asString is set to true
	 */
	public function saveSubstitutions($tag_string='', $tag_id=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->id;
		}
		if (!$tag_id) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'substitute.php');

		$ts = new TagsTableSubstitute($this->_db);
		$subs = $ts->getRecords($tag_id);
		if (!$subs)
		{
			$subs = array();
		}

		$raw_tags = explode(',', trim($tag_string));

		$tags = array();
		foreach ($raw_tags as $raw_tag)
		{
			$nrm = $this->normalize($raw_tag);
			$tags[] = $nrm;

			if (isset($subs[$nrm]))
			{
				continue; // Substitution already exists
			}

			$sub = new TagsTableSubstitute($this->_db);
			$sub->raw_tag = trim($raw_tag);
			$sub->tag_id  = $tag_id;
			if ($sub->check())
			{
				if (!$sub->store())
				{
					$this->setError($sub->getError());
				}
			}
		}

		// Build list of tags from old list not found in new list and delete them
		$remove = array();
		foreach ($subs as $key => $sub)
		{
			if (!in_array($key, $tags)) 
			{
				$remove[] = $key;
			}
		}
		$ts = new TagsTableSubstitute($this->_db);
		if (count($remove) > 0)
		{
			if (!$ts->removeForTag($tag_id, $remove)) 
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// Get all possibly existing tags that are now aliases
		$sql = "SELECT t.id FROM $this->_tbl AS t WHERE t.tag IN ('" . implode("','", $tags) . "')";
		$this->_db->setQuery($sql);

		if (($ids = $this->_db->loadObjectList()))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'object.php');

			$to = new TagsTableObject($this->_db);

			// Move associations on tag and delete tag
			foreach ($ids as $id)
			{
				if ($tag_id != $id->id) 
				{
					// Get all the associations to this tag
					// Loop through the associations and link them to a different tag
					$to->moveObjects($id->id, $tag_id);

					// Get all the substitutions to this tag
					// Loop through the records and link them to a different tag
					$ts->moveSubstitutes($id->id, $tag_id);

					// Delete the tag
					$tag = new TagsTableTag($this->_db);
					$tag->delete($id->id);
				}
			}
		}

		return true;
	}
}

