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

class NewsletterMailinglistEmail extends JTable
{
	/**
	 * Mailing List Email ID
	 *
	 * @var int(11)
	 */
	var $id 					= NULL;

	/**
	 * Mailing List ID
	 *
	 * @var varchar(150)
	 */
	var $mid 					= NULL;

	/**
	 * Mailing List Email
	 *
	 * @var varchar(50)
	 */
	var $email	 				= NULL;

	/**
	 * Mailing List Email Status
	 *
	 * @var varchar(50)
	 */
	var $status	 				= NULL;

	/**
	 * Mailing List Email Confirmed?
	 *
	 * @var int(11)
	 */
	var $confirmed 				= NULL;

	/**
	 * Mailing List Email Date Added
	 *
	 * @var datetime
	 */
	var $date_added 			= NULL;

	/**
	 * Mailing List Email Date Confirmed
	 *
	 * @var datetime
	 */
	var $date_confirmed 		= NULL;


	/**
	 * Newsletter Mailing List Email Constructor
	 *
	 * @param 	$db		Database Object
	 * @return 	void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_mailinglist_emails', 'id', $db );
	}


	/**
	 * Newsletter Mailing List Email Save Check method
	 *
	 * @return 	boolean
	 */
	public function check()
	{
		if (trim($this->email) == '')
		{
			$this->setError('Mailing list email must not be empty.');
			return false;
		}

		//validate email
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
		{
			$this->setError('Mailing list email is not a valid email address.');
			return false;
		}

		return true;
	}
}