<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for events
 */
class plgTagsEvents extends \Hubzero\Plugin\Plugin
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
			'title'   => Lang::txt('PLG_TAGS_EVENTS'),
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
		$e_fields = "SELECT e.id, e.title, NULL AS alias, NULL AS itext, e.content AS ftext, e.state, e.created, e.created_by,
						NULL AS modified, e.publish_up, e.publish_down, CONCAT('index.php?option=com_events&task=details&id=', e.id) AS href,
						'events' AS section, COUNT(DISTINCT t.tagid) AS uniques, e.params, NULL AS rcount, NULL AS data1,
						NULL AS data2, NULL AS data3 ";
		$e_from  = " FROM #__events AS e, #__tags_object AS t";
		$e_where = " WHERE e.state=1 AND t.objectid=e.id AND t.tbl='events' AND t.tagid IN ($ids)";

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
			\Hubzero\Document\Assets::addComponentStylesheet('com_events');

			$response['sql'] = $e_fields . $e_from . $e_where;
		}

		return $response;
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return   void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_events');
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		$row->href = Route::url($row->href);

		$month = Date::of($row->publish_up)->toLocal('M');
		$day   = Date::of($row->publish_up)->toLocal('d');
		$year  = Date::of($row->publish_up)->toLocal('Y');

		// Start building the HTML
		$html  = "\t" . '<li class="event">'."\n";
		$html .= "\t\t" . '<p class="event-date"><span class="month">' . $month . '</span> <span class="day">' . $day . '</span> <span class="year">' . $year . '</span></p>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title).'</a></p>' . "\n";
		if ($row->ftext)
		{
			$row->ftext = str_replace('[[BR]]', '', $row->ftext);

			// Remove tags to prevent tables from being displayed within a table.
			$row->ftext = strip_tags($row->ftext);
			$html .= "\t\t" . \Hubzero\Utility\Str::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}
