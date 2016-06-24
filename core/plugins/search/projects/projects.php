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
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
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
				p.state!=2 AND p.private=0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}
/************************************************
 *
 * HubSearch Required Methods
 * @author Kevin Wojkovich <kevinw@purdue.edu>
 *
 ***********************************************/

	/****************************
	Query-time / General Methods
	****************************/

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

	public function onGetModel($type = '')
	{
		if ($type == 'project')
		{
			return new Project;
		}
	}
	/*********************
		Index-time methods
	*********************/
	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row)
	{
		if ($type == 'project')
		{
			// Object for mapped fields
			$fields = new stdClass;

			// Public condition
			if ($row->state == 1 && $row->private == 0)
			{
				$fields->access_level = 'public';
			}
			// Default private
			else
			{
				$fields->access_level = 'private';
			}

			$group = Group::getInstance('pr-'.$row->alias);
			$fields->owner_type = 'group';
			$fields->owner = $group->get('gidNumber');

			// Build out path
			$path = '/projects/';
			$path .= $row->alias;

			$fields->url = $path;

			$fields->title = $row->title;
			$fields->alias = $row->alias;

			// Format the date for SOLR
			$date = Date::of($row->created)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->publish_up)->format('h:m:s') . 'Z';
			$fields->date = $date;

			$fields->description = $row->about;

			return $fields;
		}
	}
}

