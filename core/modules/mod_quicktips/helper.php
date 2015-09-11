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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\QuickTips;

use Hubzero\Module\Module;
use Cache;
use Date;

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
		if ($content = $this->getCacheContent())
		{
			echo $content;
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
		$database = \App::get('db');

		$catid  = trim($this->params->get('catid'));
		$secid  = trim($this->params->get('secid'));
		$method = trim($this->params->get('method'));

		$now = Date::toSql();

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
