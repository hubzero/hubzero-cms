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
 * User note edit view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class UsersViewNote extends JViewLegacy
{
	/**
	 * The edit form.
	 *
	 * @var    JForm
	 * @since  2.5
	 */
	protected $form;

	/**
	 * The item data.
	 *
	 * @var    object
	 * @since  2.5
	 */
	protected $item;

	/**
	 * The model state.
	 *
	 * @var    Object
	 * @since  2.5
	 */
	protected $state;

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
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Get the component HTML helpers
		Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

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
		Request::setVar('hidemainmenu', 1);

		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == User::get('id'));
		$canDo      = UsersHelper::getActions($this->state->get('filter.category_id'), $this->item->id);

		Toolbar::title(Lang::txt('COM_USERS_NOTES'), 'user');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || (count(User::getAuthorisedCategories('com_users', 'core.create')))))
		{
			Toolbar::apply('note.apply');
			Toolbar::save('note.save');
		}

		if (!$checkedOut && (count(User::getAuthorisedCategories('com_users', 'core.create'))))
		{
			Toolbar::save2new('note.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && (count(User::getAuthorisedCategories('com_users', 'core.create')) > 0))
		{
			Toolbar::save2copy('note.save2copy');
		}
		if (empty($this->item->id))
		{
			Toolbar::cancel('note.cancel');
		}
		else
		{
			Toolbar::cancel('note.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();
		Toolbar::help('note');
	}
}
