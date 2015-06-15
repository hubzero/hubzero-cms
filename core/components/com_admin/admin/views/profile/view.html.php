<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_admin
 */
class AdminViewProfile extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			App::abort(500, implode("\n", $errors));
			return false;
		}

		$this->form->setValue('password',  null);
		$this->form->setValue('password2', null);

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		Request::setVar('hidemainmenu', 1);

		Toolbar::title(Lang::txt('COM_ADMIN_VIEW_PROFILE_TITLE'), 'user-profile');
		Toolbar::apply('profile.apply');
		Toolbar::save('profile.save');
		Toolbar::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
		Toolbar::divider();
		Toolbar::help('JHELP_ADMIN_USER_PROFILE_EDIT');
	}
}
