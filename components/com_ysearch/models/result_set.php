<?php

jimport('joomla.application.component.model');

class YSearchModelWidgetSet extends JModel implements Iterator
{
	private $widgets = array();

	public function add_html($widget)
	{
		$this->widgets[] = $widget;
	}

	public function rewind() { $this->pos = 0; }
	public function current() { return $this->widgets[$this->pos]; }
	public function key() { return $this->pos; }
	public function next() { ++$this->pos; }
	public function valid() { return isset($this->widgets[$this->pos]); }
}

class YSearchModelResultSet extends JModel implements Iterator
{
	private static $plugin_weights;
	private $tags = array(), $total_list_count, $limit, $widgets, $custom_title = NULL, $custom_mode = false, $offset, $pos = 0, $results = array(), $custom = array(), $processed_results = array(), $highlighter, $current_plugin, $total_count, $tag_mode = false, $result_counts = array(), $shown_results = array(), $sorters = array();

	public function get_tags() { return $this->tags; }
	public function get_limit() { return $this->limit; }
	public function get_offset() { return $this->offset; }
	public function set_limit($limit) { $this->limit = $limit; }
	public function set_offset($offset) { $this->offset = $offset; }
	public function get_widgets() { return $this->widgets; }
	public function get_shown_results() { return $this->shown_results; }
	public function get_shown_count() { return count($this->shown_results); }
	public function get_result_counts() { return $this->result_counts; }
	public static function get_tag_weight_modifier()
	{
		return array_key_exists('tagmod', self::$plugin_weights) ? self::$plugin_weights['tagmod'] : 1.3;
	}

	public function get_custom_title() { return $this->custom_title; }

	public function collect($force_generic = false)
	{
		if (!$this->terms->any() || !($positive_terms = $this->terms->get_positive_chunks())) 
			return;
		
		$req = new YSearchModelRequest($this->terms);
		$this->tags = $req->get_tags();

		$weighters = array('all' => array());
		JPluginHelper::importPlugin('ysearch');
		$plugins = JPluginHelper::getPlugin('ysearch');

		$this->custom_mode = true;
		if (!$force_generic)
			foreach ($plugins as $plugin)
				if (JPluginHelper::isEnabled('ysearch', $plugin->name))
				{
					$refl = new ReflectionClass("plgYSearch$plugin->name");
					if ($refl->hasMethod('onYSearchCustom'))
					{
						$this->current_plugin = $plugin->name;
						if ($this->custom_title = $refl->getMethod('onYSearchCustom')->invokeArgs(NULL, array($req, &$this)))
							break;
					}
				}
		$this->custom_mode = false;

		foreach ($plugins as $plugin)
		{
			if (JPluginHelper::isEnabled('ysearch', $plugin->name))
			{
				$refl = new ReflectionClass("plgYSearch$plugin->name");
				$this->current_plugin = $plugin->name;
				$weighters[$plugin->name] = array();
				
				if ($refl->hasMethod('onYSearch'))
					$refl->getMethod('onYSearch')->invokeArgs(NULL, array($req, &$this));
				
				$this->result_counts[$plugin->name] = array(
					'friendly_name' => 
						$refl->hasMethod('getName') 
							? $refl->getMethod('getName')->invoke(NULL)
							: ucwords($plugin->name), 
					'count' => 0
				);
				if ($refl->hasMethod('onYSearchWidget'))
					$refl->getMethod('onYSearchWidget')->invokeArgs(NULL, array($req, &$this->widgets));
				
				if ($refl->hasMethod('onYSearchSort'))
					$this->sorters[] = array($plugin_name => $refl->getMethod('onYSearchSort'));
			}
		}

		$plugin_types = array_keys($weighters);
		foreach ($plugins as $plugin)
		{
			$class = "plgYSearch$plugin->name";
			$refl = new ReflectionClass($class);
			if ($refl->hasMethod('onYSearchWeightAll'))
				$weighters['all'][] = array($plugin->name, $refl->getMethod('onYSearchWeightAll'));
		
			foreach ($plugin_types as $type)
				if ($refl->hasMethod("onYSearchWeight$type"))
					$weighters[$type][] = array($plugin->name, $refl->getMethod("onYSearchWeight$type"));
		}
		$this->current_plugin = NULL;
		
		$this->highlighter = $this->terms->get_word_regex();
		foreach ($this->results as $res)
			$this->process_result($res);

		// call weighters
		$any_all_weighters = !!count($weighters['all']);
		@list($term_plugin, $term_section) = $this->terms->get_section();
		$flat_results = $this->processed_results;
		foreach ($flat_results as $res)
			foreach ($res->get_children() as $child)
				$flat_results[] = $child;
		
		foreach ($flat_results as $res)
		{
			$plugin = $res->get_plugin();
			$this->result_counts[$plugin]['count']++;
			if ((!$term_plugin || $term_plugin == $plugin) && (!$term_section || $term_section == $res->get_section_key()))
			{
				$weight_adj = 1;
				if (array_key_exists($plugin, self::$plugin_weights))
					$weight_adj *= self::$plugin_weights[$plugin];
				foreach (array_key_exists($plugin, $weighters) ? array_merge($weighters['all'], $weighters[$plugin]) : $weighters['all'] as $weight_plugin)
				{
					list($name, $mtd) = $weight_plugin;
					$weight_adj *= 2 * ($mtd->invokeArgs(NULL, array($this->terms, &$res)) * (array_key_exists($name, self::$plugin_weights) ? self::$plugin_weights[$name] : 1));
				}
				$res->adjust_weight($weight_adj);
				$this->shown_results[] = $res;
			}
		}

		usort($this->shown_results, array($this, 'sort_results'));

		$this->total_count = count($this->processed_results);

		if ($this->custom)
		{
			# remove dupes
			$custom_links = array();
			foreach ($this->custom as $custom)
			{
				$plugin = $custom->get_plugin();
				$this->result_counts[$plugin]['count']++;
				$custom->highlight($this->highlighter);
				$custom_links[$custom->get_link()] = $custom->get_link();
			}

			foreach ($this->shown_results as $idx=>$res)
				if (array_key_exists($res->get_link(), $custom_links))
				{
					unset($this->shown_results[$idx]);
					--$this->result_counts[$res->get_plugin()]['count'];
					--$this->total_count;
				}

			# prepend custom results
			$this->shown_results = array_merge($this->custom, $this->shown_results);
			$this->total_count += count($this->custom);
		}

		// Copy counts to list counts, which track how many actual <li> worth of
		// search results there are.
		//
		// The actual result counts may be higher, because some will be shown as
		// nested hierarchies of results in a single <li>, ie for courses that
		// all part of the same series.
		$this->total_list_count = $this->total_count;
		foreach ($this->result_counts as $plugin=>$def)
			$this->result_counts[$plugin]['list_count'] = $this->result_counts[$plugin]['count'];

		// determine section counts and adjust plugin counts to account for
		// folded-together results
		foreach ($this->processed_results as $parent)
		{
			$plugin = $parent->get_plugin();
			if (!array_key_exists('sections', $this->result_counts[$plugin]))
				$this->result_counts[$plugin]['sections'] = array();
		
			foreach (array_merge(array($parent), $parent->get_children()) as $idx=>$res)
			{
				// this is a child result that was folded in, increase the plugin count
				// to show that there are really more results
				if ($idx > 0)
				{
					++$this->total_count;
					++$this->result_counts[$plugin]['count'];
				}
				
				$section = $res->get_section();	
				if (!trim($section))
					$section = 'Uncategorized';
				$section_key = strtolower(str_replace(' ', '_', trim($section)));

				if (!array_key_exists($section_key, $this->result_counts[$plugin]['sections']))
					$this->result_counts[$plugin]['sections'][$section_key] = array('name' => $section, 'count' => 1);
				else
					++$this->result_counts[$plugin]['sections'][$section_key]['count'];
			}
		}

		if ($this->limit > 0)
			$this->shown_results = array_slice($this->shown_results, $this->offset, $this->limit);
	}

