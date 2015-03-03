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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\MyProjects;

use Hubzero\Module\Module;
use JComponentHelper;
use JFactory;
use Project;

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
		$juser = JFactory::getUser();
		$db = JFactory::getDBO();

		// Get the module parameters
		$params = $this->params;
		$this->moduleclass = $params->get('moduleclass', '');
		$limit = intval($params->get('limit', 5));

		// Load component configs
		$config = JComponentHelper::getParams('com_projects');

		// Load classes
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php');

		// Set filters
		$filters = array(
			'mine'     => 1,
			'limit'    => $limit,
			'start'    => 0,
			'updates'  => 1,
			'sortby'   => 'myprojects',
			'getowner' => 1
		);

		$setup_complete = $config->get('confirm_step', 0) ? 3 : 2;
		$this->filters  = $filters;
		$this->pconfig  = $config;

		// Get a record count
		$obj = new \Components\Projects\Tables\Project($db);
		$this->total = $obj->getCount($filters, false, $juser->get('id'), 0, $setup_complete);

		// Get records
		$this->rows = $obj->getRecords($filters, false, $juser->get('id'), 0, $setup_complete);

		// pass limit to view
		$this->limit = $limit;

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}

