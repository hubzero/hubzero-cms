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
class KbTableArticle extends JTable
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
			$this->access = $this->access ? $this->access : 0;

			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
		}
		else
		{
			$this->modified = JFactory::getDate()->toSql();
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
	 * Store changes.
	 *
	 * @return     boolean
	 */
	public function store($updateNulls = false)
	{
		if (empty($this->modified))
		{
			$this->modified = $this->created;
		}
		$this->version++;

		return parent::store($updateNulls);
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
		$sql  = "SELECT * FROM $this->_tbl WHERE `alias`=" . $this->_db->Quote($oid);
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
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl AS a
					LEFT JOIN #__faq_categories AS c ON c.id = a.section
					LEFT JOIN #__faq_categories AS cc ON cc.id = a.category ";

		if (isset($filters['user_id']) && $filters['user_id'] > 0)
		{
			$query .= " LEFT JOIN #__faq_helpful_log AS v ON v.object_id=a.id AND v.user_id=" . $this->_db->Quote($filters['user_id']) . " AND v.type='entry' ";
		}

		$where = array();

		if (isset($filters['section']) && $filters['section'] > 0)
		{
			$where[] = "a.`section`=" . $this->_db->Quote($filters['section']);
		}
		if (isset($filters['category']) && $filters['category'] >= 0)
		{
			$where[] = "a.`category`=" . $this->_db->Quote($filters['category']);
		}
		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "a.`state`=" . $this->_db->Quote($filters['state']);
			$where[] = "c.`state`=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['access']) && $filters['access'] >= 0)
		{
			$where[] = "a.`access`=" . $this->_db->Quote($filters['access']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(a.`title` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . " OR a.`fulltxt` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['sort']) && $filters['sort'])
		{
			switch ($filters['sort'])
			{
				case 'recent':     $filters['sort'] = 'a.modified DESC, a.created'; break;
				case 'popularity': $filters['sort'] = '(a.helpful - a.nothelpful)'; $filters['sort_Dir'] = 'DESC'; break;
				default:
					if (substr($filters['sort'], 0, 2) != 'a.' && array_key_exists($filters['sort'], $this->getFields()))
					{
						$filters['sort'] = 'a.' . $filters['sort'];
					}

					$filters['sort_Dir'] = (isset($filters['sort_Dir'])) ? $filters['sort_Dir'] : 'DESC';
				break;
			}

			$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
			if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}

			$query .= " ORDER BY " . $filters['sort'] . " " .  $filters['sort_Dir'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function count($filters=array())
	{
		$filters['sort'] = null;

		$query  = "SELECT COUNT(a.id) ";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function find($filters=array())
	{
		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}

		$query  = "SELECT a.*, c.title AS ctitle, c.alias AS calias, cc.title AS cctitle, cc.alias AS ccalias ";
		if (isset($filters['user_id']) && $filters['user_id'] > 0)
		{
			$query .= ", v.vote, v.user_id ";
		}

		$query .= $this->_buildQuery($filters);
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

