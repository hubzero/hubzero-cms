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
	var $id         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $userid	    = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $fullname   = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $org	    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $quote      = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $picture    = NULL;

	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $date	    = NULL;

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
	var $notes 		= NULL;

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
		if (trim($this->quote) == '') 
		{
			$this->setError(JText::_('Quote must contain text.'));
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
	public function buildQuery($filters)
	{
		$query = "FROM $this->_tbl ";
		if ((isset($filters['search']) && $filters['search'] != '')
		 || (isset($filters['id']) && $filters['id'] != 0)) 
		{
			$query .= "WHERE";
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$words = explode(' ', $filters['search']);
			$sqlsearch = array();
			foreach ($words as $word)
			{
				$sqlsearch[] = "(LOWER(fullname) LIKE '%" . $this->_db->getEscaped(strtolower($word)) . "%')";
			}
			$query .= implode(" OR ", $sqlsearch);
		}
		if (isset($filters['id']) && $filters['id'] != 0) 
		{
			$query .= " AND id=" . $this->_db->Quote($filters['id']);
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
		
		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

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
		$query  = "SELECT * " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete a picture associated with a record
	 * 
	 * @param      object $config JParameter
	 * @return     boolean True on success
	 */
	public function deletePicture($config=null)
	{
		// Load the component config
		if (!$config) 
		{
			$config =& JComponentHelper::getParams('com_feedback');
		}

		// Incoming member ID
		if (!$this->id) 
		{
			$this->setError(JText::_('FEEDBACK_NO_ID'));
			return false;
		}

		// Incoming file
		if (!$this->picture) 
		{
			return true;
		}

		// Build the file path
		ximport('Hubzero_View_Helper_Html');
		$dir  = Hubzero_View_Helper_Html::niceidformat($this->id);
		$path = JPATH_ROOT . DS . trim($config->get('uploadpath', '/site/quotes'), DS) . DS . $dir;

		if (!file_exists($path . DS . $this->picture) or !$this->picture) 
		{
			return true;
		} 
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $this->picture)) 
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
				return false;
			}
		}

		return true;
	}
}

