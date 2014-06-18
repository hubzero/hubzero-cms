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
 * Table class for publication curation history
 */
class PublicationCurationHistory extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       					= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $publication_version_id 	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by					= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created					= NULL;

	/**
	 * Logs
	 *
	 * @var text
	 */
	var $changelog					= NULL;

	/**
	 * Curator or authors
	 *
	 * @var tinyint
	 */
	var $curator					= NULL;

	/**
	 * original status of version record
	 *
	 * @var int
	 */
	var $oldstatus					= NULL;

	/**
	 * original status of version record
	 *
	 * @var int
	 */
	var $newstatus					= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_curation_history', 'id', $db );
	}

	/**
	 * Get curation record
	 *
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getRecords( $vid = NULL, $filters = array() )
	{
		if (!intval($vid))
		{
			return false;
		}

		$sortby  = isset($filters['sortby']) && $filters['sortby'] ? $filters['sortby'] : 'created';
		$sortdir = isset($filters['sortdir']) && $filters['sortdir'] ? $filters['sortdir'] : 'DESC';

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $vid;

		if (isset($filters['curator']) && $filters['curator'] == 1)
		{
			$query .= " WHERE curator=1";
		}

		$query .= " ORDER BY " . $sortby . " " . $sortdir;
		$this->_db->setQuery( $query );

		return $this->_db->loadObjectList();

	}
}
