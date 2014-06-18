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

class NewsletterMailingRecipientAction extends JTable
{
	/**
	 * Mailing Action ID
	 *
	 * @var int(11)
	 */
	var $id 					= NULL;

	/**
	 * Mailing ID
	 *
	 * @var int(11)
	 */
	var $mailingid 				= NULL;

	/**
	 * Mailing Action
	 *
	 * @var varchar(100)
	 */
	var $action	 				= NULL;

	/**
	 * Mailing Action Vars
	 *
	 * @var text
	 */
	var $action_vars	 		= NULL;

	/**
	 * Mailing Action Email
	 *
	 * @var varchar(255)
	 */
	var $email 					= NULL;

	/**
	 * Mailing Action IP
	 *
	 * @var int(11)
	 */
	var $ip 					= NULL;

	/**
	 * Mailing Action User Agent String
	 *
	 * @var varchar(255)
	 */
	var $user_agent		 		= NULL;

	/**
	 * Mailing Action Date
	 *
	 * @var datetime
	 */
	var $date		 			= NULL;


	/**
	 * Newsletter Mailing Recipient Action Constructor
	 *
	 * @param 	$db		Database Object
	 * @return 	void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_mailing_recipient_actions', 'id', $db );
	}


	/**
	 * Get newsletter mailing actions
	 *
	 * @param   $id     ID of mailing action
	 * @return  array
	 */
	public function getActions( $id = null )
	{
		$sql = "SELECT * FROM {$this->_tbl}";

		if (isset($id) && $id != '')
		{
			$sql .= " WHERE id=" . $this->_db->quote( $id );
		}

		$sql .= " ORDER BY date DESC";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}


	/**
	 * Get unconverted mailing actions
	 *
	 * @return  void
	 */
	public function getUnconvertedActions()
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE (ipLATITUDE = '' OR ipLATITUDE IS NULL OR ipLONGITUDE = '' OR ipLONGITUDE IS NULL)";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}


	/**
	 * Get mailing actions for mailing ID
	 *
	 * @param   $mailingid      Mailing ID #
	 * @param   $action         Mailing Action
	 * @return 	void
	 */
	public function getMailingActions( $mailingid, $action = 'open' )
	{
		$sql = "SELECT * FROM {$this->_tbl}
				WHERE mailingid=" . $this->_db->quote( $mailingid ) . "
				AND action=" . $this->_db->quote( $action );
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}


	/**
	 * Check to see if mailing action already exists for mailing ID and email
	 *
	 * @param   $mailingid      Mailing ID #
	 * @param   $email          Email Address
	 * @param   $action         Mailing Action
	 * @return  boolean
	 */
	public function actionExistsForMailingAndEmail( $mailingid, $email, $action = 'open' )
	{
		$sql = "SELECT * FROM {$this->_tbl}
				WHERE mailingid=" . $this->_db->quote( $mailingid ) ."
				AND email=" . $this->_db->quote( $email ) . "
				AND action=" . $this->_db->quote( $action );
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObject();
		return (is_object($result)) ? true : false;
	}
}