<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class CategoriesViewCategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter.
		$options = array();
		$options[] = Html::select('option', '1', Lang::txt('J1'));
		$options[] = Html::select('option', '2', Lang::txt('J2'));
		$options[] = Html::select('option', '3', Lang::txt('J3'));
		$options[] = Html::select('option', '4', Lang::txt('J4'));
		$options[] = Html::select('option', '5', Lang::txt('J5'));
		$options[] = Html::select('option', '6', Lang::txt('J6'));
		$options[] = Html::select('option', '7', Lang::txt('J7'));
		$options[] = Html::select('option', '8', Lang::txt('J8'));
		$options[] = Html::select('option', '9', Lang::txt('J9'));
		$options[] = Html::select('option', '10', Lang::txt('J10'));

		$this->f_levels = $options;

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
		// Initialise variables.
		$categoryId = $this->state->get('filter.category_id');
		$component  = $this->state->get('filter.component');
		$section    = $this->state->get('filter.section');
		$canDo      = null;

		// Avoid nonsense situation.
		if ($component == 'com_categories')
		{
			return;
		}

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
			Lang::load($component, JPATH_BASE, null, false, true)
		||	Lang::load($component, PATH_CORE . '/components/' . $component . '/admin', null, false, true);

		// Load the category helper.
		require_once JPATH_COMPONENT.'/helpers/categories.php';

		// Get the results for each action.
		$canDo = CategoriesHelper::getActions($component, $categoryId);

		// If a component categories title string is present, let's use it.
		if (Lang::hasKey($component_title_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_TITLE'))
		{
			$title = Lang::txt($component_title_key);
		}
		// Else if the component section string exits, let's use it
		elseif (Lang::hasKey($component_section_key = strtoupper($component.($section?"_$section":''))))
		{
			$title = Lang::txt( 'COM_CATEGORIES_CATEGORIES_TITLE', $this->escape(Lang::txt($component_section_key)));
		}
		// Else use the base title
		else
		{
			$title = Lang::txt('COM_CATEGORIES_CATEGORIES_BASE_TITLE');
		}

		// Load specific css component
		Html::asset('stylesheet', $component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		Toolbar::title($title, 'categories '.substr($component, 4).($section?"-$section":'').'-categories');

		if ($canDo->get('core.create') || (count(User::getAuthorisedCategories($component, 'core.create'))) > 0 )
		{
			 Toolbar::addNew('category.add');
		}

		if ($canDo->get('core.edit' ) || $canDo->get('core.edit.own'))
		{
			Toolbar::editList('category.edit');
			Toolbar::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			Toolbar::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			Toolbar::divider();
			Toolbar::archiveList('categories.archive');
		}

		if (User::authorise('core.admin'))
		{
			Toolbar::checkin('categories.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component))
		{
			Toolbar::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			Toolbar::trash('categories.trash');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin'))
		{
			Toolbar::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
			Toolbar::preferences($component);
			Toolbar::divider();
		}

		// Compute the ref_key if it does exist in the component
		if (!Lang::hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component, 4).($section?"_$section":'')).'_CATEGORIES';
		}

		// Get help for the categories view for the component by
		// -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		// -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		// -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		if (Lang::hasKey($lang_help_url = strtoupper($component).'_HELP_URL'))
		{
			$debug = Lang::setDebug(false);
			$url = Lang::txt($lang_help_url);
			Lang::setDebug($debug);
		}
		else
		{
			$url = null;
		}
		Toolbar::help('categories'); //$ref_key, Component::params( $component )->exists('helpURL'), $url);
	}
}