	private function sort_results($a, $b)
	{
		$weight = array(
			'a' => 0, 
			'b' => 0
		);
		if (($relevance_diff = $a->get_weight() - $b->get_weight()))
			$weight[$relevance_diff > 0 ? 'a' : 'b'] += self::$plugin_weights['sortrelevance'];
		if (($date_diff = $a->get_date() - $b->get_date()))
			$weight[$date_diff > 0 ? 'a' : 'b'] += self::$plugin_weights['sortnewer'];

		$diff = $weight['a'] - $weight['b'];
		return $diff == 0 ? 0 : $diff > 0 ? -1 : 1;
	}

	public function __construct($terms) 
	{
		parent::__construct();
		if (is_null(self::$plugin_weights) && $terms->any())
		{
			self::$plugin_weights = array();
			$this->_db->setQuery('SELECT plugin, weight FROM #__ysearch_plugin_weights');
			foreach ($this->_db->loadAssocList() as $weight)
				self::$plugin_weights[$weight['plugin']] = $weight['weight'];
		}
		
		$this->terms = $terms;
		$this->widgets = new YSearchModelWidgetSet();
	}

	public function add($res)
	{
		if (is_array($res))
			$res = array_key_exists(0, $res) ? new YSearchResultAssocList($res) : new YSearchResultAssocScalar($res);
		$res->set_plugin($this->current_plugin);
	
		if ($this->custom_mode)
			$this->custom[] = $res;
		else
			$this->results[] = $res;
	}

	private function process_result($res)
	{
		$res = $res->to_associative();
		
		if (is_a($res, 'YSearchResultEmpty'))
			return;

		if ($res->is_scalar())
		{
			$res->highlight($this->highlighter);
			$this->processed_results[] = $res;
		}
		else 
			foreach ($res as $subres)
				$this->process_result($subres);
	}

	public function get_total_count()
	{
		return $this->total_count;
	}

	public function get_total_list_count()
	{
		return $this->total_list_count;
	}

	public function get_plugin_list_count()
	{
		@list($term_plugin, $term_section) = $this->terms->get_section();
		if ($term_plugin && $term_section)
			return $this->result_counts[$term_plugin]['sections'][$term_section]['count'];
		if ($term_plugin)
			return $this->result_counts[$term_plugin]['list_count'];
		
		return $this->total_list_count;
	}

	public function get_plugin_count()
	{
		list($term_plugin, $term_section) = $this->terms->get_section();
		if ($term_plugin)
			return $this->result_counts[$term_plugin]['count'];
		return $this->total_count;
	}

	public function get_shown() { return $this->shown_results; }

	public function rewind() { $this->pos = 0; }
	public function current() { return $this->shown_results[$this->pos]; }
	public function key() { return $this->pos; }
	public function next() { ++$this->pos; }
	public function valid() { return isset($this->shown_results[$this->pos]); }
}
