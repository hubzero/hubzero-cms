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

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;
use Pathway;
use Lang;

/**
 * API Controller
 */
class Api extends SiteController
{
	/**
	 * General intro display
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		$this->_buildPathway();

		$this->view->display();
	}

	/**
	 * Documentation
	 * 
	 * @return  void
	 */
	public function docsTask()
	{
		// build pathway
		$this->_buildPathway();

		// generate documentation
		$generator = new \Hubzero\Api\Doc\Generator(!\Config::get('debug'));
		$this->view->documentation = $generator->output('array');

		// render view
		$this->view->display();
	}

	/**
	 * Endpoint Documentation
	 * 
	 * @return  void
	 */
	public function endpointTask()
	{
		// build pathway
		$this->_buildPathway();

		$active = Request::getVar('active', '');

		// generate documentation
		$generator = new \Hubzero\Api\Doc\Generator(!\Config::get('debug'));
		$documentation = $generator->output('array');

		if (!isset($documentation['sections'][$active]))
		{
			throw new \Exception(Lang::txt('Endpoint not found'), 404);
		}

		$this->view->active = $active;
		$this->view->documentation = $documentation;

		// render view
		$this->view->display();
	}

	/**
	 * Console 
	 * 
	 * @return  void
	 */
	public function consoleTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Status 
	 * 
	 * @return  void
	 */
	public function statusTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Build Pathway
	 * 
	 * @return  void
	 */
	private function _buildPathway()
	{
		// create breadcrumbs
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_controller)),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);

		if (isset($this->_task) && $this->_task != '')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option . '_' . $this->_controller . '_' . $this->_task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
			);
		}
	}
}