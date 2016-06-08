<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.view');

/**
 * User link account view class
 */
class UsersViewLink extends JViewLegacy
{
	function display($tpl = null)
	{
		$user = User::getInstance();

		// If this is an auth_link account update, carry on, otherwise raise an error
		if ($user->isGuest()
			|| !$user->hasAttribute('auth_link_id')
			|| !is_numeric($user->username)
			|| !$user->username < 0)
		{
			App::abort('405', 'Method not allowed');
			return;
		}

		// Get and add the js and extra css to the page
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'link.css');
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'providers.css');
		\Hubzero\Document\Assets::addComponentScript('com_users', 'link');

		// Import a few things
		jimport('joomla.user.helper');

		// Look up a few things
		$hzal    = \Hubzero\Auth\Link::find_by_id($user->get("auth_link_id"));
		$hzad    = \Hubzero\Auth\Domain::find_by_id($hzal->auth_domain_id);
		$plugins = Plugin::byType('authentication');

		// Get the display name for the current plugin being used
		Plugin::import('authentication', $hzad->authenticator);
		$plugin       = Plugin::byType('authentication', $hzad->authenticator);
		$pparams      = new \Hubzero\Config\Registry($plugin->params);
		$refl         = new ReflectionClass("plgAuthentication{$plugin->name}");
		$display_name = $pparams->get('display_name', $refl->hasMethod('onGetLinkDescription') ? $refl->getMethod('onGetLinkDescription')->invoke(NULL) : ucfirst($plugin->name));

		// Look for conflicts - first check in the hub accounts
		$profile_conflicts = \Hubzero\User\User::all()
			->whereEquals('email', $hzal->email)
			->rows();

		// Now check the auth_link table
		$link_conflicts = \Hubzero\Auth\Link::find_by_email($hzal->email, array($hzad->id));

		$conflict = array();
		if ($profile_conflicts)
		{
			foreach ($profile_conflicts as $juser)
			{
				$auth_link  = \Hubzero\Auth\Link::find_by_user_id($juser->id);
				$dname      = (is_object($auth_link) && $auth_link->auth_domain_name) ? $auth_link->auth_domain_name : 'hubzero';
				$conflict[] = array("auth_domain_name" => $dname, "name" => $juser->name, "email" => $juser->email);
			}
		}
		if ($link_conflicts)
		{
			foreach ($link_conflicts as $l)
			{
				$juser      = User::getInstance($l['user_id']);
				$conflict[] = array("auth_domain_name" => $l['auth_domain_name'], "name" => $juser->name, "email" => $l['email']);
			}
		}

		// Make sure we don't somehow have any duplicate conflicts
		$conflict = array_map("unserialize", array_unique(array_map("serialize", $conflict)));

		// @TODO: Could also check for high probability of name matches???

		// Get the site name
		$sitename = Config::get('sitename');

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
