<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for questions and answers
 */
class plgTagsAnswers extends \Hubzero\Plugin\Plugin
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
			'title'   => Lang::txt('PLG_TAGS_ANSWERS'),
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

		$f_fields = "SELECT a.id, a.subject AS title, NULL AS alias, NULL AS itext, a.question AS ftext, a.state, a.created, a.created_by, 
					NULL AS modified, a.created AS publish_up, NULL AS publish_down, CONCAT('index.php?option=com_answers&task=question&id=', a.id) AS href, 
					'answers' AS section, COUNT(DISTINCT t.tagid) AS uniques, a.anonymous AS params, 
					(SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.question_id=a.id) AS rcount, 
					NULL AS data1, NULL AS data2, NULL AS data3 ";

		$f_from  = " FROM #__answers_questions AS a, #__tags_object AS t WHERE a.id=t.objectid AND t.tbl='answers' AND t.tagid IN ($ids) AND a.state!=2";
		$f_from .= " GROUP BY a.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, created';    break;
			case 'id':    $order_by .= "id DESC";               break;
			case 'date':
			default:      $order_by .= 'created DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		$database->setQuery($f_count . $f_from . ") AS f");
		$response['total'] = $database->loadResult();

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
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$row->href = Route::url('index.php?option=com_answers&task=question&id=' . $row->id);

		$html  = "\t" . '<li class="answer">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . strip_tags(stripslashes($row->title)) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">';
		if ($row->state == 1)
		{
			$html .= Lang::txt('PLG_TAGS_ANSWERS_OPEN');
		}
		else
		{
			$html .= Lang::txt('PLG_TAGS_ANSWERS_CLOSED');
		}
		$html .= ' <span>|</span> ' . Lang::txt('PLG_TAGS_ANSWERS_RESPONSES') . ' ' . $row->rcount . '</p>' . "\n";
		if ($row->ftext)
		{
			$html .= "\t\t" . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::clean(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . ltrim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		return $html;
	}
}
