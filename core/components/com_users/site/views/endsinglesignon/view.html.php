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
 * End single sign on view class
 */
class UsersViewEndsinglesignon extends JViewLegacy
{
	function display($tpl = null)
	{
		// Assign variables to the view
		$authenticator = \Request::getWord('authenticator', false);

		\Hubzero\Document\Assets::addComponentStylesheet('com_user', 'login.css');

		// Get the site name
		$sitename = \Config::get('sitename');

		// Get the display name for the current plugin being used
		$plugin       = Plugin::byType('authentication', $authenticator);
		$pparams      = new \Hubzero\Config\Registry($plugin->params);
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
