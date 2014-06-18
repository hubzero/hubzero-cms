<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

/**
 * Module class for displaying current system environment
 */
class modNewsletter extends \Hubzero\Module\Module
{
	/**
	 * Display module
	 *
	 * @return     void
	 */
	public function display()
	{
		//instantiate database object
		$this->database = JFactory::getDBO();

		//instantiate user object
		$this->juser = JFactory::getUser();

		//get mailing list details that we are wanting users to sign up for
		$sql = "SELECT * FROM #__newsletter_mailinglists WHERE deleted=0 AND private=0 AND id=" . $this->database->quote( $this->params->get('mailinglist', 0) );
		$this->database->setQuery( $sql );
		$this->mailinglist = $this->database->loadObject();

		//get mailing list subscription if not guest
		$this->subscription   = null;
		$this->subscriptionId = null;
		if (!$this->juser->get('guest'))
		{
			$sql = "SELECT * FROM #__newsletter_mailinglist_emails WHERE mid=" . $this->database->quote( $this->params->get('mailinglist', 0) ) . " AND email=" . $this->database->quote( $this->juser->get('email'));
			$this->database->setQuery( $sql );
			$this->subscription = $this->database->loadObject();
		}

		//if we are unsubscribed
		if (is_object($this->subscription) && $this->subscription->status == 'unsubscribed')
		{
			$this->subscriptionId = $this->subscription->id;
			$this->subscription   = null;
		}

		//add stylesheets and scripts
		$this->css();
		$this->js();

		//display module
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
