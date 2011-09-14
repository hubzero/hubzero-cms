<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

abstract class YSearchResult
{
	private static $intro_excerpt_len = 350;
	private static $types = array();
	private $excerpt, $plugin, $canonicalized_link;
	protected $id, $title, $description, $tag_count, $author, $weight, $section, $date, $contributors, $contributor_ids, $children = array(), $weight_log = array();
	protected $has_parent = false;

	public function is_in_section($section, $plugin = NULL)
	{
		if (is_null($plugin))
			$plugin = $this->plugin;
		if (!$section)
			return true;
		if (count($section) == 2)
			return strtolower($section[0]) == strtolower($plugin) && strtolower($section[1]) == $this->get_section();
		return strtolower($section[0]) == strtolower($plugin);
	}
	public function get_weight_log() { return $this->weight_log; }
	public function has_parent() { return $this->has_parent; }
	public function set_has_parent($bool) { $this->has_parent = $bool; }
	public function add_child($child) { $this->children[] = $child; $child->set_has_parent(true); }
	public function get_children() { return $this->children; }
	public function sort_children($callback) { usort($this->children, $callback); }

	public function get($key) { return $this->$key; }
	public function get_title() { return $this->title; }
	public function get_highlighted_title() { return $this->title_highlighted; }
	public function get_raw_link() { return $this->link; }
	public function get_link()
	{
		if (!$this->canonicalized_link)
			if (preg_match('/^https?:\/\//', $this->link))
				$this->canonicalized_link = $this->link;
			else
				$this->canonicalized_link = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http').'://'.
					$_SERVER['HTTP_HOST'].(substr($this->link, 0, 1) == '/' ? $this->link : '/' . $this->link);
		return $this->canonicalized_link;
	}
	public function get_links()
	{
		$links = array($this->get_link());
		foreach ($this->children as $child)
			$links = array_merge($links, $child->get_links());
		return $links;
	}
	public function set_link($link) { $this->link = $link; $this->canonicalized_link = NULL; }
	public function get_description() { return $this->description; }
	public function set_description($descr) { $this->description = $descr; }
	public function get_highlighted_description() { return $this->description_highlighted; }
	public function get_author() { return $this->author; }
	public function get_weight() { return is_null($this->weight) ? 1 : $this->weight; }
	public function get_section() { return ucwords($this->section); }
	public function get_section_key() { return trim($this->section) ? str_replace(' ', '_', strtolower(trim($this->section))) : 'uncategorized'; }
	public function get_date() { return $this->date; }
	public function get_contributors() { return $this->contributors; }
	public function get_contributor_ids() { return $this->contributor_ids; }
	public function has_metadata() { return !!($this->section || $this->date || $this->contributors); }

	public function get_highlighted_excerpt() { return $this->get_excerpt(); }

	public function adjust_weight($weight, $reason = 'unknown')
	{
		$this->weight *= $weight;
		$this->weight_log[] = 'adjusting by '.$weight.' to '.$this->weight.': '.$reason;
	}
	public function scale_weight($scale, $reason = 'unknown')
	{
		if ($scale != 0)
		{
			$this->weight /= $scale;
			$this->weight_log[] = 'scaling by '.$scale.' to '.$this->weight.': '.$reason;
		}
	}
	public function add_weight($weight, $reason='unknown')
	{
		$this->weight += $weight;
		$this->weight_log[] = 'adding '.$weight.', total '.$this->weight.': '.$reason;
	}

	public function get_plugin() { return $this->plugin; }
	public function set_plugin($plg, $skip_cleanup = false)
	{
		$this->plugin = $skip_cleanup ? $plg : strtolower(preg_replace('/^plgYSearch/i', '', $plg));
		foreach ($this->children as $child)
			$child->set_plugin($this->plugin, true);
	}

	public function highlight($regex)
	{
		$this->highlight_regex = $regex;
		$hl = "#$regex#ims";
		foreach (array('title', 'author', 'excerpt') as $key)
			if (!empty($this->$key))
			{
				$hlkey = "{$key}_highlighted";
				$this->$hlkey = preg_replace($hl, '<span class="highlight">$1</span>', $this->$key);
			}

		foreach ($this->children as &$child)
			$child->highlight($regex);
	}

