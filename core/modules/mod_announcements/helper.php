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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
