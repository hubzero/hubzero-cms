<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;

if (!function_exists('stem'))
{
	/**
	 * Stem a string
	 *
	 * @param  string $str
	 * @return string
	 */
	function stem($str)
	{
		return $str;
	}
}

foreach (array('request', 'result', 'terms', 'authorization', 'documentmetadata') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . $mdl . '.php';
}
foreach (array('assoc', 'assoclist', 'assocscalar', 'blank', 'set', 'sql') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . 'result' . DS . $mdl . '.php';
}

/**
 * Search controller class
 */
class Basic extends AdminController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->display();
	}
}

