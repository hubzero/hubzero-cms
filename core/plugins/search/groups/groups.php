<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Groups\Models\Orm\Group;

require_once Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'group.php';

/**
 * Search groups
 */
class plgSearchGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(g.cn, g.description, g.public_desc) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		$from = '';

		if (!User::isGuest() && !User::authorise('core.view', 'com_groups'))
		{
			$from = " JOIN `#__xgroups_members` AS m ON m.gidNumber=g.gidNumber AND m.uidNumber=" . User::get('id');
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(g.cn LIKE '%$mand%' OR g.description LIKE '%$mand%' OR g.public_desc LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(g.cn NOT LIKE '%$forb%' AND g.description NOT LIKE '%$forb%' AND g.public_desc NOT LIKE '%$forb%')";
		}

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				g.description AS title,
				coalesce(g.public_desc, '') AS description,
				concat('index.php?option=com_groups&cn=', g.cn) AS link,
				$weight AS weight,
				NULL AS date,
				'Groups' AS section
			FROM `#__xgroups` g $from
			WHERE
				(g.type = 1 OR g.type = 3) AND g.published=1 AND g.approved=1 AND g.discoverability = 0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'group';

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
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'group')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__xgroups WHERE gidNumber={$id} AND type = 1 OR type = 3;";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'groups';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Public condition
				if ($row->discoverability == 0)
				{
					$access_level = 'public';
				}
				else
				{
					$access_level = 'private';
				}

				// Members 'own' the group
				$group = \Hubzero\User\Group::getInstance($id);
				if (!is_object($group) || empty($group))
				{
					return;
				}

				$members = $group->get('members');
				$owner_type = 'user';
				$owner = $members;

				$path = '/groups/' . $row->cn;

				// Get the title
				$title = $row->description;

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $row->public_desc);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
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
				$sql = "SELECT gidNumber FROM #__xgroups WHERE type=1 OR type=3;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
}
}
