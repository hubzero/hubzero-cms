<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			' AND (a.publish_up IS NULL OR a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
			' AND (a.publish_down IS NULL OR a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
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
