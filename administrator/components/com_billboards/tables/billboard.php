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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Billboard table class
 *
 */
class BillboardsBillboard extends JTable
{

	/**
	 * ID, primary key for jos_billboards
	 *
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * ID of collection, referencing jos_billboard_collection
	 *
	 * @var int(11)
	 */
	var $collection_id = NULL;

	/**
	 * Name of the billboard
	 *
	 * @var varchar(255)
	 */
	var $name = NULL;

	/**
	 * H3 header text of the billboard
	 *
	 * @var varchar(255)
	 */
	var $header = NULL;

	/**
	 * Billboard text (i.e. slide content)
	 *
	 * @var text
	 */
	var $text = NULL;

	/**
	 * Text of the "learn More" link
	 *
	 * @var varchar(255)
	 */
	var $learn_more_text = NULL;

	/**
	 * Target of the "Learn More" link, should be a relative URL
	 *
	 * @var varchar(255)
	 */
	var $learn_more_target = NULL;

	/**
	 * CSS class for the "Learn More" link
	 *
	 * @var varchar(255)
	 */
	var $learn_more_class = NULL;

	/**
	 * Location for the "Learn More" link
	 *
	 * @var varchar(255)
	 */
	var $learn_more_location = NULL;

	/**
	 * Background image of the billboard
	 *
	 * @var varchar(255)
	 */
	var $background_img = NULL;

	/**
	 * CSS paragraph padding property of the billboard
	 *
	 * @var varchar(255)
	 */
	var $padding = NULL;

	/**
	 * A unique billboard alias to be used for the billboard container
	 * It will also serve as the billboard CSS ID
	 *
	 * @var varchar(255)
	 */
	var $alias = NULL;

	/**
	 * CSS styling for the billboard, only needed for more detailed/advanced styling
	 *
	 * @var text
	 */
	var $css = NULL;

	/**
	 * Published/Unpublished state of the billboard (0 for unpublished, 1 for published)
	 *
	 * @var tinyint(1)
	 */
	var $published = NULL;

	/**
	 * Banner ordering
	 *
	 * @var int(11)
	 */
	var $ordering = NULL;

	/**
	 * uidNumber/ID of member currently editing billboard (0 if no one is editing)
	 *
	 * @var int(11)
	 */
	var $checked_out = NULL;

	/**
	 * Time at which the billboard was checked out
	 *
	 * @var datetime
	 */
	var $checked_out_time = NULL;

	//-----------

	/**
	 * Contructor method for JTable class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__billboards', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		// Do some cleanup, trying to make to keep people from making mistakes
		// Strip <p> tags from billboard text and convert closing </p> tags to break tags
		$this->text = str_replace("</p>","<br />",str_replace("<p>", "", $this->text));

		// Give an arbitrary billboard alias/CSS ID name when one isn't provided
		if (!$this->alias)
		{
			$this->alias = strtolower(str_replace(" ", "", $this->name));
		}
		else
		{
			// Even if they provide one, we should get rid of caps and spaces
			$this->alias = strtolower(str_replace(" ", "", $this->alias));
		}

		return true;
	}

	/**
	 * Build query method (basically just used to specify the FROM portion)
	 *
	 * @param  array $filters not needed right now
	 * @return $query database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS b";

		return $query;
	}

	/**
	 * Get a count of the number of billboard (used mainly for pagination)
	 *
	 * @param  unknown $filters not needed right now
	 * @return object Return count of rows
	 */
	public function getCount($filters)
	{
		$query  = "SELECT COUNT(b.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of the billboards in the database
	 *
	 * @param  array $filters start and limit, needed for pagination
	 * @return object Return billboard records
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT b.*, c.name AS bcollection";
		$query .= $this->buildquery($filters);
		$query .= " LEFT JOIN #__billboard_collection AS c ON c.id = b.collection_id";
		$query .= " ORDER BY collection_id ASC, `ordering` ASC";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build the ordering query
	 *
	 * @param  $collection_id is the collection in which to order the billboards
	 * @return $query
	 */
	public function buildOrderingQuery($collection_id)
	{
		$query  = 'SELECT `ordering` AS value, name AS text';
		$query .= $this->buildquery();
		$query .= ' WHERE collection_id = ' . $collection_id;
		$query .= ' ORDER BY `ordering` ASC';

		return $query;
	}

	/**
	 * Get the next ordering number based on the collection selected
	 *
	 * @param  $collection_id is the collection in which to find the max order number
	 * @return the current highest order number + 1
	 */
	public function getNextOrdering($collection_id)
	{
		$query  = 'SELECT MAX(ordering)+1';
		$query .= $this->buildquery();
		$query .= ' WHERE collection_id = ' . $collection_id;

		$this->_db->setQuery($query);

		$order = $this->_db->loadResult();
		if(!$order)
		{
			$order = 1;
		}

		return $order;
	}
}