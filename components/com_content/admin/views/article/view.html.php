<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit an article.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class ContentViewArticle extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		if ($this->getLayout() == 'pagebreak') {
			// TODO: This is really dogy - should change this one day.
			$eName		= Request::getVar('e_name');
			$eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
			$document	= JFactory::getDocument();
			$document->setTitle(Lang::txt('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
			$this->assignRef('eName', $eName);
			parent::display($tpl);
			return;
		}

		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo	= ContentHelper::getActions($this->state->get('filter.category_id'));

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
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= ContentHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		Toolbar::title(Lang::txt('COM_CONTENT_PAGE_'.($checkedOut ? 'VIEW_ARTICLE' : ($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE'))), 'article-add.png');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_content', 'core.create')) > 0)) {
			Toolbar::apply('article.apply');
			Toolbar::save('article.save');
			Toolbar::save2new('article.save2new');
			Toolbar::cancel('article.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					Toolbar::apply('article.apply');
					Toolbar::save('article.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						Toolbar::save2new('article.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				Toolbar::save2copy('article.save2copy');
			}

			Toolbar::cancel('article.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();
		Toolbar::help('article');
	}
}
