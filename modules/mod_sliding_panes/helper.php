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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying sliding panes of content
 */
class modSlidingPanes extends \Hubzero\Module\Module
{
	/**
	 * Get a list of content articles
	 *
	 * @return     array
	 */
	private function _getList()
	{
		$db = JFactory::getDBO();

		$catid 	 = (int) $this->params->get('catid', 0);
		$random  = $this->params->get('random', 0);
		$orderby = $random ? 'RAND()' : 'a.ordering';
		$limit   = (int) $this->params->get('limitslides', 0);
		$limitby = $limit ? ' LIMIT 0,' . $limit : '';

		$date = JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.* FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
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
		if (!$this->multiple_instances)
		{
			// Push some CSS to the template
			$this->css($type . '.css');
			$this->js();
		}

		$id = rand();

		$this->content = $this->_getList();

		$this->container = $this->params->get('container', 'pane-sliders');

		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$js = "jQuery(document).ready(function($){ $('#" . $this->container . " .panes-content').jSlidingPanes(); });";
		}
		else
		{
			$js = "window.addEvent('domready', function(){
				if ($('" . $this->container . "')) {
					myTabs" . $id . " = new ModSlidingPanes('" . $this->container . "', " . $this->params->get('rotate', 1) . ");

					// this sets it up to work even if it's width isn't a set amount of pixels
					window.addEvent('resize', myTabs" . $id . ".recalcWidths.bind(myTabs" . $id . "));
				}
			});";
		}

		$this->js($js);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}