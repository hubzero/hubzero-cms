<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for Wishlists
 */
class plgSearchWishlists extends \Hubzero\Plugin\Plugin
{
	/**
	 * Basic search
	 *
	 * @param   object  $request
	 * @param   object  &$results
	 * @return  void
	 */
	public static function onSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(wli.subject, wli.about) against(\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(wli.subject LIKE '%$mand%' OR wli.about LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(wli.subject NOT LIKE '%$forb%' AND wli.about NOT LIKE '%$forb%')";
		}

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				wli.subject AS title,
				wli.about AS description,
				concat('index.php?option=com_wishlist&category=', wl.category, '&rid=', wl.referenceid, '&task=wish&wishid=', wli.id) AS link,
				match(wli.subject, wli.about) against('collaboration') AS weight,
				wli.proposed AS date,
				concat(wl.title) AS section,
				CASE
				WHEN wli.anonymous THEN NULL
				ELSE (SELECT name FROM `#__users` ju WHERE ju.id = wli.proposed_by)
				END AS contributors,
				CASE
				WHEN wli.anonymous THEN NULL
				ELSE wli.proposed_by
				END AS contributor_ids
			FROM `#__wishlist_item` wli
			INNER JOIN `#__wishlist` wl
				ON wl.id = wli.wishlist AND wl.public = 1
			WHERE
				NOT wli.private AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" AND wli.status != 2 ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param   mixed   $type 
	 * @access  public
	 * @return  void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'wishlist';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param   string   $type
	 * @param   integer  $id 
	 * @param   boolean  $run 
	 * @access  public
	 * @return  void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'wishlist')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM `#__wishlist` WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				if (!is_object($row) || $row->id <= 0)
				{
					return;
				}

				// Get the name of the author
				$sql1 = "SELECT name FROM `#__users` WHERE id={$row->created_by};";
				$author = $db->setQuery($sql1)->query()->loadResult();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'blog';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				$path = '/wishlist/' . $row->category . '/' . $row->referenceid; // . '/wish/' . $row->id;

				// Public condition
				if ($row->public == 1)
				{
					$access_level = 'public';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				$owner_type = 'user';
				$owner = $row->created_by;

				if ($row->category && $row->referenceid > 0)
				{
					$owner_type = $row->category;
					$owner = $row->referenceid;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $row->description);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id           = $type . '-' . $id;
				$record->hubtype      = $type;
				$record->title        = $title;
				$record->description  = $description;
				$record->author       = array($author);
				$record->tags         = $tags;
				$record->path         = $path;
				$record->access_level = $access_level;
				$record->owner        = $owner;
				$record->owner_type   = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM `#__wishlist`;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}
