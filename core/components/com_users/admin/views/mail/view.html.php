<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Users mail view.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class UsersViewMail extends JViewLegacy
{
	/**
	 * @var object form object
	 */
	protected $form;

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->form = $this->get('Form');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		Request::setVar('hidemainmenu', 1);

		Toolbar::title(Lang::txt('COM_USERS_MASS_MAIL'), 'massmail.png');
		Toolbar::custom('mail.send', 'send.png', 'send_f2.png', 'COM_USERS_TOOLBAR_MAIL_SEND_MAIL', false);
		Toolbar::cancel('mail.cancel');
		Toolbar::divider();
		Toolbar::preferences('com_users');
		Toolbar::divider();
		Toolbar::help('mail');
	}
}
