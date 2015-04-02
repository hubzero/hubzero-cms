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
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * End single sign on view class
 */
class UsersViewEndsinglesignon extends JViewLegacy
{
	function display($tpl = null)
	{
		// Assign variables to the view
		$authenticator = JRequest::getWord('authenticator', false);

		\Hubzero\Document\Assets::addComponentStylesheet('com_user', 'login.css');

		// Get the site name
		$jconfig  = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename');

		// Get the display name for the current plugin being used
		$plugin       = JPluginHelper::getPlugin('authentication', $authenticator);
		$pparams      = new JRegistry($plugin->params);
		$display_name = $pparams->get('display_name', ucfirst($plugin->name));

		$this->assign('authenticator', $authenticator);
		$this->assign('sitename', $sitename);
		$this->assign('display_name', $display_name);

		parent::display($tpl);
	}

	function attach()
	{
	}
}