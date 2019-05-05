<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Projects\Models\Orm\Project;
use Hubzero\User\Group;

require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'project.php';

/**
 * Search groups
 */
class plgSearchProjects extends \Hubzero\Plugin\Plugin
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
		$weight = 'match(p.alias, p.title, p.about) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		$from = '';

		if (!User::authorise('core.view', 'com_groups'))
		{
			$from = " JOIN #__xgroups_members AS m ON m.gidNumber=p.owned_by_group AND m.uidNumber=" . User::get('id', 0);
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(p.alias LIKE '%$mand%' OR p.title LIKE '%$mand%' OR p.about LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.alias NOT LIKE '%$forb%' AND p.title NOT LIKE '%$forb%' AND p.about NOT LIKE '%$forb%')";
		}

		$access = implode(',', User::getAuthorisedViewLevels());

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				p.title,
				p.about AS `description`,
				concat('index.php?option=com_projects&alias=', p.alias) AS `link`,
				$weight AS `weight`,
				NULL AS `date`,
				'Projects' AS `section`
			FROM `#__projects` AS p $from
			WHERE
				p.state!=2 AND (p.private=0 OR p.access IN ($access)) AND $weight > 0" .
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
		$hubtype = 'project';

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
	 * @return  void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'project')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM `#__projects` WHERE id={$id} AND type=1;";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Determine the path
				$path = '/projects/' . $row->alias;

				// Public condition
				if ($row->state == 1 && $row->private == 0)
				{
					$access_level = 'public';
				}
				else
				{
					$access_level = 'private';
				}

				if ($row->owned_by_group == 0)
				{
					$owner_type = 'user';
					$owner = $row->owned_by_user;
				}
				else
				{
					$owner_type = 'group';
					$owner = $row->owned_by_group;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $row->about);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
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
				$sql = "SELECT id FROM `#__projects` WHERE type=1;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}
