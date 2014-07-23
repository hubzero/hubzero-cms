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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for feedback quote
 */
class FeedbackQuotes extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $user_id = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $fullname = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $org = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $quote = NULL;

	/**
	 * datetime
	 *
	 * @var string
	 */
	var $date = NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $publish_ok = NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $contact_ok = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $notes = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $short_quote = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $miniquote = NULL;

	/**
	* int(1)
	*
	* @var integer
	*/
	var $admin_rating = NULL;

	/**
	* int(1)
	*
	* @var integer
	*/
	var $notable_quote  = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedback', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->quote = trim($this->quote);
		if ($this->quote == '')
		{
			$this->setError(JText::_('Quote must contain text.'));
			return false;
		}
		$this->quote = str_replace('<br>', '<br />', $this->quote);

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters)
	{
		$query = "FROM $this->_tbl ";

		$where = array();

		if (isset($filters['notable_quote']) && $filters['notable_quote'] >= 0)
		{
			$where[] = "notable_quote=" . $this->_db->Quote($filters['notable_quote']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$words = explode(' ', $filters['search']);
			$sqlsearch = array();

			foreach ($words as $word)
			{
				$sqlsearch[] = "(LOWER(fullname) LIKE " . $this->_db->quote('%' . strtolower($word) . '%') . ")";
			}

			$where[] = "(" . implode(" OR ", $sqlsearch) . ")";
		}

		if (isset($filters['id']) && $filters['id'] != 0)
		{
			$where[] = "id=" . $this->_db->Quote($filters['id']);
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (empty($filters['sortby']))
		{
			$filters['sortby'] = 'date';
		}

		$query .= " ORDER BY " . $filters['sortby'] . " DESC";

		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] > 0)
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getResults($filters=array())
	{
		$query  = "SELECT * " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			$this->setError($e);
			return false;
		}

		$config = JComponentHelper::getParams('com_feedback');

		$path = JPATH_ROOT . DS . trim($config->get('uploadpath', '/site/quotes'), DS) . DS . $pk;
		$this->_delTree($path);

		return parent::delete($pk);
	}

	/**
	 * Recursively remove files and directories
	 *
	 * @param   string $dir Directory to remove
	 * @return  void
	 */
	private function _delTree($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $file)
		{
			(is_dir("$dir/$file")) ? $this->_delTree("$dir/$file") : unlink("$dir/$file");
		}
		rmdir($dir);
	}
}

