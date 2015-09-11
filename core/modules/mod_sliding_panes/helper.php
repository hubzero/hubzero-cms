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

		self::$instances++;
	}

	/**
	 * Get a list of content articles
	 *
	 * @return     array
	 */
	private function _getList()
	{
		$db = \App::get('db');

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
		if (self::$instances <= 1)
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