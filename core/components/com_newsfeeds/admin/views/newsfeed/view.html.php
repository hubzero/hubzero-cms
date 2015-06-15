<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a newsfeed.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @since		1.6
 */
class NewsfeedsViewNewsfeed extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
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

		$userId     = User::get('id');
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == User::get('id'));
		// Since we don't track these assets at the item level, use the category id.
		$canDo      = NewsfeedsHelper::getActions($this->item->catid,0);

		Toolbar::title(Lang::txt('COM_NEWSFEEDS_MANAGER_NEWSFEED'), 'newsfeeds.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count(User::getAuthorisedCategories('com_newsfeeds', 'core.create')) > 0))
		{
			Toolbar::apply('newsfeed.apply');
			Toolbar::save('newsfeed.save');
		}
		if (!$checkedOut && count(User::getAuthorisedCategories('com_newsfeeds', 'core.create')) > 0)
		{
			Toolbar::save2new('newsfeed.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			Toolbar::save2copy('newsfeed.save2copy');
		}

		if (empty($this->item->id))
		{
			Toolbar::cancel('newsfeed.cancel');
		}
		else
		{
			Toolbar::cancel('newsfeed.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();
		Toolbar::help('JHELP_COMPONENTS_NEWSFEEDS_FEEDS_EDIT');
	}
}
