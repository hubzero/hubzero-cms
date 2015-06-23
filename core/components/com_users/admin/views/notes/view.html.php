<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_HZEXEC_') or die();

/**
 * User notes list view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class UsersViewNotes extends JViewLegacy
{
	/**
	 * A list of user note objects.
	 *
	 * @var    array
	 * @since  2.5
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var    Pagination
	 * @since  2.5
	 */
	protected $pagination;

	/**
	 * The model state.
	 *
	 * @var    Object
	 * @since  2.5
	 */
	protected $state;

	/**
	 * The model state.
	 *
	 * @var    JUser
	 * @since  2.5
	 */
	protected $user;

	/**
	 * Override the display method for the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		// Initialise view variables.
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = $this->get('User');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Get the component HTML helpers
		Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// turn parameters into registry objects
		foreach ($this->items as $item)
		{
			$item->cparams = new \Hubzero\Config\Registry($item->category_params);
		}

		parent::display($tpl);

		$this->addToolbar();
	}

	/**
	 * Display the toolbar.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function addToolbar()
	{
		$canDo = UsersHelper::getActions();

		Toolbar::title(Lang::txt('COM_USERS_VIEW_NOTES_TITLE'), 'user');

		if ($canDo->get('core.create'))
		{
			Toolbar::addNew('note.add');
		}

		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('note.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			Toolbar::divider();
			Toolbar::publish('notes.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('notes.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			Toolbar::divider();
			Toolbar::archiveList('notes.archive');
			Toolbar::checkin('notes.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'notes.delete', 'JTOOLBAR_EMPTY_TRASH');
			Toolbar::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			Toolbar::trash('notes.trash');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_users');
			Toolbar::divider();
		}
		Toolbar::help('notes');
	}
}
