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
 * Time - hubs database class
 */
Class TimeHubs extends JTable
{
	/**
	 * id, primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * hub name
	 *
	 * @var varchar(255)
	 */
	var $name = null;

	/**
	 * normalized hub name
	 *
	 * @var varchar(255)
	 */
	var $name_normalized = null;

	/**
	 * hub liaison
	 *
	 * @var varchar(255)
	 */
	var $liaison = null;

	/**
	 * anniversary date
	 *
	 * @var date
	 */
	var $anniversary_date = null;

	/**
	 * support level
	 *
	 * @var varchar(255)
	 */
	var $support_level = null;

	/**
	 * active
	 *
	 * @var int(1)
	 */
	var $active = null;

	/**
	 * notes
	 *
	 * @var blob
	 */
	var $notes = null;

	/**
	 * asset id
	 *
	 * @var int
	 */
	var $asset_id = null;

	/**
	 * Constructor
	 *
	 * @param   database object
	 * @return  void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_hubs', 'id', $db );
	}

	/**
	 * Method to compute the name of the asset
	 *
	 * @return  string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_time.hubs.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table
	 *
	 * @return  string
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 *
	 * @return  integer  The id of the asset's parent
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$assetId = null;

		// Build the query to get the asset id for the parent category
		$query = $this->_db->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where('name = ' . $this->_db->quote('com_time'));

		// Get the asset id from the database
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadResult())
		{
			$assetId = (int) $result;
		}

		return ($assetId) ? $assetId : parent::_getAssetParentId($table, $id);
	}

	/**
	 * Override check function to perform validation
	 *
	 * @return boolean true if all checks pass, else false
	 */
	public function check()
	{
		// Trim whitespace from variables
		$this->name    = trim($this->name);
		$this->liaison = trim($this->liaison);

		// If name or liaison is empty, return an error
		if (empty($this->name) || empty($this->liaison))
		{
			if (empty($this->name))
			{
				$this->setError(JText::_('COM_TIME_HUBS_NO_NAME'));
				return false;
			}
			if (empty($this->liaison))
			{
				$this->setError(JText::_('COM_TIME_HUBS_NO_LIAISON'));
				return false;
			}
		}

		// Create the normalized version of the hub name
		$this->name_normalized = strtolower(str_replace(" ", "", $this->name));

		// Everything passed, return true
		return true;
	}

	/**
	 * Build query
	 *
	 * @param  $filters (not needed yet...)
	 * @return $query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS h";

		return $query;
	}

	/**
	 * Get count of hubs, mainly used for pagination
	 *
	 * @return query result number of hubs
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(h.id)";
		$query .= $this->buildquery();

		// If we only want active hubs
		if (!empty($filters['active']))
		{
			$query .= " WHERE h.active = ".$this->_db->quote($filters['active']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get list of hubs
	 *
	 * @param  $filters (examples: active, orderby, orderdir, start, limit)
	 * @return object list of hubs
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT h.*";
		$query .= $this->buildquery($filters);

		// Only active hubs
		if (!empty($filters['active']))
		{
			$query .= " WHERE h.active = 1";
		}

		// If orderby and orderdir are set, use them
		if (!empty($filters['orderby']) && !empty($filters['orderdir']))
		{
			if (!in_array(strtoupper($filters['orderdir']), array('ASC', 'DESC')))
			{
				$filters['orderdir'] = 'DESC';
			}
			$query .= " ORDER BY ".$filters['orderby']." ".$filters['orderdir'];
		}
		// If orderby and orderdir are not set, use some defaults
		else
		{
			$query .= " ORDER BY name ASC";
		}
		if (isset($filters['start']) && isset($filters['limit']) && $filters['limit'] > 0)
		{
			$query .= " LIMIT ".intval($filters['start']).",".intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}