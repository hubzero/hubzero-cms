<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Displays a list of the installed languages.
 */
class LanguagesViewInstalled extends JViewLegacy
{
	/**
	 * @var object client object
	 */
	protected $client = null;

	/**
	 * @var boolean|JExeption True, if FTP settings should be shown, or an exeption
	 */
	protected $ftp = null;

	/**
	 * @var string option name
	 */
	protected $option = null;

	/**
	 * @var object pagination information
	 */
	protected $pagination = null;

	/**
	 * @var array languages information
	 */
	protected $rows = null;

	/**
	 * @var object user object
	 */
	protected $user = null;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Get data from the model
		$this->ftp        = $this->get('Ftp');
		$this->option     = $this->get('Option');
		$this->pagination = $this->get('Pagination');
		$this->rows       = $this->get('Data');
		$this->state      = $this->get('State');

		Document::setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

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
		require_once JPATH_COMPONENT.'/helpers/languages.php';

		$canDo = LanguagesHelper::getActions();

		Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_INSTALLED_TITLE'), 'langmanager.png');

		if ($canDo->get('core.edit.state')) {
			Toolbar::makeDefault('installed.setDefault');
			Toolbar::divider();
		}

		if ($canDo->get('core.admin')) {
			// Add install languages link to the lang installer component
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Link', 'extension', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
			Toolbar::divider();

			Toolbar::preferences('com_languages');
			Toolbar::divider();
		}

		Toolbar::help('installed');
	}
}
