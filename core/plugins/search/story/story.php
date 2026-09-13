<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Sanitize;

/**
 * Puts stories in site search
 *
 * A story is indexed as one document: its headline, its kicker and its prose.
 * The discussion under it is deliberately not folded in — a comment thread is
 * long, it is written by other people, and a story that matched only because
 * somebody argued in its comments is a result nobody wanted.
 */
class plgSearchStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The type this plugin indexes
	 *
	 * @var  string
	 */
	const TYPE = 'story';

	/**
	 * Announce the type this plugin speaks for
	 *
	 * @param   string  $type
	 * @return  mixed
	 */
	public function onGetTypes($type = null)
	{
		if (!isset($type) || $type == self::TYPE)
		{
			return self::TYPE;
		}
	}

	/**
	 * Hand back one story as a search record, or the list of what to index
	 *
	 * @param   string   $type
	 * @param   integer  $id
	 * @param   boolean  $run
	 * @return  mixed
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type != self::TYPE)
		{
			return;
		}

		$db = App::get('db');

		if ($run !== true)
		{
			// Only published stories. A draft is not a secret, but it is not
			// finished either, and a search result is a promise that something
			// is there to read.
			return $db->setQuery("SELECT `id` FROM `#__story_stories` WHERE `state` = 2;")->query()->loadColumn();
		}

		$id = (int) $id;

		$db->setQuery("SELECT s.`id`, s.`title`, s.`kicker`, s.`alias`, s.`created`, s.`created_by`,
				s.`publish_up`, s.`state`, s.`access`, s.`scope`, s.`scope_id`,
				t.`intro`, t.`body`, t.`related`
			FROM `#__story_stories` AS s
			LEFT JOIN `#__story_texts` AS t ON t.`story_id` = s.`id`
			WHERE s.`id` = " . $id . " LIMIT 1;");

		$row = $db->query()->loadObject();

		if (!$row || $row->state != 2)
		{
			return;
		}

		$record = new \stdClass;
		$record->id          = $type . '-' . $id;
		$record->hubtype     = $type;
		$record->title       = array($row->title);
		$record->description = $this->prose($row);
		$record->author      = array($this->authorName($db, $row->created_by));
		$record->tags        = $this->tagsFor($db, $id);
		$record->path        = $this->pathFor($row);
		$record->access_level = ($row->access == 1) ? 'public' : (($row->access == 2) ? 'registered' : 'private');
		$record->owner       = (int) $row->created_by;
		$record->owner_type  = 'user';

		return $record;
	}

	/**
	 * The searchable prose of a story
	 *
	 * The kicker goes in. It is a real part of how these are written and
	 * often the only place a word like "retraction" or "outage" appears.
	 *
	 * @param   object  $row
	 * @return  string
	 */
	protected function prose($row)
	{
		$text = implode(' ', array_filter(array(
			$row->kicker,
			$row->intro,
			$row->body,
			$row->related
		)));

		$text = preg_replace('/<[^>]*>/', ' ', $text);
		$text = preg_replace('/ {2,}/', ' ', $text);

		return trim(Sanitize::stripAll($text));
	}

	/**
	 * Who wrote it
	 *
	 * @param   object   $db
	 * @param   integer  $userId
	 * @return  string
	 */
	protected function authorName($db, $userId)
	{
		if (!$userId)
		{
			return '';
		}

		$db->setQuery("SELECT `name` FROM `#__users` WHERE `id` = " . (int) $userId . " LIMIT 1;");

		return (string) $db->loadResult();
	}

	/**
	 * Its tags
	 *
	 * @param   object   $db
	 * @param   integer  $id
	 * @return  array
	 */
	protected function tagsFor($db, $id)
	{
		$db->setQuery("SELECT t.`tag`
			FROM `#__tags` AS t
			INNER JOIN `#__tags_object` AS o ON o.`tagid` = t.`id`
			WHERE o.`objectid` = " . (int) $id . " AND o.`tbl` = 'story';");

		$tags = $db->query()->loadColumn();

		return $tags ? array_values(array_unique($tags)) : array();
	}

	/**
	 * Where it lives
	 *
	 * The dated address, built the same way the router builds it, so a search
	 * result and a link from the front page go to the same place.
	 *
	 * @param   object  $row
	 * @return  string
	 */
	protected function pathFor($row)
	{
		$when = $row->publish_up ?: $row->created;

		if (!$when || $when == '0000-00-00 00:00:00')
		{
			return '/story';
		}

		$time = strtotime($when);

		return '/story/' . date('Y/m/d', $time) . '/' . $row->alias;
	}
}
