<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of articles.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class ContentViewArticles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->authors		= $this->get('Authors');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', Lang::txt('J1'));
		$options[]	= JHtml::_('select.option', '2', Lang::txt('J2'));
		$options[]	= JHtml::_('select.option', '3', Lang::txt('J3'));
		$options[]	= JHtml::_('select.option', '4', Lang::txt('J4'));
		$options[]	= JHtml::_('select.option', '5', Lang::txt('J5'));
		$options[]	= JHtml::_('select.option', '6', Lang::txt('J6'));
		$options[]	= JHtml::_('select.option', '7', Lang::txt('J7'));
		$options[]	= JHtml::_('select.option', '8', Lang::txt('J8'));
		$options[]	= JHtml::_('select.option', '9', Lang::txt('J9'));
		$options[]	= JHtml::_('select.option', '10', Lang::txt('J10'));

		$this->f_levels = $options;

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions($this->state->get('filter.category_id'));
		$user  = JFactory::getUser();
		Toolbar::title(Lang::txt('COM_CONTENT_ARTICLES_TITLE'), 'article.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_content', 'core.create'))) > 0 ) {
			Toolbar::addNew('article.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			Toolbar::editList('article.edit');
		}

		if ($canDo->get('core.edit.state')) {
			Toolbar::divider();
			Toolbar::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			Toolbar::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			Toolbar::divider();
			Toolbar::archiveList('articles.archive');
			Toolbar::checkin('articles.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			Toolbar::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			Toolbar::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			Toolbar::trash('articles.trash');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin')) {
			Toolbar::preferences('com_content');
			Toolbar::divider();
		}

		Toolbar::help('articles');
	}
}
