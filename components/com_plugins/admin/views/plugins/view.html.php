<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of plugins.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.5
 */
class PluginsViewPlugins extends JViewLegacy
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
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

			// Check if there are no matching items
		if(!count($this->items)){
			JFactory::getApplication()->enqueueMessage(
				Lang::txt('COM_PLUGINS_MSG_MANAGE_NO_PLUGINS')
				, 'warning'
			);
		}

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
		$state	= $this->get('State');
		$canDo	= PluginsHelper::getActions();

		Toolbar::title(Lang::txt('COM_PLUGINS_MANAGER_PLUGINS'), 'plugin');

		if ($canDo->get('core.edit')) {
			Toolbar::editList('plugin.edit');
		}

		if ($canDo->get('core.edit.state')) {
			Toolbar::divider();
			Toolbar::publish('plugins.publish', 'JTOOLBAR_ENABLE', true);
			Toolbar::unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE', true);
			Toolbar::divider();
			Toolbar::checkin('plugins.checkin');
		}

		if ($canDo->get('core.admin')) {
			Toolbar::divider();
			Toolbar::preferences('com_plugins');
		}
		Toolbar::divider();
		Toolbar::help('plugins');
	}
}
