<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * HTML View class for the Languages component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.5
 */
class LanguagesViewLanguage extends JViewLegacy
{
	public $item;
	public $form;
	public $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->state = $this->get('State');

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

		Request::setVar('hidemainmenu', 1);
		$isNew = empty($this->item->lang_id);
		$canDo = LanguagesHelper::getActions();

		Toolbar::title(Lang::txt($isNew ? 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE' : 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_EDIT_TITLE'), 'langmanager.png');

		// If a new item, can save.
		if ($isNew && $canDo->get('core.create'))
		{
			Toolbar::save('language.save');
		}

		//If an existing item, allow to Apply and Save.
		if (!$isNew && $canDo->get('core.edit'))
		{
			Toolbar::apply('language.apply');
			Toolbar::save('language.save');
		}

		// If an existing item, can save to a copy only if we have create rights.
		if ($canDo->get('core.create'))
		{
			Toolbar::save2new('language.save2new');
		}

		if ($isNew)
		{
			Toolbar::cancel('language.cancel');
		}
		else
		{
			Toolbar::cancel('language.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();
		Toolbar::help('language');
	}
}