	public function get_excerpt()
	{
		if (!$this->excerpt)
		{
			$descr = preg_replace('#(?:[{]xhub:.*?[}]|[{}]|[\#][!]html)#ixms', '', $this->description);
			if (preg_match_all('#(?:^|[.?!"])\s*?(.*?'.$this->highlight_regex.'.*?(?:[.?!"]|$))#ims', $descr, $excerpt))
				$descr = join('<small>…</small> ', array_unique($excerpt[1]));

			$wrapped = wordwrap(str_replace("\n", '', $descr), self::$intro_excerpt_len);
			$wrap_pos = strpos($wrapped, "\n");
			$this->excerpt = substr($wrapped, 0, $wrap_pos ? $wrap_pos : self::$intro_excerpt_len) . ($wrap_pos ? '<small>&hellip;</small>' : '');
			$this->excerpt = preg_replace("#$this->highlight_regex#ims", '<span class="highlight">$1</span>', $this->excerpt);
		}
		return $this->excerpt;
	}

	abstract public function to_associative();
}

class YSearchResultEmpty extends YSearchResult
{
	public function to_associative() { throw new Exception('empty result -> to_associative'); }
}

abstract class YSearchResultAssoc extends YSearchResult
{
	abstract public function is_scalar();
}

class YSearchResultAssocList extends YSearchResultAssoc implements Iterator
{
	private $rows = array(), $pos = 0;

	public function is_scalar() { return false; }

	public function set_plugin($plugin, $skip_cleanup = false)
	{
		foreach ($this->rows as $row)
			$row->set_plugin($plugin, $skip_cleanup);
	}

	public function __construct($rows, $plugin = NULL)
	{
		$this->rows = is_array($rows) ? $rows : array($rows);
		$scale = 1;
		foreach ($this->rows as $idx=>&$row)
		{
			if (!is_a($row, 'YSearchResult'))
			{
				$row = new YSearchResultAssocScalar($row);
				$row->set_plugin($plugin);
			}

			if ($idx == 0 && ($weight = $row->get_weight()) > 1)
				$scale = $weight;

			if ($scale > 1)
			{
				$row->scale_weight($scale, 'normalizing within plugin');
			}
		}
	}

	public function &at($idx) { return $this->rows[$idx]; }

	public function to_associative() { return $this; }

	public function get_items() { return $this->rows; }

	public function rewind() { $this->pos = 0; }
	public function current() { return $this->rows[$this->pos]; }
	public function key() { return $this->pos; }
	public function next() { ++$this->pos; }
	public function valid() { return isset($this->rows[$this->pos]); }
}

class YSearchResultAssocScalar extends YSearchResult
{
	private static $tag_weight_modifier;
	private $row;

	public function is_scalar() { return true; }

	private static function assert_keys($keys, $row)
	{
		foreach ($keys as $key)
			if (!array_key_exists($key, $row))
				throw new YSearchPluginError("Result plugin did not define key '$key'");
	}

	public function __construct($row)
	{
		if (is_null(self::$tag_weight_modifier))
			self::$tag_weight_modifier = YSearchModelResultSet::get_tag_weight_modifier();

		self::assert_keys(array('title', 'description', 'link'), $row);
		foreach ($row as $key=>$val)
			$this->$key = is_array($val) ? array_map('stripslashes', array_map('strip_tags', $val)) : stripslashes(strip_tags($val));

		if ($this->weight === NULL)
		{
			if ($this->tag_count)
				$this->weight = $this->tag_count * (self::$tag_weight_modifier / 2);
			$this->weight = 1.0;
			$this->weight_log[] = 'plugin did not suggest weight, guessing '.$this->weight.' based on tag count('.$this->tag_count.')';
		}
		else if ($this->tag_count)
		{
			$this->weight_log[] = 'plugin suggested weight of '.$this->weight;
			$this->adjust_weight($this->tag_count * self::$tag_weight_modifier, 'tag count of '.$this->tag_count);
		}

		$this->contributors = $this->contributors ? array_unique(is_array($this->contributors) ? $this->contributors : split("\n", $row['contributors'])) : array();
		$this->contributor_ids = $this->contributor_ids ? array_unique(is_array($this->contributor_ids) ? $this->contributor_ids : split("\n", $row['contributor_ids'])) : array();

		if ($this->date) $this->date = strtotime($row['date']);
	}
	public function get_result() { return $this->row; }
	public function to_associative() { return $this; }
}

class YSearchResultSql extends YSearchResult
{
	public function __construct($sql = NULL) { $this->sql = $sql; }

	public function get_sql() { return $this->sql; }

	public function to_associative()
	{
		$dbh =& JFactory::getDBO();
		$dbh->setQuery($this->sql);
		if (isset($_GET['dbgsql']))
			echo '<pre>'.$dbh->getQuery().'</pre>';
		if (!($rows = $dbh->loadAssocList()))
		{
			if (($error = mysql_error()))
				throw new YSearchPluginError('Invalid SQL in '.$this->sql.': ' . $error );
			return new YSearchResultEmpty();
		}
		return new YSearchResultAssocList($rows, $this->get_plugin());
	}
}

