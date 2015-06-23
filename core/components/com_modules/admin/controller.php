<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Modules manager master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @since		1.6
 */
class ModulesController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/modules.php';

		// Load the submenu.
		ModulesHelper::addSubmenu(Request::getCmd('view', 'modules'));

		$view   = Request::getCmd('view', 'modules');
		$layout = Request::getCmd('layout', 'default');
		$id     = Request::getInt('id');

		// Check for edit form.
		if ($view == 'module' && $layout == 'edit' && !$this->checkEditId('com_modules.edit.module', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(Route::url('index.php?option=com_modules&view=modules', false));

			return false;
		}

		parent::display();
	}
}
