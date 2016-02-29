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
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias, v.pagetext AS introtext,
					NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.rating, w.times_rated, w.ranking, 'Topic' AS section, w.`group_cn`
				FROM `#__wiki_page` AS w
				JOIN `#__wiki_version` AS v ON w.id=v.pageid
				JOIN `#__wiki_page_links` AS wl ON wl.page_id=w.id
				WHERE v.approved=1 AND wl.scope='resource' AND wl.scope_id=" . $database->Quote($resource->id);

		if (!User::isGuest())
		{
			if (User::authorize('com_resources', 'manage')
			 || User::authorize('com_groups', 'manage'))
			{
				$sql1 .= '';
			}
			else
			{
				$ugs = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');
				$groups = array();
				if ($ugs && count($ugs) > 0)
				{
					foreach ($ugs as $ug)
					{
						$groups[] = $database->quote($ug->cn);
					}
				}
				$g = implode(",", $groups);

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group_cn IN ($g) OR w.created_by=" . $database->quote(User::get('id')) . "))) ";
			}
		}
		else
		{
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, NULL AS pageid, NULL AS version, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up, "
			 . " NULL AS scope, r.rating, r.times_rated, r.ranking, rt.type AS section, NULL AS `group` "
			 . " FROM #__resource_types AS rt, #__resources AS r"
			 . " JOIN #__resource_assoc AS a ON r.id=a.parent_id"
			 . " LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . " WHERE r.published=1 AND a.child_id=" . $database->quote($resource->id) . " AND r.type=rt.id AND r.type!=8 ";
		if (!User::isGuest())
		{
			if (User::authorize('com_resources', 'manage')
			 || User::authorize('com_groups', 'manage'))
			{
				$sql2 .= '';
			}
			else
			{
				$sql2 .= "AND (r.access!=1 OR (r.access=1 AND (r.group_owner IN ($g) OR r.created_by=" . $database->quote(User::get('id')) . "))) ";
			}
		}
		else
		{
			$sql2 .= "AND r.access=0 ";
		}
		$sql2 .= "ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery($query);

		// Instantiate a view
		$view = $this->view(($miniview ? 'mini' : 'default'), 'browse')
			->set('option', $option)
			->set('resource', $resource)
			->set('related', $database->loadObjectList())
			->setErrors($this->getErrors());

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}
