<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'SearchResult'
 *
 * Long description (if any) ...
 */
abstract class SearchResult
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
				/*$this->canonicalized_link = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http').'://'.
					$_SERVER['HTTP_HOST'].(substr($this->link, 0, 1) == '/' ? $this->link : '/' . $this->link);*/
				$this->canonicalized_link = rtrim(JURI::base(), '/') . '/' . substr(ltrim(JRoute::_($this->link), '/'), strlen(JURI::base(true)));
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
			$links = array_merge($links, $child->get_links());
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
			if (preg_match_all('#(?:^|[.?!"])\s*?(.*?'.$this->highlight_regex.'.*?(?:[.?!"]|$))#ims', $descr, $excerpt))
			{
				$descr = join('<small>…</small> ', array_unique($excerpt[1]));
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

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class SearchResultEmpty extends SearchResult
{

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 * @throws Exception  Exception description (if any) ...
	 */
	public function to_associative()
	{
		throw new Exception('empty result -> to_associative');
	}
}

/**
 * Short description for 'SearchResultAssoc'
 *
 * Long description (if any) ...
 */
abstract class SearchResultAssoc extends SearchResult
{

	/**
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 */
	abstract public function is_scalar();
}

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class SearchResultAssocList extends SearchResultAssoc implements Iterator
{
	/**
	 * Description for 'rows'
	 *
	 * @var array
	 */
	private $rows = array();

	/**
	 * Description for 'pos'
	 *
	 * @var integer
	 */
	private $pos = 0;

	/**
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function is_scalar()
	{
		return false;
	}

	/**
	 * Short description for 'set_plugin'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @param      boolean $skip_cleanup Parameter description (if any) ...
	 * @return     void
	 */
	public function set_plugin($plugin, $skip_cleanup = false)
	{
		foreach ($this->rows as $row)
		{
			$row->set_plugin($plugin, $skip_cleanup);
		}
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $rows Parameter description (if any) ...
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($rows, $plugin = NULL)
	{
		$this->rows = is_array($rows) ? $rows : array($rows);
		$scale = 1;
		foreach ($this->rows as $idx=>&$row)
		{
			if (!is_a($row, 'SearchResult'))
			{
				$row = new SearchResultAssocScalar($row);
				$row->set_plugin($plugin);
			}

			if ($idx == 0 && ($weight = $row->get_weight()) > 1)
			{
				$scale = $weight;
			}

			if ($scale > 1)
			{
				$row->scale_weight($scale, 'normalizing within plugin');
			}
		}
	}

	/**
	 * Short description for 'at'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $idx Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &at($idx)
	{
		return $this->rows[$idx];
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function to_associative()
	{
		return $this;
	}

	/**
	 * Short description for 'get_items'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function get_items()
	{
		return $this->rows;
	}

	/**
	 * Short description for 'rewind'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

	/**
	 * Short description for 'current'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function current()
	{
		return $this->rows[$this->pos];
	}

	/**
	 * Short description for 'key'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function key()
	{
		return $this->pos;
	}

	/**
	 * Short description for 'next'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function next()
	{
		++$this->pos;
	}

	/**
	 * Short description for 'valid'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function valid()
	{
		return isset($this->rows[$this->pos]);
	}
}

/**
 * Short description for 'SearchResultAssocScalar'
 *
 * Long description (if any) ...
 */
class SearchResultAssocScalar extends SearchResult
{

	/**
	 * Description for 'tag_weight_modifier'
	 *
	 * @var number
	 */
	private static $tag_weight_modifier;

	/**
	 * Description for 'row'
	 *
	 * @var unknown
	 */
	private $row;

	/**
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function is_scalar()
	{
		return true;
	}

	/**
	 * Short description for 'assert_keys'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $keys Parameter description (if any) ...
	 * @param      unknown $row Parameter description (if any) ...
	 * @return     void
	 * @throws SearchPluginError  Exception description (if any) ...
	 */
	private static function assert_keys($keys, $row)
	{
		foreach ($keys as $key)
		{
			if (!array_key_exists($key, $row))
			{
				throw new SearchPluginError("Result plugin did not define key '$key'");
			}
		}
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $row Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($row)
	{
		if (is_null(self::$tag_weight_modifier))
		{
			self::$tag_weight_modifier = SearchModelResultSet::get_tag_weight_modifier();
		}

		self::assert_keys(array('title', 'description', 'link'), $row);
		foreach ($row as $key=>$val)
		{
			$this->$key = is_array($val) ? array_map('stripslashes', array_map('strip_tags', $val)) : stripslashes(strip_tags($val));
		}

		if ($this->weight === NULL)
		{
			if ($this->tag_count)
			{
				$this->weight = $this->tag_count * (self::$tag_weight_modifier / 2);
			}
			$this->weight = 1.0;
			$this->weight_log[] = 'plugin did not suggest weight, guessing '.$this->weight.' based on tag count('.$this->tag_count.')';
		}
		else if ($this->tag_count)
		{
			$this->weight_log[] = 'plugin suggested weight of '.$this->weight;
			$this->adjust_weight($this->tag_count * self::$tag_weight_modifier, 'tag count of '.$this->tag_count);
		}

		$this->contributors = $this->contributors ? array_unique(is_array($this->contributors) ? $this->contributors : preg_split("#\n#", $row['contributors'])) : array();
		$this->contributor_ids = $this->contributor_ids ? array_unique(is_array($this->contributor_ids) ? $this->contributor_ids : preg_split("#\n#", $row['contributor_ids'])) : array();

		if ($this->date && $this->date != '0000-00-00 00:00:00')
		{
			$this->date = strtotime($row['date']);
		}
		else
		{
			$this->date = null;
		}
	}

	/**
	 * Short description for 'get_result'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_result()
	{
		return $this->row;
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function to_associative()
	{
		return $this;
	}
}

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class SearchResultSql extends SearchResult
{
	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $sql Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($sql = NULL)
	{
		$this->sql = $sql;
	}

	/**
	 * Short description for 'get_sql'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_sql()
	{
		return $this->sql;
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 * @throws SearchPluginError  Exception description (if any) ...
	 */
	public function to_associative()
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery($this->sql);

		if (isset($_GET['dbgsql']))
		{
			echo '<pre>'.$dbh->getQuery().'</pre>';
		}

		if (!($rows = $dbh->loadAssocList()))
		{
			if (($error = $dbh->getErrorMsg()))
			{
				throw new SearchPluginError('Invalid SQL in '.$this->sql.': ' . $error );
			}
			return new SearchResultEmpty();
		}
		return new SearchResultAssocList($rows, $this->get_plugin());
	}
}

