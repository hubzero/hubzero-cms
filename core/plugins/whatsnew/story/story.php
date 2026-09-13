<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Stories in the What's New listing
 */
class plgWhatsnewStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The area this plugin answers for
	 *
	 * @return  array
	 */
	public function onWhatsnewAreas()
	{
		return array(
			'story' => Lang::txt('PLG_WHATSNEW_STORY')
		);
	}

	/**
	 * Stories published within the period
	 *
	 * Keyed on publish_up rather than created: a story written a fortnight ago
	 * and held for today's slot is new today, which is what a reader means by
	 * the question.
	 *
	 * @param   object   $period
	 * @param   mixed    $limit
	 * @param   integer  $limitstart
	 * @param   array    $areas
	 * @param   array    $tagids
	 * @return  mixed
	 */
	public function onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = array())
	{
		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name]) && !in_array($this->_name, $areas))
			{
				return array();
			}
		}

		$database = App::get('db');

		$fields = "SELECT s.`id`, s.`title`, s.`kicker` AS `text`, s.`publish_up` AS `created`,
			s.`created_by`, s.`alias`, 'story' AS `section`, '' AS `subsection`,
			concat('index.php?option=com_story&id=', s.`id`) AS `href`";

		$count = "SELECT COUNT(s.`id`)";

		$from = " FROM `#__story_stories` AS s
			WHERE s.`state` = 2
				AND s.`publish_up` > " . $database->quote($period->cStartDate) . "
				AND s.`publish_up` < " . $database->quote($period->cEndDate) . "
				AND s.`access` IN (" . implode(',', User::getAuthorisedViewLevels()) . ")";

		if (!$limit)
		{
			$database->setQuery($count . $from);

			return $database->loadResult();
		}

		$order = " ORDER BY s.`publish_up` DESC, s.`title`";
		$order .= ($limit != 'all') ? " LIMIT " . (int) $limitstart . "," . (int) $limit : "";

		$database->setQuery($fields . $from . $order);

		$rows = $database->loadObjectList();

		foreach ($rows as $key => $row)
		{
			$rows[$key]->href = Route::url($this->linkFor($row));
		}

		return $rows;
	}

	/**
	 * A story's dated address
	 *
	 * @param   object  $row
	 * @return  string
	 */
	protected function linkFor($row)
	{
		if (!$row->created || $row->created == '0000-00-00 00:00:00')
		{
			return 'index.php?option=com_story';
		}

		$time = strtotime($row->created);

		return 'index.php?option=com_story&year=' . date('Y', $time)
			. '&month=' . date('m', $time)
			. '&day=' . date('d', $time)
			. '&story=' . $row->alias
			. '&task=article';
	}
}
