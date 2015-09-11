<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Search\Models\Basic;

/**
 * Abstract search result
 */
abstract class Result
{
	/**
	 * Description for 'intro_excerpt_len'
	 *
	 * @var integer
	 */
	private static $intro_excerpt_len = 350;

	/**
	 * Description for 'types'
	 *
	 * @var array
	 */
	private static $types = array();

	/**
	 * Description for 'excerpt'
	 *
	 * @var unknown
	 */
	private $excerpt, $plugin, $canonicalized_link;

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	protected $id, $title, $description, $tag_count, $author, $weight, $section, $date, $contributors, $contributor_ids, $children = array(), $weight_log = array();

	/**
	 * Description for 'has_parent'
	 *
	 * @var boolean
	 */
	protected $has_parent = false;

	/**
	 * Short description for 'is_in_section'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $section Parameter description (if any) ...
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function is_in_section($section, $plugin = NULL)
	{
		if (is_null($plugin))
		{
			$plugin = $this->plugin;
		}

		if (!$section)
		{
			return true;
		}

		if (count($section) == 2)
		{
			return strtolower($section[0]) == strtolower($plugin) && strtolower($section[1]) == $this->get_section();
		}

		return strtolower($section[0]) == strtolower($plugin);
	}

	/**
	 * Short description for 'get_weight_log'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_weight_log()
	{
		return $this->weight_log;
	}

	/**
	 * Short description for 'has_parent'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function has_parent()
	{
		return $this->has_parent;
	}

	/**
	 * Short description for 'set_has_parent'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $bool Parameter description (if any) ...
	 * @return     void
	 */
	public function set_has_parent($bool)
	{
		$this->has_parent = $bool;
	}

	/**
	 * Short description for 'add_child'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $child Parameter description (if any) ...
	 * @return     void
	 */
	public function add_child($child)
	{
		$this->children[] = $child;
		$child->set_has_parent(true);
	}

	/**
	 * Short description for 'get_children'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_children()
	{
		return $this->children;
	}

	/**
	 * Short description for 'sort_children'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $callback Parameter description (if any) ...
	 * @return     void
	 */
	public function sort_children($callback)
	{
		usort($this->children, $callback);
	}

	/**
	 * Short description for 'get'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function get($key)
	{
		return $this->$key;
	}

	/**
	 * Short description for 'get_title'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * Short description for 'get_highlighted_title'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_highlighted_title()
	{
		// return $this->title_highlighted; @FIXME: this property no longer seems to exist
		return $this->title;
	}

	/**
	 * Short description for 'get_raw_link'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_raw_link()
	{
		return $this->link;
	}

	/**
	 * Short description for 'get_link'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_link()
	{
		if (!$this->canonicalized_link)
		{
			if (preg_match('/^https?:\/\//', $this->link))
			{
				$this->canonicalized_link = $this->link;
			}
			else
			{
				$this->canonicalized_link = rtrim(\Request::base(), '/') . '/' . substr(ltrim(\Route::url($this->link), '/'), strlen(\Request::base(true)));
			}
		}
		return $this->canonicalized_link;
	}

	/**
	 * Short description for 'get_links'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function get_links()
	{
		$links = array($this->get_link());
		foreach ($this->children as $child)
		{
			$links = array_merge($links, $child->get_links());
		}
		return $links;
	}

	/**
	 * Short description for 'set_link'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $link Parameter description (if any) ...
	 * @return     void
	 */
	public function set_link($link)
	{
		$this->link = $link;
		$this->canonicalized_link = NULL;
	}

	/**
	 * Short description for 'get_description'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_description()
	{
		return $this->description;
	}

	/**
	 * Short description for 'set_description'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $descr Parameter description (if any) ...
	 * @return     void
	 */
	public function set_description($descr)
	{
		$this->description = $descr;
	}

	/**
	 * Short description for 'get_highlighted_description'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_highlighted_description()
	{
		return $this->description_highlighted;
	}

	/**
	 * Short description for 'get_author'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_author()
	{
		return $this->author;
	}

	/**
	 * Short description for 'get_weight'
	 *
	 * Long description (if any) ...
	 *
	 * @return     integer Return description (if any) ...
	 */
	public function get_weight()
	{
		return is_null($this->weight) ? 1 : $this->weight;
	}

