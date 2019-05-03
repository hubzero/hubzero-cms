<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$display_name = $pparams->get('display_name', $refl->hasMethod('onGetLinkDescription') ? $refl->getMethod('onGetLinkDescription')->invoke(null) : ucfirst($plugin->name));

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
