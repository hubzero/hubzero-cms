<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of modules.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @since		1.6
 */
class ModulesViewModules extends JViewLegacy
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
			throw new Exception(implode("\n", $errors), 500, E_ERROR);
			return false;
		}

		// Check if there are no matching items
		if (!count($this->items))
		{
			Notify::warning(
				Lang::txt('COM_MODULES_MSG_MANAGE_NO_MODULES')
			);
		}

		$this->addToolbar();

		// Include the component HTML helpers.
		Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = ModulesHelper::getActions();

		Toolbar::title(Lang::txt('COM_MODULES_MANAGER_MODULES'), 'module.png');

		if ($canDo->get('core.create'))
		{
			//Toolbar::addNew('module.add');
			Toolbar::appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_modules&amp;view=select&amp;tmpl=component', 850, 400);
		}

		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('module.edit');
		}

		if ($canDo->get('core.create'))
		{
			Toolbar::custom('modules.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}

		if ($canDo->get('core.edit.state'))
		{
			Toolbar::divider();
			Toolbar::publish('modules.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('modules.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			Toolbar::divider();
			Toolbar::checkin('modules.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'modules.delete', 'JTOOLBAR_EMPTY_TRASH');
			Toolbar::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			Toolbar::trash('modules.trash');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_modules');
			Toolbar::divider();
		}
		Toolbar::help('modules');
	}
}
