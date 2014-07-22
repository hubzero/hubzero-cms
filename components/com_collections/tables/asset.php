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
 * Table class for collection item asset
 */
class CollectionsTableAsset extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $item_id = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $filename    = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $description = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * int(2)
	 *
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $type = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $ordering = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_assets', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
			return false;
		}

		$this->filename = trim($this->filename);
		if (!$this->filename)
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_MISSING_FILE_NAME'));
			return false;
		}

		$this->description = trim($this->description);

		$this->type = strtolower(trim($this->type));
		if (!in_array($this->type, array('file', 'link')))
		{
			$this->type = 'file';
		}

		if (!$this->id)
		{
			$this->created    = JFactory::getDate()->toSql();
			$this->created_by = JFactory::getUser()->get('id');
			$this->state      = 1;

			$this->ordering = $this->_getHighestOrdering($this->item_id) + 1;
		}

		return true;
	}

	/**
	 * Get the last page in the ordering
	 *
	 * @param      string  $gid    Group alias (cn)
	 * @return     integer
	 */
	public function _getHighestOrdering($item_id)
	{
		$sql = "SELECT ordering from $this->_tbl WHERE item_id=" . $this->_db->Quote(intval($item_id)) . " ORDER BY ordering DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Load a record
	 *
	 * @param      integer $oid     ID
	 * @param      integer $item_id Item ID
	 * @return     boolean True upon success, False if errors
	 */
	public function load($oid=null, $item_id=null)
	{
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$fields = array(
			'item_id'  => (int) $item_id,
			'filename' => (string) $oid
		);

		return parent::load($fields);
	}

	/**
	 * Return data based on a set of filters. Returned value 
	 * can be integer, object, or array
	 * 
	 * @param   string $what
	 * @param   array  $filters
	 * @return  mixed
	 */
	public function find($what='', $filters=array())
	{
		$what = strtolower(trim($what));

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query = "SELECT a.*, u.name";
				$query .= $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'a.ordering';
				}
				if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS a";
		$query .= " LEFT JOIN #__users AS u ON a.created_by=u.id";

		$where = array();

		if (isset($filters['item_id']))
		{
			if (is_array($filters['item_id']))
			{
				$filters['item_id'] = array_map('intval', $filters['item_id']);
				$where[] = "a.item_id IN (" . implode(',', $filters['item_id']) . ")";
			}
			else
			{
				$where[] = "a.item_id=" . $this->_db->Quote(intval($filters['item_id']));
			}
		}
		if (isset($filters['filename']))
		{
			$where[] = "a.filename=" . $this->_db->Quote($filters['filename']);
		}
		/*if (isset($filters['description']))
		{
			$where[] = "a.description=" . $this->_db->Quote($filters['description']);
		}*/
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(a.filename) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(a.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}
		if (isset($filters['created_by']))
		{
			$where[] = "a.created_by=" . $this->_db->Quote(intval($filters['created_by']));
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}
		$where[] = "a.state=" . $this->_db->Quote(intval($filters['state']));

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		return $this->find('count', $filters);
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		return $this->find('list', $filters);
	}

	/**
	 * Rename a file and mark the record as "deleted"
	 *
	 * @param      integer $id   Entry ID
	 * @param      string  $path File path
	 * @return     boolean True on success, false on error
	 */
	public function remove($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_MISSING_ID'));
			return false;
		}

		$this->load($id);

		if (!$this->filename)
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_MISSING_FILE_NAME'));
			return false;
		}

		//$UrlPtn = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";
		//if (!preg_match("/$UrlPtn/", $this->filename) && $this->filename != 'http://')
		if ($this->type == 'file')
		{
			jimport('joomla.filesystem.file');

			$config = JComponentHelper::getParams('com_collections');
			$path = JPATH_ROOT . DS . trim($config->get('filepath', '/site/collections'), DS) . DS . $this->item_id;

			$ext = JFile::getExt($this->filename);
			$fileRemoved = JFile::stripExt($this->filename) . uniqid('_d') . '.' . $ext;

			$file = $path . DS . $this->filename;

			if (!file_exists($file) or !$file)
			{
				$this->setError(JText::_('COM_COLLECTIONS_FILE_NOT_FOUND'));
				return false;
			}

			if (!JFile::move($file, $path . DS . $fileRemoved))
			{
				$this->setError(JText::_('COM_COLLECTIONS_ERROR_UNABLE_TO_RENAME_FILE'));
				return false;
			}

			$this->filename = $fileRemoved;
		}

		$this->state = 2;

		if (!$this->store())
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_UNABLE_TO_UPDATE_RECORD'));
			return false;
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $oid   Entry ID
	 * @return     boolean True on success, false on error
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		$this->load($oid);

		$config = JComponentHelper::getParams('com_collections');
		$path = JPATH_ROOT . DS . trim($config->get('filepath', '/site/collections'), DS) . DS . $this->item_id;

		jimport('joomla.filesystem.file');
		if (!JFile::delete($path . DS . $this->filename))
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_UNABLE_TO_DELETE_FILE'));
		}

		return parent::delete();
	}
}
