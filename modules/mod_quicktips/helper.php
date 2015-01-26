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

namespace Modules\QuickTips;

use Hubzero\Module\Module;
use JFactory;

/**
 * Module class for displaying tips
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
		$debug = (defined('JDEBUG') && JDEBUG ? true : false);

		if (!$debug && intval($this->params->get('cache', 0)))
		{
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);

			// Module time is in seconds, setLifeTime() is in minutes
			// Some module times may have been set in minutes so we
			// need to account for that.
			$ct = intval($this->params->get('cache_time', 900));
			$ct = (!$ct || $ct == 15 ?: $ct / 60);
			$cache->setLifeTime($ct);

			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}

	/**
	 * Build module content
	 *
	 * @return  void
	 */
	public function run()
	{
		$database = JFactory::getDBO();

		$catid  = trim($this->params->get('catid'));
		$secid  = trim($this->params->get('secid'));
		$method = trim($this->params->get('method'));

		$now = JFactory::getDate();

		if ($method == 'random')
		{
			$order = "RAND()";
		}
		elseif ($method == 'ordering')
		{
			$order = "a.ordering ASC";
		}
		else
		{
			$order = "a.publish_up DESC";
		}

		$query = "SELECT a.id, a.title, a.introtext, a.created"
				. " FROM `#__content` AS a"
				. " WHERE (a.state = '1' AND a.checked_out = '0' AND a.sectionid > '0')"
				. " AND (a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '$now')"
				. " AND (a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '$now')"
				. ($catid ? "\n AND (a.catid IN (" . $catid . "))" : '')
				. ($secid ? "\n AND (a.sectionid IN (" . $secid . "))" : '')
				. " ORDER BY $order LIMIT 1";
		$database->setQuery($query);
		$this->rows = $database->loadObjectList();

		require $this->getLayoutPath();
	}
}
