<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for related resources
 */
class plgResourcesRelated extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $resource  Current resource
	 * @return  array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'related' => Lang::txt('PLG_RESOURCES_RELATED')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource  Current resource
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$database = App::get('db');

		// Build the query that checks topic pages
		$sql1 = "SELECT DISTINCT w.id, w.title, w.pagename AS alias, v.pagetext AS introtext,
						NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.scope_id,
						w.rating, w.times_rated, w.ranking, 'Topic' AS section
				FROM `#__wiki_pages` AS w
				INNER JOIN `#__wiki_versions` AS v ON w.version_id=v.id
				INNER JOIN `#__wiki_links` AS wl ON wl.page_id=w.id
				WHERE v.approved=1 AND wl.scope='resource' AND w.scope !='group' AND wl.scope_id=" . $database->quote($resource->id);

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up,
				NULL AS scope, NULL AS scope_id, r.rating, r.times_rated, r.ranking, rt.type AS section
				FROM `#__resource_types` AS rt, `#__resources` AS r
				JOIN `#__resource_assoc` AS a ON r.id=a.parent_id
				LEFT JOIN `#__resource_types` AS t ON r.logical_type=t.id
				WHERE r.published=1 AND a.child_id=" . $database->quote($resource->id) . " AND r.type=rt.id AND r.type!=8 ";

		$sql3 = "";

		if (!User::isGuest())
		{
			$groups = [];
			$cns = [];
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');

			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					$cns[] = $database->quote($ug->cn);
					$groups[] = $database->quote($ug->gidNumber);
				}
			}
			$g = implode(",", $groups);
			$c = implode(",", $cns);

			if (User::authorize('com_resources', 'manage')
			 || User::authorize('com_groups', 'manage'))
			{
				$sql1 .= '';
				$sql2 .= '';
			}
			else
			{
				$x = "";
				if (count($groups))
				{
					$x = "(w.scope=" . $database->quote('group') . " AND w.scope_id IN ($g)) OR";
				}

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND ($x w.created_by=" . $database->quote(User::get('id')) . "))) ";

				$x = "";
				if (count($groups))
				{
					$x = "r.group_owner IN ($c) OR";
				}

				$sql2 .= "AND (r.access!=1 OR (r.access=1 AND ($x r.created_by=" . $database->quote(User::get('id')) . "))) ";
			}

			if (!!$g)
			{
				$sql3 .= " UNION (SELECT DISTINCT w.id, w.title, w.pagename AS alias, v.pagetext AS introtext,
						NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.scope_id,	w.rating, w.times_rated, w.ranking, 'Topic' AS section
						FROM `#__wiki_pages` AS w
						INNER JOIN `#__wiki_versions` AS v ON w.version_id=v.id
						INNER JOIN `#__wiki_links` AS wl ON wl.page_id=w.id
						WHERE v.approved=1 AND wl.scope='resource' AND w.scope ='group' AND wl.scope_id=" . $database->quote($resource->id) .
						"AND w.scope_id in ($g))";
			}
		}
		else
		{
			$sql1 .= "AND w.access!=1 ";
			$sql2 .= "AND r.access=0 ";
		}
		$sql1 .= "GROUP BY w.id, v.pagetext ORDER BY ranking DESC, title LIMIT 10";
		$sql2 .= "ORDER BY r.ranking LIMIT 10";

		// Build the final query
		if (!User::isGuest())
		{
			$query = "SELECT k.* FROM (($sql1) UNION ($sql2)$sql3) AS k ORDER BY ranking DESC LIMIT 10";
		}
		else
		{
			$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";
		}

		// Execute the query
		$database->setQuery($query);

		$rows = $database->loadObjectList();

		// No data found. Nothing to display.
		if (!count($rows))
		{
			return;
		}

		// Instantiate a view
		$view = $this->view(($miniview ? 'mini' : 'default'), 'browse')
			->set('option', $option)
			->set('resource', $resource)
			->set('related', $rows)
			->setErrors($this->getErrors());

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}
