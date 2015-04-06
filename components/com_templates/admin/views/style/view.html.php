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
class TemplatesViewStyle extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->form  = $this->get('Form');

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

		$isNew = ($this->item->id == 0);
		$canDo = TemplatesHelper::getActions();

		Toolbar::title(
			$isNew ? Lang::txt('COM_TEMPLATES_MANAGER_ADD_STYLE')
			: Lang::txt('COM_TEMPLATES_MANAGER_EDIT_STYLE'), 'thememanager'
		);

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			Toolbar::apply('style.apply');
			Toolbar::save('style.save');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			Toolbar::save2copy('style.save2copy');
		}

		if (empty($this->item->id))
		{
			Toolbar::cancel('style.cancel');
		}
		else
		{
			Toolbar::cancel('style.cancel', 'JTOOLBAR_CLOSE');
		}
		Toolbar::divider();
		// Get the help information for the template item.

		$lang = Lang::getRoot();

		$help = $this->get('Help');
		if ($lang->hasKey($help->url))
		{
			$debug = $lang->setDebug(false);
			$url = Lang::txt($help->url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}
		Toolbar::help('style'); //$help->key, false, $url);
	}
}
