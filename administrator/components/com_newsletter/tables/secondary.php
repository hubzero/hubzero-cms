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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class NewsletterSecondaryStory extends JTable
{
	/**
	 * Secondary Story Id
	 *
	 * @var int(11)
	 */
	var $id 		= NULL;

	/**
	 * Secondary Story Newsletter ID
	 *
	 * @var int(11)
	 */
	var $nid	  	= NULL;

	/**
	 * Secondary Story Title
	 *
	 * @var varchar(150)
	 */
	var $title 		= NULL;

	/**
	 * Secondary Story Story
	 *
	 * @var text
	 */
	var $story	 	= NULL;

	/**
	 * Secondary Story Read More Title
	 *
	 * @var varchar(100)
	 */
	var $readmore_title 	= NULL;

	/**
	 * Secondary Story Read More Link
	 *
	 * @var varchar(200)
	 */
	var $readmore_link 		= NULL;

	/**
	 * Primary Story Order
	 *
	 * @var int(11)
	 */
	var $order		= NULL;

	/**
	 * Secondary Story Deleted?
	 *
	 * @var int(11)
	 */
	var $deleted	= NULL;


	/**
	 * Newsletter Primary Story Constructor
	 *
	 * @param 	$db		Database Object
	 * @return 	void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_secondary_story', 'id', $db );
	}


	/**
	 * Get Secondary Stories
	 *
	 * @param 	$newsletterId		Newsletter Id
	 * @return 	array
	 */
	public function getStories( $newsletterId )
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";

		if ($newsletterId)
		{
			$sql .= " AND nid=". $this->_db->quote( $newsletterId );
		}

		$sql .= " ORDER BY `order`";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}


	/**
	 * Get Highest Story Order
	 *
	 * @param 	$newsletterId		Newsletter Id
	 * @return 	order
	 */
	public function _getCurrentHighestOrder( $newsletterId )
	{
		$sql = "SELECT `order` FROM {$this->_tbl} WHERE deleted=0 AND nid=" . $this->_db->quote( $newsletterId ) . " ORDER BY `order` DESC LIMIT 1";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
}