<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML Languages View class for the Languages component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.6
 */
class LanguagesViewLanguages extends JViewLegacy
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
		require_once JPATH_COMPONENT . '/helpers/languages.php';

		$canDo = LanguagesHelper::getActions();

		Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_LANGUAGES_TITLE'), 'langmanager.png');

		if ($canDo->get('core.create'))
		{
			Toolbar::addNew('language.add');
		}

		if ($canDo->get('core.edit'))
		{
			Toolbar::editList('language.edit');
			Toolbar::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.published') != 2)
			{
				Toolbar::publishList('languages.publish');
				Toolbar::unpublishList('languages.unpublish');
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			Toolbar::deleteList('', 'languages.delete', 'JTOOLBAR_EMPTY_TRASH');
			Toolbar::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			Toolbar::trash('languages.trash');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin'))
		{
			// Add install languages link to the lang installer component
			Toolbar::appendButton('Link', 'extension', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
			Toolbar::divider();

			Toolbar::preferences('com_languages');
			Toolbar::divider();
		}

		Toolbar::help('languages');
	}
}
