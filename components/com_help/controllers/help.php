<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Help\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use Exception;

/**
 * Help controller class
 */
class Help extends SiteController
{
	/**
	 * Help file extension
	 *
	 * @var  string
	 */
	protected $layoutExt = 'phtml';

	/**
	 * Display Help Article Pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Force help template
		\JRequest::setVar('tmpl', 'help');

		// Get the page we are trying to access
		$page      = \JRequest::getWord('page', 'index');
		$component = \JRequest::getWord('component', 'com_help');
		$name      = str_replace('com_', '', $component);
		$extension = \JRequest::getWord('extension', '');

		$tmpl = \JFactory::getApplication()->getTemplate();
		$lang = \JFactory::getLanguage()->getTag();

		$paths = array(
			// Template override help page
			JPATH_ROOT . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $page . DS . 'help' . DS . $lang . DS . 'index.' . $this->layoutExt,
			JPATH_ROOT . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lang . DS . $page . '.' . $this->layoutExt
		);

		// Path to help page
		if (file_exists(JPATH_SITE . DS . 'components' . DS . $component . DS . 'site' . DS . 'help'))
		{
			$paths[] = JPATH_SITE . DS . 'components' . DS . $component . DS . 'site' . DS . 'help' . DS . $lng . DS . $page . '.' . $this->layoutExt;
		}
		else
		{
			$paths[] = JPATH_SITE . DS . 'components' . DS . $component . DS . 'help' . DS . $lng . DS . $page . '.' . $this->layoutExt;
		}
		$paths[] = JPATH_ROOT . DS . 'plugins' . DS . $name . DS . $page . DS . 'help' . DS . $lang . DS . 'index.' . $this->layoutExt;

		// If we have an extension
		if (isset($extension) && $extension != '')
		{
			$paths[2] = JPATH_ROOT . DS . 'plugins' . DS . $name . DS . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . $this->layoutExt;
			$paths[0] = JPATH_ROOT . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . $this->layoutExt;
		}

		$finalHelpPage = '';

		// Determine path for help page
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$finalHelpPage = $path;
			}
		}

		$content = '';

		// If we have an existing page
		if ($finalHelpPage != '')
		{
			ob_start();
			require_once($finalHelpPage);
			$content = ob_get_contents();
			ob_end_clean();
		}
		else if (isset($component) && $component != '' && $page == 'index')
		{
			// Get list of component pages
			$pages[] = $this->helpPagesForComponent($component);

			//display page
			$view = with(new View(array(
					'name'   => $this->_controller,
					'layout' => 'index'
				)))
				->set('option', $this->_option)
				->set('controller', $this->_controller)
				->set('layoutExt', $this->layoutExt)
				->set('pages', $pages);

			$content = $view->loadTemplate();
		}
		else
		{
			// Raise error to avoid security bug
			throw new Exception(\JText::_('COM_HELP_PAGE_NOT_FOUND'), 404);
		}

		// Set vars for views
		$this->view
			->set('modified', filemtime($finalHelpPage))
			->set('component', $component)
			->set('extension', $extension)
			->set('content', $content)
			->set('page', $page)
			->display();
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   string  $component  Component to get pages for
	 * @return  array
	 */
	private function helpPagesForComponent($component)
	{
		// Get component name from database
		$this->database->setQuery(
			"SELECT `name`
			FROM `#__extensions`
			WHERE `type`=" . $this->database->quote('component') . "
			AND `element`=" . $this->database->quote($component) . "
			AND `enabled`=1"
		);
		$name = $this->database->loadResult();

		// Make sure we have a component
		if ($name == '')
		{
			$name = str_replace('com_', '', $component);

			return array(
				'name'   => ucfirst($name),
				'option' => $component,
				'pages'  => array()
			);
		}

		// Path to help pages
		$helpPagesPath = JPATH_SITE . DS . 'components' . DS . $component . DS . 'help' . DS . \JFactory::getLanguage()->getTag();

		// Make sure directory exists
		$pages = array();
		if (is_dir($helpPagesPath))
		{
			// Get help pages for this component
			jimport('joomla.filesystem.folder');
			$pages = \JFolder::files($helpPagesPath , '.' . $this->layoutExt);
		}

		// Return pages
		return array(
			'name'   => $name,
			'option' => $component,
			'pages'  => $pages
		);
	}
}