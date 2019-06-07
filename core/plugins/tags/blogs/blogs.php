<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for blog articles
 */
class plgTagsBlogs extends \Hubzero\Plugin\Plugin
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
			'title'   => Lang::txt('PLG_TAGS_BLOGS'),
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

		$now = Date::toSql();

		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.alias, NULL AS itext, e.content AS ftext, e.state, e.created, e.created_by,
					NULL AS modified, e.publish_up, e.publish_down, CONCAT('index.php?option=com_blog&task=view&id=', e.id) AS href,
					'blogs' AS section, COUNT(DISTINCT t.tagid) AS uniques, e.params, e.scope AS rcount, u.name AS data1,
					e.scope_id AS data2, NULL AS data3 ";
		$e_from  = " FROM #__blog_entries AS e, #__tags_object AS t, #__users AS u";
		$e_where = " WHERE e.created_by=u.id AND t.objectid=e.id AND t.tbl='blog' AND t.tagid IN ($ids)";

		if (User::isGuest())
		{
			$e_where .= " AND e.state=1";
		}
		else
		{
			$e_where .= " AND e.state>0";
		}
		$e_where .= " AND (e.publish_up IS NULL OR e.publish_up <= " . $database->quote($now) . ") ";
		$e_where .= " AND (e.publish_down IS NULL OR e.publish_down >= " . $database->quote($now) . ") ";
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);
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

		$database->setQuery($e_count . $e_from . $e_where . ") AS f");
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($e_fields . $e_from . $e_where . $order_by);
			$response['results'] = $database->loadObjectList();
		}
		else
		{
			$response['sql'] = $e_fields . $e_from . $e_where;
		}

		return $response;
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		include_once \Component::path('com_blog') . DS . 'models' . DS . 'entry.php';

		$row->scope    = $row->rcount;
		$row->scope_id = $row->data2;
		$row->content  = $row->ftext;

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'tags',
			'element' => 'blogs',
			'name'    => 'result'
		));
		$view->entry = \Components\Blog\Models\Entry::blank();
		$view->entry->set(get_object_vars($row));

		return $view->loadTemplate();
	}
}
