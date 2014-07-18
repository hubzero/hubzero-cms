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
 * User link account view class
 */
class UsersViewLink extends JViewLegacy
{
	function display($tpl = null)
	{
		$user     = JFactory::getUser();
		$document = JFactory::getDocument();

		// If this is an auth_link account update, carry on, otherwise raise an error
		if (!is_object($user)
				|| !array_key_exists('auth_link_id', $user)
				|| !is_numeric($user->get('username'))
				|| !$user->get('username') < 0)
		{
			JError::raiseError('405', 'Method not allowed');
			return;
		}

		// Get and add the js and extra css to the page
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'link.css');
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'providers.css');
		\Hubzero\Document\Assets::addComponentScript('com_users', 'assets/js/link');

		// Import a few things
		jimport('joomla.user.helper');

		// Look up a few things
		$hzal    = \Hubzero\Auth\Link::find_by_id($user->get("auth_link_id"));
		$hzad    = \Hubzero\Auth\Domain::find_by_id($hzal->auth_domain_id);
		$plugins = JPluginHelper::getPlugin('authentication');

		// Get the display name for the current plugin being used
		$plugin       = JPluginHelper::getPlugin('authentication', $hzad->authenticator);
		$pparams      = new JRegistry($plugin->params);
		$display_name = $pparams->get('display_name', ucfirst($plugin->name));

		// Look for conflicts - first check in the hub accounts
		$profile_conflicts = \Hubzero\User\Profile\Helper::find_by_email($hzal->email);

		// Now check the auth_link table
		$link_conflicts = \Hubzero\Auth\Link::find_by_email($hzal->email, array($hzad->id));

		$conflict = array();
		if ($profile_conflicts)
		{
			foreach ($profile_conflicts as $p)
			{
				$user_id    = JUserHelper::getUserId($p);
				$juser      = JFactory::getUser($user_id);
				$auth_link  = \Hubzero\Auth\Link::find_by_user_id($juser->id);
				$dname      = (is_object($auth_link) && $auth_link->auth_domain_name) ? $auth_link->auth_domain_name : 'hubzero';
				$conflict[] = array("auth_domain_name" => $dname, "name" => $juser->name, "email" => $juser->email);
			}
		}
		if ($link_conflicts)
		{
			foreach ($link_conflicts as $l)
			{
				$juser      = JFactory::getUser($l['user_id']);
				$conflict[] = array("auth_domain_name" => $l['auth_domain_name'], "name" => $juser->name, "email" => $l['email']);
			}
		}

		// Make sure we don't somehow have any duplicate conflicts
		$conflict = array_map("unserialize", array_unique(array_map("serialize", $conflict)));

		// @TODO: Could also check for high probability of name matches???

		// Get the site name
		$jconfig  = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename');

		// Assign variables to the view
		$this->assign('hzal', $hzal);
		$this->assign('hzad', $hzad);
		$this->assign('plugins', $plugins);
		$this->assign('display_name', $display_name);
		$this->assign('conflict', $conflict);
		$this->assign('sitename', $sitename);
		$this->assignref('juser', $user);

		parent::display($tpl);
	}

	function attach()
	{
	}
}