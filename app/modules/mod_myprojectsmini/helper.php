<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyProjects;

use Hubzero\Module\Module;
use Components\Projects\Tables\Project;
use Component;
use User;

/**
 * Module class for displaying a user's projects
 */
class Helper extends Module
{
	/**
	 * Display module content
	 * 
	 * @return  void
	 */
	public function display()
	{
		$db = \App::get('db');

		// Get the module parameters
		$params = $this->params;
		$this->moduleclass = $params->get('moduleclass', '');
		$limit = intval($params->get('limit', 5));

		// Load component configs
		$config = Component::params('com_projects');

		// Load classes
		require_once Component::path('com_projects') . DS . 'tables' . DS . 'project.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';

		// Set filters
		$filters = array(
			'mine'     => 1,
			'limit'    => $limit,
			'start'    => 0,
			'updates'  => 1,
			'sortby'   => 'myprojects',
			'getowner' => 1
		);

		if (!$this->params->get('include_archived', 1))
		{
			$filters['filterby'] = 'active';
		}

		$setup_complete = $config->get('confirm_step', 0) ? 3 : 2;
		$this->filters  = $filters;
		$this->pconfig  = $config;

		// Get a record count
		$obj = new Project($db);
		$this->total = $obj->getCount($filters, false, User::get('id'), 0, $setup_complete);

		// Get records
		$this->rows = $obj->getRecords($filters, false, User::get('id'), 0, $setup_complete);

		// pass limit to view
		$this->limit = $limit;

		require $this->getLayoutPath();
	}
}