	/**
	 * Short description for 'get_section'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_section()
	{
		return ucwords($this->section);
	}

	/**
	 * Short description for 'get_section_key'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function get_section_key()
	{
		return trim($this->section) ? str_replace(' ', '_', strtolower(trim($this->section))) : 'uncategorized';
	}

	/**
	 * Short description for 'get_date'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_date()
	{
		return $this->date;
	}

	/**
	 * Short description for 'get_contributors'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_contributors()
	{
		return $this->contributors;
	}

	/**
	 * Short description for 'get_contributor_ids'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_contributor_ids()
	{
		return $this->contributor_ids;
	}

	/**
	 * Short description for 'has_metadata'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function has_metadata()
	{
		return !!($this->section || $this->date || $this->contributors);
	}

	/**
	 * Short description for 'get_highlighted_excerpt'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_highlighted_excerpt()
	{
		return $this->get_excerpt();
	}

	/**
	 * Short description for 'adjust_weight'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $weight Parameter description (if any) ...
	 * @param      string $reason Parameter description (if any) ...
	 * @return     void
	 */
	public function adjust_weight($weight, $reason = 'unknown')
	{
		$this->weight *= $weight;
		$this->weight_log[] = 'adjusting by '.$weight.' to '.$this->weight.': '.$reason;
	}

	/**
	 * Short description for 'scale_weight'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $scale Parameter description (if any) ...
	 * @param      string $reason Parameter description (if any) ...
	 * @return     void
	 */
	public function scale_weight($scale, $reason = 'unknown')
	{
		if ($scale != 0)
		{
			$this->weight /= $scale;
			$this->weight_log[] = 'scaling by '.$scale.' to '.$this->weight.': '.$reason;
		}
	}

	/**
	 * Short description for 'add_weight'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $weight Parameter description (if any) ...
	 * @param      string $reason Parameter description (if any) ...
	 * @return     void
	 */
	public function add_weight($weight, $reason='unknown')
	{
		$this->weight += $weight;
		$this->weight_log[] = 'adding '.$weight.', total '.$this->weight.': '.$reason;
	}

	/**
	 * Short description for 'get_plugin'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_plugin()
	{
		return $this->plugin;
	}

	/**
	 * Short description for 'set_plugin'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $plg Parameter description (if any) ...
	 * @param      boolean $skip_cleanup Parameter description (if any) ...
	 * @return     void
	 */
	public function set_plugin($plg, $skip_cleanup = false)
	{
		$this->plugin = $skip_cleanup ? $plg : strtolower(preg_replace('/^plgSearch/i', '', $plg));
		foreach ($this->children as $child)
		{
			$child->set_plugin($this->plugin, true);
		}
	}

	/**
	 * Short description for 'highlight'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $regex Parameter description (if any) ...
	 * @return     void
	 */
	public function highlight($regex)
	{
		$this->highlight_regex = $regex;
		$hl = "#$regex#ims";
		foreach (array('title', 'author', 'excerpt') as $key)
		{
			if (!empty($this->$key))
			{
				$hlkey = "{$key}_highlighted";
				$this->$hlkey = preg_replace($hl, '<span class="highlight">$1</span>', $this->$key);
			}
		}

		foreach ($this->children as &$child)
		{
			$child->highlight($regex);
		}
	}

	/**
	 * Short description for 'get_excerpt'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_excerpt()
	{
		if (!$this->excerpt)
		{
			$descr = preg_replace('#(?:[{]xhub:.*?[}]|[{}]|[\#][!]html)#ixms', '', $this->description);
			if (preg_match_all('#(?:^|[.?!"])\s*?(.*?' . $this->highlight_regex . '.*?(?:[.?!"]|$))#ims', $descr, $excerpt))
			{
				$descr = join('<small>&hellip;</small> ', array_unique($excerpt[1]));
			}

			$wrapped = wordwrap(str_replace("\n", '', $descr), self::$intro_excerpt_len);
			$wrap_pos = strpos($wrapped, "\n");
			$this->excerpt = substr($wrapped, 0, $wrap_pos ? $wrap_pos : self::$intro_excerpt_len) . ($wrap_pos ? '<small>&hellip;</small>' : '');
			$this->excerpt = preg_replace("#$this->highlight_regex#ims", '<span class="highlight">$1</span>', $this->excerpt);
		}
		return $this->excerpt;
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 */
	abstract public function to_associative();
}
