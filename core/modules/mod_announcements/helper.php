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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Announcements;

use Hubzero\Module\Module;
use Lang;
use Date;

/**
 * Module class for displaying announcements
 */
class Helper extends Module
{
	/**
	 * Get a list of content pages
	 *
	 * @return  void
	 */
	private function _getList()
	{
		$db = \App::get('db');

		$catid   = (int) $this->params->get('catid', 0);
		$orderby = 'a.publish_up DESC';
		$limit   = (int) $this->params->get('numitems', 0);
		$limitby = $limit ? ' LIMIT 0,' . $limit : '';

		$now = Date::toSql();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.*, cc.alias as catname, cc.path as catpath, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' WHERE a.state = 1 ' .
			' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
			' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
			' AND cc.id = '. (int) $catid .
			' AND cc.published = 1' .
			' ORDER BY ' . $orderby . ' ' . $limitby;

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		//check if cache diretory is writable as cache files will be created for the announcements
		if ($this->params->get('cache', 1) && !is_writable(PATH_APP . DS . 'cache'))
		{
			echo '<p class="warning">' . Lang::txt('MOD_ANNOUNCEMENTS_ERROR_CACHE_DIR_WRITEABLE') . '</p>';
			return;
		}

		//check if category has been set
		if (!intval($this->params->get('catid', 0)))
		{
			echo '<p class="warning">' . Lang::txt('MOD_ANNOUNCEMENTS_ERROR_NO_CATEGORY') . '</p>';
			return;
		}

		// Push some CSS to the template
		$this->css();

		$this->content   = $this->_getList();
		$this->cid       = (int) $this->params->get('catid', 0);
		$this->container = $this->params->get('container', 'block-announcements');

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
