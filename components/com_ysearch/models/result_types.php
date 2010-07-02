<?php
abstract class YSearchResult 
{
	private static $intro_excerpt_len = 350;
	private static $types = array();
	private $excerpt, $plugin, $canonicalized_link;
	protected $title, $description, $tag_count, $author, $weight, $section, $date, $contributors, $contributor_ids, $children = array();

	public function add_child($child) { $this->children[] = $child; }
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

	public function adjust_weight($weight) { $this->weight *= $weight; }
	public function scale_weight($scale) { if ($scale != 0) $this->weight /= $scale; }
	public function add_weight($weight)	{ $weight; $this->weight += $weight; }

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
		$hl = "/$regex/ims";
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
			$descr = preg_replace('/[{]xhub:.*?[}]/ixms', '', $this->description);
			if (preg_match_all('/(?:^|[.?!"])\s*?(.*?'.$this->highlight_regex.'.*?(?:[.?!"]|$))/ims', $descr, $excerpt))
				$descr = join('<small>…</small> ', array_unique($excerpt[1]));

			$wrapped = wordwrap(str_replace("\n", '', $descr), self::$intro_excerpt_len);
			$wrap_pos = strpos($wrapped, "\n");
			$this->excerpt = substr($wrapped, 0, $wrap_pos ? $wrap_pos : self::$intro_excerpt_len) . ($wrap_pos ? '<small>…</small>' : '');
			$this->excerpt = preg_replace("/$this->highlight_regex/ims", '<span class="highlight">$1</span>', $this->excerpt);
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
				$row->scale_weight($scale);
		}
	}

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
		}
		else if ($this->tag_count)
		{
			$this->weight *= ($this->tag_count * self::$tag_weight_modifier);
		}
		
		$this->contributors = $this->contributors ? array_unique(is_array($this->contributors) ? $this->contributors : split("\n", $this->contributors)) : array();
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
		if (!($rows = $dbh->loadAssocList()))
		{
			if (($error = mysql_error()))
				throw new YSearchPluginError('Invalid SQL in '.$this->sql.': ' . $error );
			return new YSearchResultEmpty();
		}
		return new YSearchResultAssocList($rows, $this->get_plugin());
	}
}
