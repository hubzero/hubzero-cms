<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for members
 */
class plgTagsMembers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param   array    $tags        Tags to match records against
	 * @param   mixed    $limit       SQL record limit
	 * @param   integer  $limitstart  SQL record limit start
	 * @param   string   $sort        The field to sort records by
	 * @param   mixed    $areas       An array or string of areas that should retrieve records
	 * @return  mixed    Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_MEMBERS'),
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

		// Build the query
		$f_count = "SELECT COUNT(f.id) FROM (SELECT a.id, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.id, a.name AS title, a.username as alias, NULL AS itext, b.profile_value AS ftext, a.activation AS state, a.registerDate AS created,
					a.id AS created_by, NULL AS modified, a.registerDate AS publish_up, NULL AS publish_down,
					CONCAT('index.php?option=com_members&id=', a.id) AS href, 'members' AS section, COUNT(DISTINCT t.tagid) AS uniques, a.params, NULL AS rcount,
					NULL AS data1, NULL AS data2, NULL AS data3 ";

		$f_from = " FROM #__users AS a
					LEFT JOIN #__user_profiles AS b ON a.id=b.user_id AND b.profile_key='bio', #__tags_object AS t
					WHERE a.access IN (" . implode(',', User::getAuthorisedViewLevels()) . ")
					AND a.block=0
					AND a.activation > 0
					AND a.id=t.objectid
					AND t.tbl='xprofiles'
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.id, b.profile_value HAVING uniques=".count($tags);
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

		if ($response['total'])
		{
			\Hubzero\Document\Assets::addComponentStylesheet('com_members');
		}

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($f_fields . $f_from .  $order_by);
			$response['results'] = $database->loadObjectList();
		}
		else
		{
			$response['sql'] = $f_fields . $f_from;
		}

		return $response;
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return  void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_members');
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

		$member = \Components\Members\Models\Member::oneOrNew($row->id);

		$row->href = Route::url($member->link());

		$html  = "\t" . '<li class="member">' . "\n";
		$html .= "\t\t" . '<p class="photo"><img width="50" height="50" src="' . $member->picture() . '" alt="" /></p>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($row->ftext)
		{
			$html .= "\t\t" . \Hubzero\Utility\Str::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . ltrim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}
