<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for groups
 */
class plgTagsGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieve records for items tagged with specific tags
	 *
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_GROUPS'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		$database = App::get('db');

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$from = '';

		if (!User::authorise('core.view', 'com_groups'))
		{
			$from = " JOIN #__xgroups_members AS m ON m.gidNumber=a.gidNumber AND m.uidNumber=" . (int)User::get('id', 0);
		}

		// Build the query
		$f_count = "SELECT COUNT(f.gidNumber) FROM (SELECT a.gidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.gidNumber AS id, a.description AS title, a.cn AS alias, NULL AS itext, a.public_desc AS ftext, a.type AS state, a.created,
					a.created_by, NULL AS modified, NULL AS publish_up,
					NULL AS publish_down, CONCAT('index.php?option=com_groups&cn=', a.cn) AS href, 'groups' AS section, COUNT(DISTINCT t.tagid) AS uniques,
					a.params, NULL AS rcount, NULL AS data1, NULL AS data2, NULL AS data3 ";
		$f_from = " FROM #__xgroups AS a $from
					JOIN #__tags_object AS t
					WHERE (a.type=1 OR a.type=3) AND a.discoverability=0
					AND a.gidNumber=t.objectid
					AND t.tbl='groups'
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.gidNumber HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title':
				$order_by .= 'title ASC, publish_up';
				break;
			case 'id':
				$order_by .= "id DESC";
				break;
			case 'date':
			default:
				$order_by .= 'publish_up DESC, title';
				break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		$database->setQuery($f_count . $f_from . ") AS f");
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($f_fields . $f_from .  $order_by);
			$response['results'] = $database->loadObjectList();
			if ($response['results'])
			{
				// Loop through the results and set each item's HREF
				foreach ($response['results'] as $key => $row)
				{
					$response['results'][$key]->href = Route::url('index.php?option=com_groups&cn=' . $row->alias);
				}
			}
		}
		else
		{
			$response['sql'] = $f_fields . $f_from;
		}

		return $response;
	}
}
