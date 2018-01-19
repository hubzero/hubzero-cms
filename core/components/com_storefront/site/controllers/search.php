<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Storefront\Site\Controllers;

use Components\Storefront\Models\Warehouse;
use Pathway;
use Request;
use Lang;
use User;
use App;

/**
 * Product browsing controller class
 */
class Search extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->warehouse = new Warehouse();

		$this->warehouse->addAccessLevels(User::getAuthorisedViewLevels());
		$this->warehouse->addAccessGroups(User::getAuthorisedGroups());

		if (is_numeric(User::get('id')))
		{
			$this->warehouse->addUserScope(User::get('id'));
		}

		// Get the task
		$this->_task  = Request::getCmd('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'search';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Display default page
	 *
	 * @return  void
	 */
	public function searchTask()
	{
		// Incoming
		$search = Request::getVar('q', '');
		$cId = Request::getVar('cId', '');

		if ($cId)
		{
			$this->warehouse->addLookupCollection($cId);
		}

		$products = $this->warehouse->getProducts('rows', true, array('sort' => 'title', 'search' => $search));
		$this->view->products = $products;
		$this->view->config = $this->config;
		$this->view->search = $search;

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt('Search')
		);

		$this->view->display();
	}
}
