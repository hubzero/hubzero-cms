<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Search Collections
 */
class plgSearchCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param   object  $request   \Components\Search\Models\Basic\Request
	 * @param   object  &$results  \Components\Search\Models\Basic\Result\Set
	 * @param   object  $authz     \Components\Search\Models\Basic\Authorization
	 * @return  void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(p.description) AGAINST (\'' . join(' ', $terms['stemmed']) . '\') + match(i.title, i.description) AGAINST (\'' . join(' ', $terms['stemmed']) . '\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(p.description LIKE '%$mand%' OR i.title LIKE '%$mand%' OR i.description LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.description NOT LIKE '%$forb%' AND i.title NOT LIKE '%$forb%' AND i.description NOT LIKE '%$forb%')";
		}

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				i.title,
				CASE WHEN (p.description!='' AND p.description IS NOT NULL) THEN p.description ELSE i.description END AS description,
				concat('index.php?option=com_collections&controller=posts&post=', p.id) AS `link`,
				$weight AS `weight`,
				p.created AS `date`,
				'Collections' AS `section`,
				u.name AS contributors,
				p.created_by AS contributor_ids
			FROM `#__collections_posts` AS p
			INNER JOIN `#__collections` AS c ON c.id=p.collection_id
			INNER JOIN `#__collections_items` AS i ON p.item_id=i.id
			INNER JOIN `#__users` u ON u.id = p.created_by
			WHERE
				i.state=1 AND i.access=0 AND c.state=1 AND c.access=0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
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
		$hubtype = 'collection';

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
		if ($type == 'collection')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM `#__collections` WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT name FROM `#__users` WHERE id={$row->created_by};";
				$author = $db->setQuery($sql1)->query()->loadResult();

				// Get any tags
				// Unable to tag a collection
				/*
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'collection';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();
				*/
				$tags = array();

				if ($row->object_type == 'member')
				{
					$path = '/members/' . $row->object_id . '/collections/'. $row->alias;
				}
				elseif ($row->object_type == 'group')
				{
					$group = \Hubzero\User\Group::getInstance($row->object_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/collections/'. $row->alias;
					}
					else
					{
						$path = '';
					}
				}

				// Public condition
				if ($row->state == 1 && $row->access == 0)
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($row->state == 1 && $row->access == 1)
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				if ($row->object_type != 'group')
				{
					$owner_type = 'user';
					$owner = $row->created_by;
				}
				else
				{
					$owner_type = 'group';
					$owner = $row->object_id;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = $row->description;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->author = array($author);
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM `#__collections`;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}
