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
 * Table class for knowledge base categories
 */
class KbCategory extends JTable
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
	var $description  = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $section      = NULL;

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
		parent::__construct('#__faq_categories', 'id', $db);
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		$this->id = intval($this->id);
		$this->title = trim($this->title);

		if ($this->title == '') 
		{
			$this->setError(JText::_('COM_KB_ERROR_EMPTY_TITLE'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

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
		return 'com_kb.category.' . (int) $this->$k;
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
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Alias
	 * @return     boolean True upon success, False if errors
	 */
	public function loadAlias($oid=NULL)
	{
		if (empty($oid)) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias='$oid'");
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
	 * Get categories for a section
	 * 
	 * @param      integer $noauth    Restrict by article authorizartion level
	 * @param      integer $empty_cat Return empty categories?
	 * @param      integer $catid     Section ID
	 * @return     array
	 */
	public function getCategories($noauth, $empty_cat=0, $catid=0)
	{
		$juser =& JFactory::getUser();

		if ($empty_cat) 
		{
			$empty = '';
		} 
		else 
		{
			$empty = "\n HAVING COUNT(b.id) > 0";
		}

		if ($catid) 
		{
			$sect = "b.category";
		} 
		else 
		{
			$sect = "b.section";
		}

		$query = "SELECT a.*, COUNT(b.id) AS numitems"
				. " FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq AS b ON " . $sect . " = a.id AND b.state=1 AND b.access=0"
				. " WHERE a.state=1 AND a.section=" . $catid
				. ($noauth ? " AND a.access <= '" . $juser->get('aid') . "'" : '')
				. " GROUP BY a.id"
				. $empty
				. " ORDER BY a.title";
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
		$this->_db->setQuery("DELETE FROM #__redirection WHERE newurl='index.php?option=" . $option . "&task=category&id=" . $id . "'");
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
	 * Get all sections
	 * 
	 * @return     array
	 */
	public function getAllSections()
	{
		$this->_db->setQuery("SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section=0 ORDER BY m.title");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all categories
	 * 
	 * @return     array
	 */
	public function getAllCategories()
	{
		$this->_db->setQuery("SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section!=0 ORDER BY m.title");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of records based off of filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCategoriesCount($filters=array())
	{
		$query  = "SELECT count(*) FROM $this->_tbl WHERE section=";
		$query .= (isset($filters['id'])) ? $filters['id'] : "0";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records based off of filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getCategoriesAll($filters=array())
	{
		if (isset($filters['id']) && $filters['id']) 
		{
			$sect = $filters['id'];
			$sfield = "category";
		} 
		else 
		{
			$sect = 0;
			$sfield = "section";
		}

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT m.id, m.title, m.section, m.state, m.access, m.alias, g.name AS groupname, 
					(SELECT count(*) FROM #__faq AS fa WHERE fa." . $sfield . "=m.id) AS total, 
					(SELECT count(*) FROM $this->_tbl AS fc WHERE fc.section=m.id) AS cats"
				. " FROM #__faq_categories AS m"
				. " LEFT JOIN #__groups AS g ON g.id = m.access";
		}
		else 
		{
			$query = "SELECT m.id, m.title, m.section, m.state, m.access, m.alias, g.title AS groupname, 
					(SELECT count(*) FROM #__faq AS fa WHERE fa." . $sfield . "=m.id) AS total, 
					(SELECT count(*) FROM $this->_tbl AS fc WHERE fc.section=m.id) AS cats"
				. " FROM #__faq_categories AS m"
				. " LEFT JOIN #__viewlevels AS g ON g.id = (m.access + 1)";
		}
		$query .= " WHERE m.section=" . $sect
				. " ORDER BY " . $filters['filterby'];
		if (isset($filters['limit']) && $filters['limit'])
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

