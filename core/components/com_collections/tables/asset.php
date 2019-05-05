<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tables;

use Hubzero\Database\Table;
use Component;
use Filesystem;
use Date;
use User;
use Lang;

/**
 * Table class for collection item asset
 */
class Asset extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_assets', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
		}

		$this->filename = trim($this->filename);
		if (!$this->filename)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_FILE_NAME'));
		}

		if ($this->getError())
		{
			return false;
		}

		$this->description = trim($this->description);

		$this->type = strtolower(trim($this->type));
		if (!in_array($this->type, array('file', 'link')))
		{
			$this->type = 'file';
		}

		$this->state = intval($this->state);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
			$this->state      = 1;

			$this->_db->setQuery("SELECT ordering FROM $this->_tbl WHERE item_id=" . $this->_db->quote($this->item_id) . " ORDER BY ordering DESC LIMIT 1");

			$this->ordering = (int) $this->_db->loadResult() + 1;
		}

		return true;
	}

	/**
	 * Load a record
	 *
	 * @param   integer  $oid      ID
	 * @param   integer  $item_id  Item ID
	 * @return  boolean  True upon success, False if errors
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
	 * @param   string  $what
	 * @param   array   $filters
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
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
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
				$where[] = "a.item_id=" . $this->_db->quote(intval($filters['item_id']));
			}
		}
		if (isset($filters['filename']))
		{
			$where[] = "a.filename=" . $this->_db->quote($filters['filename']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(a.filename) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(a.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}
		if (isset($filters['created_by']))
		{
			$where[] = "a.created_by=" . $this->_db->quote(intval($filters['created_by']));
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}
		$where[] = "a.state=" . $this->_db->quote(intval($filters['state']));

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
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		return $this->find('count', $filters);
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		return $this->find('list', $filters);
	}

	/**
	 * Rename a file and mark the record as "deleted"
	 *
	 * @param   integer  $id    Entry ID
	 * @param   string   $path  File path
	 * @return  boolean  True on success, false on error
	 */
	public function remove($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ID'));
			return false;
		}

		$this->load($id);

		if (!$this->filename)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_FILE_NAME'));
			return false;
		}

		if ($this->type == 'file')
		{
			$path = $this->path($this->item_id);

			$ext = Filesystem::extension($this->filename);
			$fileRemoved = Filesystem::name($this->filename) . uniqid('_d') . '.' . $ext;

			$file = $path . DS . $this->filename;

			if (!file_exists($file) or !$file)
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_FILE_NOT_FOUND'));
				return false;
			}

			if (!Filesystem::move($file, $path . DS . $fileRemoved))
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_RENAME_FILE'));
				return false;
			}

			$this->filename = $fileRemoved;
		}

		$this->state = 2;

		if (!$this->store())
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_UPDATE_RECORD'));
			return false;
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $oid  Entry ID
	 * @return  boolean  True on success, false on error
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		$this->load($oid);

		$path = $this->path($this->item_id);

		$ext     = Filesystem::extension($this->filename);
		$thumb   = Filesystem::name($this->filename) . '_t.' . $ext;
		$removed = Filesystem::name($this->filename) . uniqid('_d') . '.' . $ext;

		if (!Filesystem::delete($path . DS . $this->filename))
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_DELETE_FILE'));
		}

		if (file_exists($path . DS . $thumb))
		{
			if (!Filesystem::delete($path . DS . $thumb))
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_DELETE_FILE'));
			}
		}

		if (file_exists($path . DS . $removed))
		{
			if (!Filesystem::delete($path . DS . $removed))
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_DELETE_FILE'));
			}
		}

		return parent::delete();
	}

	/**
	 * Get file path
	 *
	 * @param   integer  $id  Entry ID
	 * @return  string
	 */
	public function path($id=null)
	{
		$config = Component::params('com_collections');
		return PATH_APP . DS . trim($config->get('filepath', '/site/collections'), DS) . DS . $id;
	}
}
