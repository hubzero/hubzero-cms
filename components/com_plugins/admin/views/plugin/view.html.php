<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a plugin.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.5
 */
class PluginsViewPlugin extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
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
		Request::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$canDo		= PluginsHelper::getActions();

		Toolbar::title(Lang::txt('COM_PLUGINS_MANAGER_PLUGIN', Lang::txt($this->item->name)), 'plugin');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			Toolbar::apply('plugin.apply');
			Toolbar::save('plugin.save');
		}
		Toolbar::cancel('plugin.cancel', 'JTOOLBAR_CLOSE');
		Toolbar::divider();
		// Get the help information for the plugin item.

		$lang = JFactory::getLanguage();

		$help = $this->get('Help');
		if ($lang->hasKey($help->url)) {
			$debug = $lang->setDebug(false);
			$url = Lang::txt($help->url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}
		Toolbar::help('plugin');// $help->key, false, $url);
	}
}
