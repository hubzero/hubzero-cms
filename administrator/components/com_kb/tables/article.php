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
 * Table class for knowledge base articles
 */
class KbArticle extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id           = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $title        = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params       = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $fulltxt     = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by   = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modified     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $checked_out  = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $checked_out_time = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $state        = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $access       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $hits         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $version      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $section      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $category     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $helpful      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $nothelpful   = NULL;

	/**
	 * varchar(200)
	 * 
	 * @var string
	 */
	var $alias        = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->id = intval($this->id);

		if (trim($this->title) == '') 
		{
			$this->setError(JText::_('COM_KB_ERROR_EMPTY_TITLE'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

		$juser = JFactory::getUser();
		if (!$this->id)
		{
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}
		else 
		{
			$this->modified = date('Y-m-d H:i:s', time());
			$this->modified_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_kb.article.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 *
	 * @return  integer  The id of the asset's parent
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db		= $this->getDbo();

		if ($assetId === null) 
		{
			// Build the query to get the asset id for the parent category.
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_kb'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult()) 
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId) 
		{
			return $assetId;
		} 
		else 
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Short description for 'store'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function store()
	{
		if (empty($this->modified)) 
		{
			$this->modified = $this->created;
		}
		$row->version++;

		return parent::store();
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string  $oid Alias
	 * @param      integer $cat Section ID
	 * @return     boolean True upon success, False if errors
	 */
	public function loadAlias($oid=NULL, $cat=NULL)
	{
		if (empty($oid)) 
		{
			return false;
		}
		$sql  = "SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid);
		$sql .= ($cat) ? " AND section=" . $this->_db->Quote($cat) : '';
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

	/**
	 * Get articles for a category
	 * 
	 * @param      integer $noauth   Restrict by article authorizartion level
	 * @param      integer $section  Section ID
	 * @param      integer $category Category ID
	 * @param      string  $access   Access
	 * @return     array
	 */
	public function getCategoryArticles($noauth, $section, $category, $access)
	{
		$juser =& JFactory::getUser();

		$query = "SELECT a.id, a.title, a.created, a.created_by, a.access, a.hits, a.section, a.category, a.helpful, a.nothelpful, a.alias, c.alias AS calias"
				. " FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq_categories AS c ON c.id = a.category"
				. " WHERE a.section=" . $this->_db->Quote($section) . " AND a.category=" . $this->_db->Quote($category) . " AND a.state=1"
				. ($noauth ? " AND a.access<='" . $juser->get('aid') . "'" : '')
				. " AND '" . $access . "'<='" . $juser->get('aid') . "'"
				. " ORDER BY a.modified DESC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get published articles
	 * 
	 * @param      integer $limit Number of records to return
	 * @param      string  $order Sort field
	 * @return     array
	 */
	public function getArticles($limit, $order)
	{
		$juser =& JFactory::getUser();

		$query = "SELECT a.id, a.title, a.state, a.access, a.created, a.modified, a.hits, a.alias, c.alias AS category,  cc.alias AS section"
				." FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq_categories AS c ON c.id = a.section"
				. " LEFT JOIN #__faq_categories AS cc ON cc.id = a.category"
				." WHERE a.state=1"
				." AND a.access <= ". $juser->get('aid') .""
				." ORDER BY " . $order
				." LIMIT " . $limit;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all records for a specific category
	 * 
	 * @param      integer $cid Category ID
	 * @return     array
	 */
	public function getCollection($cid=NULL)
	{
		if ($cid == NULL) 
		{
			$cid = $this->category;
		}
		$query = "SELECT r.id, r.section, r.category"
				. " FROM $this->_tbl AS r"
				. " WHERE r.section=" . $this->_db->Quote($cid) . " OR r.category=" . $this->_db->Quote($cid);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of all records
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getArticlesCount($filters=array())
	{
		if (isset($filters['cid']) && $filters['cid']) 
		{
			$where = "m.section=" . $this->_db->Quote($filters['cid']) . " AND m.category=" . $this->_db->Quote($filters['id']);
		} 
		else 
		{
			if (isset($filters['id']) && $filters['id']) 
			{
				$where = "m.section=" . $filters['id'];
			} 
			else 
			{
				$where = "m.section!=0";
			}
		}
		if (isset($filters['orphans']) && $filters['orphans']) 
		{
			$where = "m.section=0";
		}

		$query = "SELECT count(*) FROM $this->_tbl AS m WHERE " . $where;

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get all records
	 * Used by admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getArticlesAll($filters=array())
	{
		if (isset($filters['cid']) && $filters['cid']) 
		{
			$where = "m.section=".$this->_db->Quote($filters['cid'])." AND m.category=".$this->_db->Quote($filters['id']);
		} 
		else 
		{
			if (isset($filters['id']) && $filters['id']) 
			{
				$where = "m.section=".$this->_db->Quote($filters['id']);
			} 
			else 
			{
				$where = "m.section!=0";
			}
		}
		if (isset($filters['orphans']) && $filters['orphans']) 
		{
			$where = "m.section=0";
		}

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT m.id, m.title, m.created, m.state, m.access, m.checked_out, m.section, m.category, m.helpful, m.nothelpful, m.alias, c.title AS ctitle, cc.title AS cctitle, u.name AS editor, g.name AS groupname"
				. " FROM $this->_tbl AS m"
				. " LEFT JOIN #__users AS u ON u.id = m.checked_out"
				. " LEFT JOIN #__groups AS g ON g.id = m.access";
		}
		else 
		{
			$query = "SELECT m.id, m.title, m.created, m.state, m.access, m.checked_out, m.section, m.category, m.helpful, m.nothelpful, m.alias, c.title AS ctitle, cc.title AS cctitle, u.name AS editor, g.title AS groupname"
				. " FROM $this->_tbl AS m"
				. " LEFT JOIN #__users AS u ON u.id = m.checked_out"
				. " LEFT JOIN #__viewlevels AS g ON g.id = (m.access + 1)";
		}
		$query .= " LEFT JOIN #__faq_categories AS c ON c.id = m.section"
				. " LEFT JOIN #__faq_categories AS cc ON cc.id = m.category"
				. " WHERE ".$where
				. " ORDER BY ".$filters['filterby'];
		if (isset($filters['limit']) && $filters['limit'])
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete an SEF record
	 * 
	 * @param      string $option Component name
	 * @param      string $id     Record ID
	 * @return     boolean True upon success
	 */
	public function deleteSef($option, $id=NULL)
	{
		if ($id == NULL) 
		{
			$id = $this->id;
		}
		$this->_db->setQuery("DELETE FROM #__redirection WHERE newurl='index.php?option=" . $this->_db->getEscaped($option) . "&task=article&id=" . intval($id) . "'");
		if ($this->_db->query()) 
		{
			return true;
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$sql = "FROM $this->_tbl AS m 
				LEFT JOIN #__faq_categories AS c ON c.id = m.section 
				LEFT JOIN #__faq_categories AS cc ON cc.id = m.category ";
		/*if (isset($filters['search']) && $filters['search'] != '') {
			$sql .= " LEFT JOIN #__tags_object AS tt ON tt.objectid=m.id AND tt.tbl='kb'";
			$sql .= " LEFT JOIN #__tags AS t ON tt.tagid=t.id";
		}*/
		if (isset($filters['user_id']) && $filters['user_id'] > 0) 
		{
			$sql .= " LEFT JOIN #__faq_helpful_log AS v ON v.object_id=m.id AND v.user_id=" . $this->_db->Quote($filters['user_id']) . " AND v.type='entry' ";
		}

		$w = array();
		if (isset($filters['section']) && $filters['section']) 
		{
			$w[] = "m.section=" . $this->_db->Quote($filters['section']);
		}
		if (isset($filters['category']) && $filters['category']) 
		{
			$w[] = "m.category=" . $this->_db->Quote($filters['category']);
		}
		if (isset($filters['state'])) 
		{
			$w[] = "m.state=" . $this->_db->Quote($filters['state']);
			$w[] = "c.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			/*$w[] = "(
					m.title LIKE '%".$filters['search']."%' 
					OR m.fulltxt LIKE '%".$filters['search']."%' 
					OR t.raw_tag LIKE '%".$filters['search']."%' 
					OR t.tag LIKE '%".$filters['search']."%'
			)";*/
			$w[] = "(
					m.title LIKE '%" . $this->_db->getEscaped($filters['search']) . "%' 
					OR m.fulltxt LIKE '%" . $this->_db->getEscaped($filters['search']) . "%' 
				)";
		}

		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ", $w);

		if (isset($filters['order']) && $filters['order'] != '') 
		{
			switch ($filters['order'])
			{
				case 'recent': $order = 'm.modified DESC, m.created DESC'; break;
				//case 'created': $order = $filters['orderby'].' DESC'; break;
				case 'popularity': $order = '(m.helpful-m.nothelpful) DESC'; break;
				default: $order = $filters['order']; break;
			}
			$sql .= " ORDER BY " . $order;
		}
		if (isset($filters['limit']) && $filters['limit'] != '') 
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		return $sql;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = '';
		$query = "SELECT count(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT DISTINCT(m.id), m.title, m.created, m.state, m.access, m.modified, m.section, m.category, m.helpful, m.nothelpful, m.alias, c.title AS ctitle, c.alias AS calias, cc.title AS cctitle, cc.alias AS ccalias ";
		if (isset($filters['user_id']) && $filters['user_id'] > 0) 
		{
			$query .= ", v.vote, v.user_id ";
		}
		$query .= $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

