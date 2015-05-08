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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\SlidingPanes;

use Hubzero\Module\Module;
use Date;

/**
 * Module class for displaying sliding panes of content
 */
class Helper extends Module
{
	/**
	 * The number of module instances
	 *
	 * @var  integer
	 */
	static $instances = 0;

	/**
	 * Constructor
	 *
	 * @param   object  $params  Registry
	 * @param   object  $module  Database row
	 * @return  void
	 */
	public function __construct($params, $module)
	{
		parent::__construct($params, $module);

		$this->instances++;
	}

	/**
	 * Get a list of content articles
	 *
	 * @return     array
	 */
	private function _getList()
	{
		$db = \JFactory::getDBO();

		$catid 	 = (int) $this->params->get('catid', 0);
		$random  = $this->params->get('random', 0);
		$orderby = $random ? 'RAND()' : 'a.ordering';
		$limit   = (int) $this->params->get('limitslides', 0);
		$limitby = $limit ? ' LIMIT 0,' . $limit : '';

		$now  = Date::toSql();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.* FROM `#__content` AS a' .
			' INNER JOIN `#__categories` AS cc ON cc.id = a.catid' .
			' WHERE a.state = 1 ' .
			' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
			' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
			' AND cc.id = ' . (int) $catid .
			' AND cc.published = 1' .
			' ORDER BY ' . $orderby . ' ' . $limitby;

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		$type = $this->params->get('animation', 'slide');

		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if ($this->instances <= 1)
		{
			// Push some CSS to the template
			$this->css($type . '.css')
			     ->js();
		}

		$id = rand();

		$this->content   = $this->_getList();
		$this->container = $this->params->get('container', 'pane-sliders');

		$this->js("jQuery(document).ready(function($){ $('#" . $this->container . " .panes-content').jSlidingPanes(); });");

		require $this->getLayoutPath();
	}
}