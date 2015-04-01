<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once dirname(__FILE__).'/../default/view.php';

/**
 * Extension Manager Manage View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class InstallerViewDiscover extends InstallerViewDefault
{
	/**
	 * @since	1.6
	 */
	public function display($tpl=null)
	{
		// Get data from the model
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo = InstallerHelper::getActions();

		// Set toolbar items for the page
		Toolbar::custom('discover.install', 'upload', 'upload', 'JTOOLBAR_INSTALL', true, false);
		Toolbar::custom('discover.refresh', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_DISCOVER', false, false);
		Toolbar::custom('discover.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
		Toolbar::divider();
		parent::addToolbar();
		Toolbar::help('discover');
	}
}
