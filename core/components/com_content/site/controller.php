<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Content Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class ContentController extends JControllerLegacy
{
	public function __construct($config = array())
	{
		// Article frontpage Editor pagebreak proxying:
		if (Request::getCmd('view') === 'article' && Request::getCmd('layout') === 'pagebreak')
		{
			$config['base_path'] = PATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor article proxying:
		elseif (Request::getCmd('view') === 'articles' && Request::getCmd('layout') === 'modal')
		{
			Html::asset('stylesheet', 'adminlist.css', array(), true);
			$config['base_path'] = PATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

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
		$cachable = true;

		Html::behavior('caption');

		// Set the default view name and format from the Request.
		// Note we are using a_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id    = Request::getInt('a_id');
		$vName = Request::getCmd('view', 'categories');
		Request::setVar('view', $vName);

		if (User::get('id') ||
			($_SERVER['REQUEST_METHOD'] == 'POST' &&
				(($vName == 'category' && Request::getCmd('layout') != 'blog') || $vName == 'archive' ))) {
			$cachable = false;
		}

		$safeurlparams = array('catid'=>'INT', 'id'=>'INT', 'cid'=>'ARRAY', 'year'=>'INT', 'month'=>'INT', 'limit'=>'UINT', 'limitstart'=>'UINT',
			'showall'=>'INT', 'return'=>'BASE64', 'filter'=>'STRING', 'filter_order'=>'CMD', 'filter_order_Dir'=>'CMD', 'filter-search'=>'STRING', 'print'=>'BOOLEAN', 'lang'=>'CMD', 'Itemid'=>'INT');

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_content.edit.article', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
