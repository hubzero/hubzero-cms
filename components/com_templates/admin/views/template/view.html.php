<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a template style.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesViewTemplate extends JViewLegacy
{
	protected $files;
	protected $state;
	protected $template;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->files    = $this->get('Files');
		$this->state    = $this->get('State');
		$this->template = $this->get('Template');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
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
		Request::setVar('hidemainmenu', true);

		$canDo = TemplatesHelper::getActions();

		Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_VIEW_TEMPLATE'), 'thememanager');

		Toolbar::cancel('template.cancel', 'JTOOLBAR_CLOSE');
		Toolbar::divider();
		Toolbar::help('template');
	}
}
