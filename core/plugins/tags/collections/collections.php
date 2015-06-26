<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Find collections data
 */
class plgTagsCollections extends \Hubzero\Plugin\Plugin
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
			'title'   => Lang::txt('PLG_TAGS_COLLECTIONS'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		$database = \App::get('db');

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT p.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT p.id, i.title, c.alias, NULL AS itext,
					CASE WHEN (p.description!='' AND p.description IS NOT NULL) THEN p.description ELSE i.description END AS ftext,
					i.state,
					p.created,
					p.created_by,
					NULL AS modified,
					p.created AS publish_up,
					NULL AS publish_down,
					concat('index.php?option=com_collections&controller=posts&post=', p.id) AS href,
					'collections' AS section,
					COUNT(DISTINCT t.tagid) AS uniques,
					c.object_type AS params,
					c.object_id AS rcount,
					i.type AS data1,
					i.object_id AS data2,
					NULL AS data3 ";
		$e_from  = " FROM #__collections_posts AS p
			INNER JOIN #__collections AS c ON c.id=p.collection_id
			INNER JOIN #__collections_items AS i ON p.item_id=i.id
			INNER JOIN #__tags_object AS t";
		$e_where = " WHERE i.state=1 AND c.state=1 AND t.objectid=p.item_id AND t.tbl='bulletinboard' AND t.tagid IN ($ids)";

		if (User::isGuest())
		{
			$e_where .= " AND i.access=0 AND c.access=0";
		}
		else
		{
			$e_where .= " AND i.access IN (0, 1) AND c.access IN (0, 1)";
		}
		$e_where .= " GROUP BY p.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'i.title ASC';  break;
			case 'id':    $order_by .= "p.id DESC"; break;
			case 'date':
			default:      $order_by .= 'p.created'; break;
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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'post.php');

		$row->object_type     = $row->params;
		$row->object_id     = $row->rcount;
		$row->i_title     = $row->title;
		$row->i_type      = $row->data1;
		$row->i_object_id = $row->data2;
		$row->description = $row->ftext;

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'tags',
			'element' => 'collections',
			'name'    => 'result'
		));
		$view->entry = new \Components\Collections\Models\Post($row);

		return $view->loadTemplate();
	}
}

