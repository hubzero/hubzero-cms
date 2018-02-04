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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Showcase;

use Hubzero\Module\Module;
use Hubzero\User\Group;
use Components\Publications\Models\Publication;

/**
 * Mod_Showcase helper class, used to query for billboards and contains the display method
 */
class Helper extends Module
{
	/**
	 * Get the list of billboads in the selected collection
	 *
	 * @return retrieved rows
	 */
	private function _getBillboards($collection)
	{
		$db = \App::get('db');

		// Query to grab all the billboards associated with the selected collection
		// Make sure we only grab published billboards
		$query = 'SELECT b.*, c.name' .
				' FROM #__billboards_billboards as b, #__billboards_collections as c' .
				' WHERE c.id = b.collection_id' .
				' AND published = 1' .
				' AND c.name = ' . $db->quote($collection) .
				' ORDER BY `ordering` ASC';

/*		if ($indices)
		{
			$query .= ' AND b.id IN (' . str_replace(';', ',', $indices) . ')';
		}*/
		// $query .= ' ORDER BY `ordering` ASC';

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Get the most recent publications.
	 * @return array Publications, ordered by most recent.
	 */
	private function _getPublications($featured = 0)
	{
		include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';

		$pubmodel = new \Components\Publications\Models\Publication();
		$filters = array(
			'start'   => 0,
			'dev'     => 0,
			'sortby'  => 'date_created',
			'sortdir' => 'DESC'
		);

		if ($featured) {
			$filters['featured'] = 1;
		}

		$pubs = $pubmodel->entries('list', $filters);

		return $pubs;
	}

	/**
	 * Get groups.  
	 * 
	 * We are mirroring code at Hubzero\User\Group\Helper::getFeaturedGroups()
	 * @return array Groups.
	 */
	private function _getGroups($featured = 0)
	{
		//database object
		$db = \App::get('db');

		//query to get groups
		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc, g.created
				FROM `#__xgroups` AS g
				WHERE (g.type=1
				OR g.type=3)
				AND g.published=1
				AND g.approved=1
				AND g.discoverability=0";

		if ($featured) {
			//parse the featured group list
			$featuredGroupList = \Component::params('com_groups')->get('intro_featuredgroups_list', '');
			$featuredGroupList = array_map('trim', array_filter(explode(',', $featuredGroupList), 'trim'));
			$sql .= "	AND g.cn IN ('" . implode("','", $featuredGroupList) . "')";
		}

		$sql .= "   ORDER BY `created` DESC;";

		$db->setQuery($sql);
		if (!$db->getError())
		{
			return $db->loadObjectList();
		}
	}

	/**
	 * Parse the item specifications.
	 * @return [type] [description]
	 */
	private function _parseItems()
	{
		$str_items = $this->params->get('items');

		$separator = "\r\n";
		$str_item = true;
		$items = array();
		$i = 0;
		while ($str_item !== false) {
			if ($i == 0) {
				$str_item = strtok($str_items, $separator);
			} else {
    			$str_item = strtok($separator);
    		}

    		if ($str_item !== false) {
    			$item = explode(',', $str_item);
    			$items[] = array(
    			  "n" => (int) $item[0],
    			  "class" => $item[1],
    			  "type" => $item[2],
    			  "ordering" => $item[3],
    			  "content" => strtolower($item[4]),
    			  "featured" => (($item[2] === 'dynamic' and count($item) > 5) ? $item[5] : 0),
    			  "indices" => (($item[3] === 'indexed' and count($item) > 5) ? explode(';', $item[5]) : 0),
    			  "tag" => 0, // Set this below
    			  "tag-target" => 0 // Set this below
    			);

    			// Add autotags to dynamic content
    			if (($this->autotag) and ($items[$i]["type"] === 'dynamic')) {
    				$items[$i]["tag"] = ucfirst(rtrim($items[$i]["content"], 's'));
    				switch ($items[$i]["content"])
    				{
    					case 'publications':
    						$items[$i]["tag-target"] = Route::url('index.php?option=com_publications');
    					break;

    					case 'groups':
    						$items[$i]["tag-target"] = Route::url('index.php?option=com_groups');
    					break;

    					default:
    					break;
    				}
    			}

    			// Add optional tag
				$tag = 0;
				if (($items[$i]["indices"] or $items[$i]["featured"]) and count($item) > 6) {
					$tag = explode(';', $item[6]);
				} elseif ((!$items[$i]["indices"] and !$items[$i]["featured"]) and count($item) > 5) {
					$tag = explode(';', $item[5]);
				}
				if ($tag) {
					$items[$i]["tag"] = $tag[0];
					$items[$i]["tag-target"] = (count($tag) > 1 ? $tag[1] : 0);
				}
    		}
    		$i++;
		}

		return $items;
	}

	/**
	 * Display method
	 * Used to add CSS for each slide as well as the javascript file(s) and the parameterized function
	 *
	 * @return void
	 */
	public function display()
	{
		$this->css();

		$this->autotag = $this->params->get('autotag');
		$this->items = $this->_parseItems();

		// Get the billboard background location from the billboards parameters
		$params = \Component::params('com_billboards');
		$image_location = $params->get('image_location', '/app/site/media/images/billboards/');
		if ($image_location == '/site/media/images/billboards/')
		{
			$image_location = '/app' . $image_location;
		}
		$this->image_location = $image_location;

		require $this->getLayoutPath();
	}
}