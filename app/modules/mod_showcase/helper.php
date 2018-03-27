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
use Components\Partners\Models\Partner;

include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';
include_once \Component::path('com_partners') . DS . 'models' . DS . 'partner.php';

/**
 * Mod_Showcase helper class, used to query for billboards and contains the display method
 */
class Helper extends Module
{
	protected $db = null;

	protected $groups = [];

	protected $pubs = [];

	protected $partners = [];

	protected $featured = [];

	/**
	 * Get the list of billboads in the selected collection
	 *
	 * @return retrieved rows
	 */
	private function _getBillboards($collection)
	{
		// Query to grab all the billboards associated with the selected collection
		// Make sure we only grab published billboards
		$query = 'SELECT b.*, c.name' .
				' FROM #__billboards_billboards as b, #__billboards_collections as c' .
				' WHERE c.id = b.collection_id' .
				' AND published = 1' .
				' AND c.name = ' . $this->db->quote($collection) .
				' ORDER BY `ordering` ASC';

		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();

		return $rows;
	}

	/**
	 * Get the most recent publications.
	 * @return array Publications, ordered by most recent.
	 */
	private function _getPublications($item)
	{
		if (empty($this->pubs)) {
			//query to get publications
			$sql = 'SELECT V.*, C.id as id, C.category, C.project_id, C.access as master_access, C.checked_out, C.checked_out_time, C.rating as master_rating, C.group_owner, C.master_type, C.master_doi, C.ranking as master_ranking, C.times_rated as master_times_rated, C.alias, V.id as version_id, t.name AS cat_name, t.alias as cat_alias, t.url_alias as cat_url, PP.alias as project_alias, PP.title as project_title, PP.state as project_status, PP.private as project_private, PP.provisioned as project_provisioned, MT.alias as base, MT.params as type_params, (SELECT vv.version_label FROM #__publication_versions as vv WHERE vv.publication_id=C.id AND vv.state=3 ORDER BY ID DESC LIMIT 1) AS dev_version_label , (SELECT COUNT(*) FROM #__publication_versions WHERE publication_id=C.id AND state!=3) AS versions FROM #__publication_versions as V, #__projects as PP, #__publication_master_types AS MT, #__publications AS C LEFT JOIN #__publication_categories AS t ON t.id=C.category WHERE V.publication_id=C.id AND MT.id=C.master_type AND PP.id = C.project_id AND V.id = (SELECT MAX(wv2.id) FROM #__publication_versions AS wv2 WHERE wv2.publication_id = C.id AND state!=3)';

			$this->db->setQuery($sql . ' AND V.state != 2 GROUP BY C.id ORDER BY V.published_up DESC');
			if (!$this->db->getError())
			{
				$this->pubs = $this->db->loadObjectList('id');
			}

			// Get featured publications
			$this->db->setQuery($sql . ' AND C.featured = 1 AND V.state != 2 GROUP BY C.id ORDER BY V.published_up DESC');
			if (!$this->db->getError())
			{
				$this->featured["pubs"] = $this->db->loadObjectList('id');
			}
		}

		// This code really needs to be turned into a function
		// Make sure we don't ask for too much
		$n = min($item["n"], ($item["featured"] ? count($this->featured["pubs"]) : count($this->pubs)));
		if ($n < $item["n"]) {
			echo 'Showcase Module Error: Not enough requested publications left!';
			return [];
		}

		if ($item["ordering"] === "recent") {
			if ($item["featured"]) {
				$item_pubs = array_slice($this->featured["pubs"], 0, $n, $preserve = true);
			} else {
				$item_pubs = array_slice($this->pubs, 0, $n, $preserve = true);
			}
		} elseif ($item["ordering"] === "random") {
			if ($item["featured"]) {
				$rind = array_flip((array)array_rand($this->featured["pubs"], $n));
				$item_pubs = $this->shuffle_assoc(array_intersect_key($this->featured["pubs"], $rind));
			} else {
				$rind = array_flip((array)array_rand($this->pubs, $n));
				$item_pubs = $this->shuffle_assoc(array_intersect_key($this->pubs, $rind));
			}
		} elseif ($item["ordering"] === "indexed") {
			// Just use array_intersect_keys silly!
			$item_pubs = array_filter($this->pubs, function($pub) use ($item) {
				return in_array($pub->id, $item["indices"]);
			});
		} else {
			echo 'Showcase Module Error: Unknown ordering "' . $item["ordering"] . '".  Possible values include "recent", "random", or "indexed".';
			return [];
		}
		// Remove used pubs from master lists
		$this->pubs = array_diff_key($this->pubs, $item_pubs);
		$this->featured["pubs"] = array_diff_key($this->featured["pubs"], $item_pubs);

		return $item_pubs;
	}

