<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for courses
 */
class plgTagsCourses extends \Hubzero\Plugin\Plugin
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
			'title'   => Lang::txt('PLG_TAGS_COURSES'),
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
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.alias, e.blurb AS itext, e.description AS ftext, e.state, e.created, e.created_by,
					NULL AS modified, NULL AS publish_up, NULL AS publish_down, CONCAT('index.php?option=com_courses&gid=', e.alias) AS href,
					'courses' AS section, COUNT(DISTINCT t.tagid) AS uniques, e.params, 0 AS rcount, u.name AS data1,
					NULL AS data2, NULL AS data3 ";
		$e_from  = " FROM `#__courses` AS e, `#__tags_object` AS t, `#__users` AS u";
		$e_where  = " WHERE e.created_by=u.id AND e.state=1 AND t.objectid=e.id AND t.tbl='courses' AND t.tagid IN ($ids)";
		$e_where .= " AND e.access IN (" . implode(',', User::getAuthorisedViewLevels()) . ")";
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);

		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title':
				$order_by .= 'title ASC';
				break;
			case 'id':
				$order_by .= "id DESC";
				break;
			case 'date':
			default:
				$order_by .= 'created DESC, title ASC';
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
		include_once Component::path('com_courses') . DS . 'models' . DS . 'course.php';

		$course = new \Components\Courses\Models\Course($row);
		$course->set('blurb', $row->itext);
		$course->set('description', $row->ftext);

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'tags',
			'element' => 'courses',
			'name'    => 'result'
		));
		$view->set('course', $course);

		return $view->loadTemplate();
	}
}
