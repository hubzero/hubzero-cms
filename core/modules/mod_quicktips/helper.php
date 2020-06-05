<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

		$query = "SELECT a.id, a.title, a.introtext, a.created
				FROM `#__content` AS a
				WHERE (a.state = '1' AND a.checked_out = '0' AND a.sectionid > '0')
				AND (a.publish_up IS NULL OR a.publish_up <= " . $database->quote($now) . ")
				AND (a.publish_down IS NULL OR a.publish_down >= " . $database->quote($now) . ")"
				. ($catid ? " AND (a.catid IN (" . $catid . "))" : '')
				. ($secid ? " AND (a.sectionid IN (" . $secid . "))" : '')
				. " ORDER BY $order LIMIT 1";
		$database->setQuery($query);
		$this->rows = $database->loadObjectList();

		require $this->getLayoutPath();
	}
}
