<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of template styles.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesViewStyles extends JViewLegacy
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
		$this->preview    = Component::params('com_templates')->get('template_positions_display');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			App::abort(500, implode("\n", $errors));
			return false;
		}

			// Check if there are no matching items
		if (!count($this->items))
		{
			JFactory::getApplication()->enqueueMessage(
				Lang::txt('COM_TEMPLATES_MSG_MANAGE_NO_STYLES')
				, 'warning'
			);
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
		$state  = $this->get('State');
		$canDo  = TemplatesHelper::getActions();
		$isSite = ($state->get('filter.client_id') == 0);

		Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_STYLES'), 'thememanager');
		if ($canDo->get('core.edit.state'))
		{
			Toolbar::makeDefault('styles.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
			Toolbar::divider();
		}
		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('style.edit');
		}
		if ($canDo->get('core.create'))
		{
			Toolbar::custom('styles.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			Toolbar::divider();
		}
		if ($canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'styles.delete');
			Toolbar::divider();
		}
		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_templates');
			Toolbar::divider();
		}
		Toolbar::help('styles');
	}
}
