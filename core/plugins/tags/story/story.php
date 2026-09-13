<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Stories in tag listings
 *
 * A story is tagged against the 'story' table in com_tags, so a topic and a
 * tag stay separate things: the topic is where an editor filed it, the tag is
 * what anybody thought it was about.
 */
class plgTagsStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Stories matching a set of tags
	 *
	 * @param   array    $tags
	 * @param   mixed    $limit
	 * @param   integer  $limitstart
	 * @param   string   $sort
	 * @param   mixed    $areas
	 * @return  mixed
	 */
	public function onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_STORY'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		if (is_array($areas) && count($areas) && !isset($areas[$this->_name]) && !in_array($this->_name, $areas))
		{
			return $response;
		}

		$database = App::get('db');

		$ids = array();

		foreach ($tags as $tag)
		{
			$ids[] = (int) $tag->get('id');
		}

		if (!$ids)
		{
			return $response;
		}

		$ids = implode(',', $ids);

		$levels = implode(',', User::getAuthorisedViewLevels());

		// Ranked by how many of the asked-for tags each story carries, so a
		// story matching three of three sorts above one matching one of three.
		$from = " FROM `#__story_stories` AS s
			INNER JOIN `#__tags_object` AS o
				ON o.`objectid` = s.`id` AND o.`tbl` = 'story'
			WHERE s.`state` = 2
				AND s.`access` IN (" . ($levels ?: '1') . ")
				AND o.`tagid` IN (" . $ids . ")
			GROUP BY s.`id`";

		$fields = "SELECT s.`id`, s.`title`, s.`kicker` AS `description`, s.`alias`,
			s.`publish_up` AS `created`, s.`created_by`, 'story' AS `section`,
			COUNT(DISTINCT o.`tagid`) AS `matches`,
			concat('index.php?option=com_story&id=', s.`id`) AS `href`";

		$database->setQuery("SELECT COUNT(*) FROM (SELECT s.`id`" . $from . ") AS c;");

		$response['total'] = (int) $database->loadResult();

		if (!$limit)
		{
			return $response;
		}

		$order = " ORDER BY `matches` DESC, s.`publish_up` DESC";
		$order .= ($limit != 'all') ? " LIMIT " . (int) $limitstart . "," . (int) $limit : "";

		$database->setQuery($fields . $from . $order);

		$response['results'] = $database->loadObjectList();

		return $response;
	}
}
