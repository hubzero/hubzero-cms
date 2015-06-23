<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * View to edit an language override
 */
class LanguagesViewOverride extends JViewLegacy
{
	/**
	 * The form to use for the view
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $form;

	/**
	 * The item to edit
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $state;

	/**
	 * Displays the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 * @return  void
	 */
	public function display($tpl = null)
	{
		Html::behavior('framework');

		\Hubzero\Document\Assets::addComponentStylesheet('com_languages', 'overrider.css');
		\Hubzero\Document\Assets::addComponentScript('com_languages', 'overrider.js');

		//Document::addStyleSheet(Request::root().'media/overrider/css/overrider.css');
		//Document::addScript(Request::root().'media/overrider/js/overrider.js');

		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));

			return;
		}

		// Check whether the cache has to be refreshed
		$cached_time = User::getState('com_languages.overrides.cachedtime.'.$this->state->get('filter.client').'.'.$this->state->get('filter.language'), 0);
		if (time() - $cached_time > 60 * 5)
		{
			$this->state->set('cache_expired', true);
		}

		// Add strings for translations in Javascript
		JText::script('COM_LANGUAGES_VIEW_OVERRIDE_NO_RESULTS');
		JText::script('COM_LANGUAGES_VIEW_OVERRIDE_REQUEST_ERROR');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Adds the page title and toolbar
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		Request::setVar('hidemainmenu', true);

		$canDo = LanguagesHelper::getActions();

		Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_TITLE'), 'langmanager');

		if ($canDo->get('core.edit'))
		{
			Toolbar::apply('override.apply');
			Toolbar::save('override.save');
		}

		// This component does not support Save as Copy

		if ($canDo->get('core.edit') && $canDo->get('core.create'))
		{
			Toolbar::save2new('override.save2new');
		}

		if (empty($this->item->key))
		{
			Toolbar::cancel('override.cancel');
		}
		else
		{
			Toolbar::cancel('override.cancel', 'JTOOLBAR_CLOSE');
		}
		Toolbar::divider();
		Toolbar::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_OVERRIDES_EDIT');
	}
}
