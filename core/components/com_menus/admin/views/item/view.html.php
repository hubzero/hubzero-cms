<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * The HTML Menus Menu Item View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class MenusViewItem extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $modules;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->form    = $this->get('Form');
		$this->item    = $this->get('Item');
		$this->modules = $this->get('Modules');
		$this->state   = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
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
		Request::setVar('hidemainmenu', true);

		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == User::get('id'));
		$canDo      = MenusHelper::getActions($this->state->get('filter.parent_id'));

		Toolbar::title(Lang::txt($isNew ? 'COM_MENUS_VIEW_NEW_ITEM_TITLE' : 'COM_MENUS_VIEW_EDIT_ITEM_TITLE'), 'menu-add');

		// If a new item, can save the item.  Allow users with edit permissions to apply changes to prevent returning to grid.
		if ($isNew && $canDo->get('core.create'))
		{
			if ($canDo->get('core.edit'))
			{
				Toolbar::apply('item.apply');
			}
			Toolbar::save('item.save');
		}

		// If not checked out, can save the item.
		if (!$isNew && !$checkedOut && $canDo->get('core.edit'))
		{
			Toolbar::apply('item.apply');
			Toolbar::save('item.save');
		}

		// If the user can create new items, allow them to see Save & New
		if ($canDo->get('core.create'))
		{
			Toolbar::save2new('item.save2new');
		}

		// If an existing item, can save to a copy only if we have create rights.
		if (!$isNew && $canDo->get('core.create'))
		{
			Toolbar::save2copy('item.save2copy');
		}

		if ($isNew)
		{
			Toolbar::cancel('item.cancel');
		}
		else
		{
			Toolbar::cancel('item.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();

		// Get the help information for the menu item.
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
			$url = $help->url;
		}
		Toolbar::help('item'); //$help->key, $help->local, $url);
	}
}
