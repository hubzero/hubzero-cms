<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * HTML View class for the Categories component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 */
class CategoriesViewCategory extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = CategoriesHelper::getActions($this->state->get('category.component'));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		parent::display($tpl);
		Request::setVar('hidemainmenu', true);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$extension = Request::getCmd('extension');
		$userId     = User::get('id');

		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Avoid nonsense situation.
		if ($extension == 'com_categories') {
			return;
		}

		// The extension can be in the form com_foo.section
		$parts = explode('.', $extension);
		$component = $parts[0];
		$section = (count($parts) > 1) ? $parts[1] : null;

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Lang::getRoot();
			$lang->load($component, JPATH_BASE, null, false, false)
		||	$lang->load($component, PATH_CORE.'/components/'.$component.'/admin', null, false, false)
		||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($component, PATH_CORE.'/components/'.$component.'/admin', $lang->getDefault(), false, false);

		// Load the category helper.
		require_once JPATH_COMPONENT.'/helpers/categories.php';

		// Get the results for each action.
		$canDo = CategoriesHelper::getActions($component, $this->item->id);

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = $component.($section?"_$section":'').'_CATEGORY_'.($isNew?'ADD':'EDIT').'_TITLE'))
		{
			$title = Lang::txt($component_title_key);
		}
		// Else if the component section string exits, let's use it
		elseif ($lang->hasKey($component_section_key = $component.($section?"_$section":'')))
		{
			$title = Lang::txt( 'COM_CATEGORIES_CATEGORY_'.($isNew?'ADD':'EDIT').'_TITLE', $this->escape(Lang::txt($component_section_key)));
		}
		// Else use the base title
		else
		{
			$title = Lang::txt('COM_CATEGORIES_CATEGORY_BASE_'.($isNew?'ADD':'EDIT').'_TITLE');
		}

		// Load specific css component
		Html::asset('stylesheet', $component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		Toolbar::title($title, 'category-'.($isNew?'add':'edit').' '.substr($component, 4).($section?"-$section":'').'-category-'.($isNew?'add':'edit'));

		// For new records, check the create permission.
		if ($isNew && (count(User::getAuthorisedCategories($component, 'core.create')) > 0))
		{
			Toolbar::apply('category.apply');
			Toolbar::save('category.save');
			Toolbar::save2new('category.save2new');
		}
		// If not checked out, can save the item.
		elseif (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId)))
		{
			Toolbar::apply('category.apply');
			Toolbar::save('category.save');
			if ($canDo->get('core.create'))
			{
				Toolbar::save2new('category.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			Toolbar::save2copy('category.save2copy');
		}

		if (empty($this->item->id))
		{
			Toolbar::cancel('category.cancel');
		}
		else
		{
			Toolbar::cancel('category.cancel', 'JTOOLBAR_CLOSE');
		}

		Toolbar::divider();

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORY_'.($isNew?'ADD':'EDIT').'_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component, 4).($section?"_$section":'')).'_CATEGORY_'.($isNew?'ADD':'EDIT');
		}

		// Get help for the category/section view for the component by
		// -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		// -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		// -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		if ($lang->hasKey($lang_help_url = strtoupper($component).'_HELP_URL'))
		{
			$debug = $lang->setDebug(false);
			$url = Lang::txt($lang_help_url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}
		Toolbar::help('category'); //$ref_key, Component::params( $component )->exists('helpURL'), $url, $component);
	}
}
