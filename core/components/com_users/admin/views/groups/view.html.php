<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * View class for a list of user groups.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersViewGroups extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			App::abort(500, implode("\n", $errors));
			return false;
		}

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
		$canDo	= UsersHelper::getActions();

		Toolbar::title(Lang::txt('COM_USERS_VIEW_GROUPS_TITLE'), 'groups');

		if ($canDo->get('core.create')) {
			Toolbar::addNew('group.add');
		}
		if ($canDo->get('core.edit')) {
			Toolbar::editList('group.edit');
			Toolbar::divider();
		}
		if ($canDo->get('core.delete')) {
			Toolbar::deleteList('', 'groups.delete');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin')) {
			Toolbar::preferences('com_users');
			Toolbar::divider();
		}
		Toolbar::help('groups');
	}
}
