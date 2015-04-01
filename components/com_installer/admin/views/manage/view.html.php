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
class InstallerViewManage extends InstallerViewDefault
{
	protected $items;
	protected $pagination;
	protected $form;
	protected $state;

	/**
	 * @since	1.6
	 */
	public function display($tpl=null)
	{
		// Get data from the model
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->form       = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			App::abort(500, implode("\n", $errors));
			return false;
		}

		//Check if there are no matching items
		if (!count($this->items))
		{
			JFactory::getApplication()->enqueueMessage(
				Lang::txt('COM_INSTALLER_MSG_MANAGE_NOEXTENSION')
				, 'warning'
			);
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Display the view
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
		if ($canDo->get('core.edit.state'))
		{
			Toolbar::publish('manage.publish', 'JTOOLBAR_ENABLE', true);
			Toolbar::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE', true);
			Toolbar::divider();
		}
		Toolbar::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', true);
		Toolbar::divider();
		if ($canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'manage.remove', 'JTOOLBAR_UNINSTALL');
			Toolbar::divider();
		}
		parent::addToolbar();
		Toolbar::help('manage');
	}

	/**
	 * Creates the content for the tooltip which shows compatibility information
	 *
	 * @var  string  $system_data  System_data information
	 *
	 * @since  2.5.28
	 *
	 * @return  string  Content for tooltip
	 */
	protected function createCompatibilityInfo($system_data)
	{
		$system_data = json_decode($system_data);

		if (empty($system_data->compatibility))
		{
			return '';
		}

		$compatibility = $system_data->compatibility;

		$info = Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_INSTALLED',
					$compatibility->installed->version,
					implode(', ', $compatibility->installed->value)
				)
				. '<br/>'
				. Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_AVAILABLE',
					$compatibility->available->version,
					implode(', ', $compatibility->available->value)
				);

		return $info;
	}
}
