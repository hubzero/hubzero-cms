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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Courses\Models\Orm\Course;

require_once Component::path('com_courses') . DS . 'models' . DS . 'orm' . DS . 'course.php';

/**
 * Search course entries
 */
class plgSearchCourses extends \Hubzero\Plugin\Plugin
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
		$authorization = 'state = 1';

		$terms = $request->get_term_ar();
		$weight = '(match(c.alias, c.title, c.blurb) against (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(c.title LIKE '%$mand%' OR c.blurb LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(c.title NOT LIKE '%$forb%' AND c.blurb NOT LIKE '%$forb%')";
		}

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				c.id,
				c.title,
				c.blurb AS description,
				concat('index.php?option=com_courses&gid=', c.alias) AS link,
				$weight AS weight,
				'Course' AS section,
				c.created AS date
			FROM #__courses c
			INNER JOIN #__users u ON u.id = c.created_by
			WHERE
				$authorization AND
				$weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);
		if (($rows = $rows->to_associative()) instanceof \Components\Search\Models\Basic\Result\Blank)
		{
			return;
		}

		$results->add($rows);
	}

	public $hubtype = 'course';

	/**
	 * onGetTypes - Announces the available hubtype
	 *
	 * @param mixed $type
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		if (isset($type) && $type == $this->hubtype)
		{
			return $this->hubtype;
		}
		elseif (!isset($type))
		{
			return $this->hubtype;
		}
	}

	/**
	 * onGetModel 
	 * 
	 * @param string $type 
	 * @access public
	 * @return void
	 */
	public function onGetModel($type = '')
	{
		if ($type == $this->hubtype)
		{
			return new Course;
		}
	}

	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row, &$db)
	{
		if ($type == $this->hubtype)
		{
			// Instantiate new $fields object
			$fields = new stdClass;

			// Format the date for SOLR
			$date = Date::of($row->created)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->created)->format('h:m:s') . 'Z';
			$fields->date = $date;

			// Title is required
			$fields->title = $row->title;

			$fields->description = strip_tags(htmlspecialchars_decode($row->get('description')));

			/**
			 * Each entity should have an owner. 
			 * Owner type can be a user or a group,
			 * where the owner is the ID of the user or group
			 **/
			$owners = array();

			// Original course creator
			array_push($owners, $row->created_by);

			$offerings = $row->offerings()->rows();
			foreach ($offerings as $offering)
			{
				// Offering creators
				array_push($owners, $offering->created_by);
			}

			$fields->owner_type = 'user';
			$fields->owner = $owners;

			/**
			 * A document should have an access level.
			 * This value can be:
			 *  public - all users can view
			 *  registered - only registered users can view
			 *  private - only owners (set above) can view
			 **/
			if ($row->state == 1)
			{
				$fields->access_level = 'public';
			}
			else
			{
				$fields->access_level = 'private';
			}

			// The URL this document is accessible through
			$fields->url = '/courses/' . $row->alias;

			return $fields;
		}
	}
}

