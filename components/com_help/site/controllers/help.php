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

namespace Components\Help\Site\Controllers;

use Components\Help\Helpers\Finder;
use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use Exception;

/**
 * Help controller class
 */
class Help extends SiteController
{
	/**
	 * Display Help Article Pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Force help template
		Request::setVar('tmpl', 'help');

		// Get the page we are trying to access
		$page      = Request::getWord('page', 'index');
		$component = Request::getWord('component', 'com_help');
		$name      = str_replace('com_', '', $component);
		$extension = Request::getWord('extension', '');

		$tmpl = \JFactory::getApplication()->getTemplate();
		$lang = \JFactory::getLanguage()->getTag();

		$finalHelpPage = Finder::page($component, $extension, $page);

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
			$pages[] = Finder::pages($component);

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
			throw new Exception(Lang::txt('COM_HELP_PAGE_NOT_FOUND'), 404);
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
}