<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * View class for a list of users.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersViewUsers extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			App::abort(500, implode("\n", $errors));
			return false;
		}

		// Include the component HTML helpers.
		Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');

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
		$canDo = UsersHelper::getActions();

		Toolbar::title(Lang::txt('COM_USERS_VIEW_USERS_TITLE'), 'user');

		if ($canDo->get('core.create'))
		{
			//Toolbar::addNew('user.add');
		}

		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('user.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			Toolbar::divider();
			Toolbar::publish('users.approve', 'COM_USERS_TOOLBAR_APPROVE', true);
			Toolbar::unpublish('users.block', 'COM_USERS_TOOLBAR_BLOCK', true);
			Toolbar::custom('users.unblock', 'unblock.png', 'unblock_f2.png', 'COM_USERS_TOOLBAR_UNBLOCK', true);
			Toolbar::divider();
		}

		if ($canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'users.delete');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_users');
			Toolbar::divider();
		}

		Toolbar::help('users');
	}
}
