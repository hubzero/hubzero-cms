<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * View class for a list of template styles.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesViewTemplates extends JViewLegacy
{
	/**
	 * @var		array
	 * @since	1.6
	 */
	protected $items;

	/**
	 * @var		object
	 * @since	1.6
	 */
	protected $pagination;

	/**
	 * @var		object
	 * @since	1.6
	 */
	protected $state;

	/**
	 * Display the view.
	 *
	 * @param	string
	 *
	 * @return	void
	 * @since	1.6
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
			Notify::warning(
				Lang::txt('COM_TEMPLATES_MSG_MANAGE_NO_TEMPLATES')
			);
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = TemplatesHelper::getActions();

		Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_TEMPLATES'), 'thememanager');
		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_templates');
			Toolbar::divider();
		}
		Toolbar::help('templates');
	}
}
