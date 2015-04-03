<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Templates manager master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'styles';

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
		// Load the submenu.
		TemplatesHelper::addSubmenu(Request::getCmd('view', 'styles'));

		$view   = Request::getCmd('view', 'styles');
		$layout = Request::getCmd('layout', 'default');
		$id     = Request::getInt('id');

		// Check for edit form.
		if ($view == 'style' && $layout == 'edit' && !$this->checkEditId('com_templates.edit.style', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(Route::url('index.php?option=com_templates&view=styles', false));

			return false;
		}

		parent::display();
	}

	/**
	* Preview Template
	*/
	function preview()
	{
		Request::setVar('view', 'prevuuw');
		parent::display();
	}
}