	/**
	 * Get groups.
	 *
	 * We are mirroring code at Hubzero\User\Group\Helper::getFeaturedGroups()
	 * @return true
	 */
	private function _getGroups($item)
	{
		if (empty($this->groups)) {
			//query to get groups
			$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc, g.created
					FROM `#__xgroups` AS g
					WHERE (g.type=1
					OR g.type=3)
					AND g.published=1
					AND g.approved=1
					AND g.discoverability=0";

			$this->db->setQuery($sql . " ORDER BY `created` DESC;");
			if (!$this->db->getError())
			{
				$this->groups = $this->db->loadObjectList('gidNumber');
			}

			// Get the featured group list (whether we need it or not)
			$featuredGroupList = \Component::params('com_groups')->get('intro_featuredgroups_list', '');
			$featuredGroupList = array_map('trim', array_filter(explode(',', $featuredGroupList), 'trim'));
			$sql_feat = $sql . "	AND g.cn IN ('" . implode("','", $featuredGroupList) . "')";

			$this->db->setQuery($sql_feat . "ORDER BY `created` DESC;");
			if (!$this->db->getError())
			{
				$this->featured["groups"] = $this->db->loadObjectList('gidNumber');
			}
		}

		// This code really needs to be turned into a function
		// Make sure we don't ask for too much
		$n = min($item["n"], ($item["featured"] ? count($this->featured["groups"]) : count($this->groups)));
		if ($n < $item["n"]) {
			echo 'Showcase Module Error: Not enough selected groups left!';
			return [];
		}

		if ($item["ordering"] === "recent") {
			if ($item["featured"]) {
				$item_groups = array_slice($this->featured["groups"], 0, $n, $preserve = true);
			} else {
				$item_groups = array_slice($this->groups, 0, $n, $preserve = true);
			}
		} elseif ($item["ordering"] === "random") {
			if ($item["featured"]) {
				$rind = array_flip((array)array_rand($this->featured["groups"], $n));
				$item_groups = $this->shuffle_assoc(array_intersect_key($this->featured["groups"], $rind));
			} else {
				$rind = array_flip((array)array_rand($this->groups, $n));
				$item_groups = $this->shuffle_assoc(array_intersect_key($this->groups, $rind));
			}
		} elseif ($item["ordering"] === "indexed") {
			// Just use array_intersect_keys silly!
			$item_groups = array_filter($this->groups, function($group) use ($item) {
				return in_array($group->gidNumber, $item["indices"]);
			});
		} else {
			echo 'Showcase Module Error: Unknown ordering "' . $item["ordering"] . '".  Possible values include "recent", "random", or "indexed".';
			return [];
		}
		// Remove used groups from master lists
		$this->groups = array_diff_key($this->groups, $item_groups);
		$this->featured["groups"] = array_diff_key($this->featured["groups"], $item_groups);

		return $item_groups;
	}

	/**
	 * Get partners.
	 * @return array Partners
	 */
	private function _getPartners($item)
	{
		if (empty($this->partners))
		{
			$sql = "SELECT p.*
					FROM `#__partner_partners` AS p
					WHERE p.state=1";

			$this->db->setQuery($sql . " ORDER BY `date_joined` DESC;");
			if (!$this->db->getError())
			{
				$this->partners = $this->db->loadObjectList('id');
			}

			// Get featured partners
			$this->db->setQuery($sql . ' AND p.featured=1 ORDER BY `date_joined` DESC;');
			if (!$this->db->getError())
			{
				$this->featured["partners"] = $this->db->loadObjectList('id');
			}
		}

		// This code really needs to be turned into a function
		// Make sure we don't ask for too much
		$n = min($item["n"], ($item["featured"] ? count($this->featured["partners"]) : count($this->partners)));
		if ($n < $item["n"]) {
			echo 'Showcase Module Error: Not enough selected partners left!';
			return [];
		}

		if ($item["ordering"] === "recent") {
			if ($item["featured"]) {
				$item_partners = array_slice($this->featured["partners"], 0, $n, $preserve = true);
			} else {
				$item_partners = array_slice($this->partners, 0, $n, $preserve = true);
			}
		} elseif ($item["ordering"] === "random") {
			if ($item["featured"]) {
				$rind = array_flip((array)array_rand($this->featured["partners"], $n));
				$item_partners = $this->shuffle_assoc(array_intersect_key($this->featured["partners"], $rind));
			} else {
				$rind = array_flip((array)array_rand($this->partners, $n));
				$item_partners = $this->shuffle_assoc(array_intersect_key($this->partners, $rind));
			}
		} elseif ($item["ordering"] === "indexed") {
			// Just use array_intersect_keys silly!
			$item_partners = array_filter($this->partners, function($partner) use ($item) {
				return in_array($partner->id, $item["indices"]);
			});
		} else {
			echo 'Showcase Module Error: Unknown ordering "' . $item["ordering"] . '".  Possible values include "recent", "random", or "indexed".';
			return [];
		}
		// Remove used partners from master lists
		$this->partners = array_diff_key($this->partners, $item_partners);
		$this->featured["partners"] = array_diff_key($this->featured["partners"], $item_partners);

		return $item_partners;
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
    			  "content" => ($item[2] === 'static' ? $item[4] : strtolower($item[4])),
    			  "featured" => (($item[2] === 'dynamic' and count($item) > 5) ? $item[5] : 0),
    			  "indices" => (($item[3] === 'indexed' and count($item) > 5) ? explode(';', $item[5]) : 0),
    			  "tag" => 0, // Set this below
    			  "tag-target" => 0 // Set this below
    			);

    			// Add autotags to dynamic content
    			if (($this->autotag) and ($items[$i]["type"] === 'dynamic')) {
    				if ($items[$i]["content"] === 'publications') {
    					$items[$i]["tag"] = "Resource";
    				} else {
    					$items[$i]["tag"] = ucfirst(rtrim($items[$i]["content"], 's'));
    				}
    				switch ($items[$i]["content"])
    				{
    					case 'publications':
    						$items[$i]["tag-target"] = Route::url('index.php?option=com_publications');
    					break;

    					case 'groups':
    						$items[$i]["tag-target"] = Route::url('index.php?option=com_groups');
    					break;

    					case 'partners':
    						$items[$i]["tag-target"] = Route::url('index.php?option=com_partners');
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
	 * Associative array shuffle
	 * @param  array $list Unshuffled associative array
	 * @return array       Shuffled associative array
	 */
	private function shuffle_assoc($list) {
		if (!is_array($list)) return $list;

  		$keys = array_keys($list);
  		shuffle($keys);
  		$random = array();
  		foreach ($keys as $key) {
    		$random[$key] = $list[$key];
  		}

  		return $random;
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

		$this->db = \App::get('db');

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
