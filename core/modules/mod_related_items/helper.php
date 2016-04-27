<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RelatedItems;

use Hubzero\Module\Module;
use stdClass;
use ContentHelperRoute;
use Request;
use Route;
use User;
use Lang;

/**
 * Module class for displaying related articles
 */
class Helper extends Module
{
	/**
	 * Display module content
	 * 
	 * @return  void
	 */
	public function display()
	{
		require_once PATH_CORE . '/components/com_content/site/helpers/route.php';

		// [!] Legacy compatibility
		$params = $this->params;

		$cacheparams = new stdClass;
		$cacheparams->cachemode    = 'safeuri';
		$cacheparams->class        = '\Modules\RelatedItems\Helper';
		$cacheparams->method       = 'getList';
		$cacheparams->methodparams = $params;
		$cacheparams->modeparams   = array(
			'id'     => 'int',
			'Itemid' => 'int'
		);

		$list = \Module::cache($module, $params, $cacheparams);

		if (!count($list))
		{
			return;
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
		$showDate = $params->get('showDate', 0);

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of articles
	 * 
	 * @param   object  $params  Registry
	 * @return  array
	 */
	public static function getList($params)
	{
		$db     = \App::get('db');
		$userId = (int) User::get('id');
		$count  = intval($params->get('count', 5));
		$groups = implode(',', User::getAuthorisedViewLevels());
		$date   = \Date::toSql();

		$option = Request::getCmd('option');
		$view   = Request::getCmd('view');

		$temp   = Request::getString('id');
		$temp   = explode(':', $temp);
		$id     = $temp[0];

		$showDate = $params->get('showDate', 0);
		$nullDate = $db->getNullDate();
		$now      = $date->toSql();
		$related  = array();
		$query    = $db->getQuery(true);

		if ($option == 'com_content' && $view == 'article' && $id)
		{
			// Select the meta keywords from the item
			$query->select('metakey');
			$query->from('#__content');
			$query->where('id = ' . (int) $id);
			$db->setQuery($query);

			if ($metakey = trim($db->loadResult()))
			{
				// Explode the meta keys on a comma
				$keys  = explode(',', $metakey);
				$likes = array();

				// Assemble any non-blank word(s)
				foreach ($keys as $key)
				{
					$key = trim($key);
					if ($key)
					{
						$likes[] = $db->escape($key);
					}
				}

				if (count($likes))
				{
					// Select other items based on the metakey field 'like' the keys found
					$query->clear();
					$query->select('a.id');
					$query->select('a.title');
					$query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
					$query->select('a.catid');
					$query->select('a.language');
					$query->select('cc.access AS cat_access');
					$query->select('cc.published AS cat_state');

					// sqlsrv changes
					$case_when = ' CASE WHEN ';
					$case_when .= $query->charLength('a.alias');
					$case_when .= ' THEN ';
					$a_id = $query->castAsChar('a.id');
					$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
					$case_when .= ' ELSE ';
					$case_when .= $a_id . ' END as slug';
					$query->select($case_when);

					$case_when = ' CASE WHEN ';
					$case_when .= $query->charLength('cc.alias');
					$case_when .= ' THEN ';
					$c_id = $query->castAsChar('cc.id');
					$case_when .= $query->concatenate(array($c_id, 'cc.alias'), ':');
					$case_when .= ' ELSE ';
					$case_when .= $c_id . ' END as catslug';
					$query->select($case_when);
					$query->from('#__content AS a');
					$query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
					$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
					$query->where('a.id != ' . (int) $id);
					$query->where('a.state = 1');
					$query->where('a.access IN (' . $groups . ')');
					$concat_string = $query->concatenate(array('","', ' REPLACE(a.metakey, ", ", ",")', ' ","'));
					$query->where('(' . $concat_string . ' LIKE "%' . implode('%" OR ' . $concat_string . ' LIKE "%', $likes) . '%")'); //remove single space after commas in keywords)
					$query->where('(a.publish_up = ' . $db->Quote($nullDate).' OR a.publish_up <= ' . $db->Quote($now) . ')');
					$query->where('(a.publish_down = ' . $db->Quote($nullDate).' OR a.publish_down >= ' . $db->Quote($now) . ')');

					// Filter by language
					if (\App::get('language.filter'))
					{
						$query->where('a.language in (' . $db->Quote(Lang::getTag()) . ',' . $db->Quote('*') . ')');
					}

					$db->setQuery($query);
					$qstring = $db->getQuery();
					$temp = $db->loadObjectList();

					if (count($temp))
					{
						foreach ($temp as $row)
						{
							if ($row->cat_state == 1)
							{
								$row->route = Route::url(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->language));
								$related[] = $row;
							}
						}
					}
					unset ($temp);
				}
			}
		}

		return $related;
	}
}
