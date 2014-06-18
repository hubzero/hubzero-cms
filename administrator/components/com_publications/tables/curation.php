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
 * Table class for publication curation flow
 */
class PublicationCuration extends JTable
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
	var $publication_id 			= NULL;

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
	var $updated_by					= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $updated					= NULL;

	/**
	 * Messages from authors
	 *
	 * @var text
	 */
	var $update						= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $reviewed_by				= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $reviewed					= NULL;

	/**
	 * Messages from reviewer
	 *
	 * @var text
	 */
	var $review						= NULL;

	/**
	 * Block alias
	 *
	 * @var string
	 */
	var $block						= NULL;

	/**
	 * Block sequence number
	 *
	 * @var int
	 */
	var $step						= NULL;

	/**
	 * Element ID
	 *
	 * @var int
	 */
	var $element					= NULL;

	/**
	 * Review status
	 *
	 * @var int (1 = pass, 2 = fail)
	 */
	var $review_status				= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_curation', 'id', $db );
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->publication_id)
		{
			$this->setError( JText::_('Must have a publication ID.') );
			return false;
		}

		if (!$this->publication_version_id)
		{
			$this->setError( JText::_('Must have a publication version ID.') );
			return false;
		}

		return true;
	}

	/**
	 * Get curation record
	 *
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getRecords( $vid = NULL )
	{
		if (!intval($vid))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $vid;
		$query.= " ORDER BY step ASC, element ASC ";
		$this->_db->setQuery( $query );

		return $this->_db->loadObjectList();

	}

	/**
	 * Load record
	 *
	 * @param      integer 	$pid Publication ID
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function loadRecord( $pid = NULL, $vid = NULL, $block = NULL, $step = 0, $element = NULL )
	{
		if (!$pid || !$vid || !$block || !intval($step))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_id=" . $pid;
		$query.= " AND publication_version_id=" . $vid;
		$query.= " AND block='" . $block . "' ";
		$query.= " AND step=" . $step;
		$query.= $element ? " AND element='$element' " : " AND (element IS NULL OR element=0)";
		$query.= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
