<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			' AND (a.publish_up IS NULL OR a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ' ) ' .
			' AND (a.publish_down IS NULL OR a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ' )' .
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
