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
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
class Hubzero_Comment extends JTable
{
	/**
	 * Entry ID
	 * @var	integer int(11) Primary key
	 */
	public $id = NULL;

	/**
	 * ID of the entry the comment is attached to
	 * @var	integer int(11)
	 */
	public $referenceid = NULL;

	/**
	 * Category of the entry the comment is attached to (resource, question, etc.)
	 * @var	string varchar(50)
	 */
	public $category = NULL;

	/**
	 * The comment
	 * @var	string text
	 */
	public $comment = NULL;

	/**
	 * When the entry was made
	 * @var	datetime (0000-00-00 00:00:00)
	 */
	public $added = NULL;

	/**
	 * User ID of commenter
	 * @var	integer int(11)
	 */
	public $added_by = NULL;

	/**
	 * The published state of the entry
	 * @var	integer int(3)
	 */
	public $state = NULL;

	/**
	 * Flag for if the user posted anonymously
	 * @var	integer int(3)
	 */
	public $anonymous = NULL;

	/**
	 * Flag notifying the commenter of replies by email
	 * @var	integer int(3)
	 */
	public $email = NULL;

	/**
	 * A short subject or title of the entry
	 * @var	string varchar(150)
	 */
	public $subject = NULL;

	/**
	 * Object constructor to set the database
	 * Calls parent to set the table and key field
	 *
	 * @param object $db JDatabase object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__comments', 'id', $db);
	}

	/**
	 * Check if a comment was provided
	 *
	 * @return boolean True if a comment was provided
	 */
	public function check()
	{
		$this->comment = trim($this->comment);
		if ($this->comment == '' || $this->comment == JText::_('Enter your comments...')) 
		{
			$this->setError(JText::_('Please provide a comment'));
			return false;
		}
		return true;
	}

	/**
	 * Get an array of comments from the database
	 *
	 * @param array Optional array of filters used in building the query
	 * @param int Optional flag for returning the user's name in the result set
	 * @param int Optional flag for returning the number of abuse reports in the result set
	 * @return array of objects with properties matching the fields SELECTed
	 */
	public function getResults($filters=array(), $get_profile_name = 0, $get_abuse_reports = 0)
	{
		$query  = "SELECT c.* ";
		$query .= $get_profile_name ? ", xp.name AS authorname " : "";
		$query .= $get_abuse_reports ? ", (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=c.id AND RR.state=0 AND RR.category='wishcomment') AS reports " : "";
		$query .= "FROM $this->_tbl AS c ";
		$query .= $get_profile_name ? "JOIN #__xprofiles AS xp ON xp.uidNumber=c.added_by " : "";
		$query .= "WHERE c.referenceid=" . $filters['id'] . " AND c.category='" . $filters['category'] . "' AND c.state!=2 ";
		$query .= "ORDER BY c.added ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

