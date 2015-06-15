<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class ContentViewFeatured extends JViewLegacy
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

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
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
		$state = $this->get('State');
		$canDo = ContentHelper::getActions($this->state->get('filter.category_id'));

		Toolbar::title(Lang::txt('COM_CONTENT_FEATURED_TITLE'), 'featured.png');

		if ($canDo->get('core.create'))
		{
			Toolbar::addNew('article.add');
		}
		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('article.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			Toolbar::divider();
			Toolbar::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			Toolbar::divider();
			Toolbar::archiveList('articles.archive');
			Toolbar::checkin('articles.checkin');
			Toolbar::custom('featured.delete', 'remove.png', 'remove_f2.png', 'JTOOLBAR_REMOVE', true);
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			Toolbar::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			Toolbar::divider();
			Toolbar::trash('articles.trash');
		}

		if ($canDo->get('core.admin'))
		{
			Toolbar::preferences('com_content');
			Toolbar::divider();
		}
		Toolbar::help('featured');
	}
}
