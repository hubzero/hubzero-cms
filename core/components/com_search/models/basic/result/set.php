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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Authorization;
use Components\Search\Models\Basic\Request;
use ReflectionClass;
use Iterator;
use Plugin;

jimport('joomla.application.component.model');

/**
 * Search result set
 */
class Set extends \JModel implements Iterator
{
	/**
	 * Description for 'plugin_weights'
	 *
	 * @var array
	 */
	private static $plugin_weights;

	/**
	 * Description for 'tags'
	 *
	 * @var array
	 */
	private $tags = array(), $total_list_count, $limit, $widgets, $custom_title = NULL, $custom_mode = false, $offset, $pos = 0, $results = array(), $custom = array(), $processed_results = array(), $highlighter, $current_plugin, $total_count, $tag_mode = false, $result_counts = array(), $shown_results = array(), $sorters = array();

	/**
	 * Short description for 'get_tags'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function get_tags()
	{
		return ($this->tags instanceof Blank) ? array() : $this->tags;
	}

	/**
	 * Short description for 'get_limit'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_limit()
	{
		return $this->limit;
	}

	/**
	 * Short description for 'get_offset'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_offset()
	{
		return $this->offset;
	}

	/**
	 * Short description for 'set_limit'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $limit Parameter description (if any) ...
	 * @return     void
	 */
	public function set_limit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * Short description for 'set_offset'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $offset Parameter description (if any) ...
	 * @return     void
	 */
	public function set_offset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * Short description for 'get_widgets'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_widgets()
	{
		return $this->widgets;
	}

	/**
	 * Short description for 'get_shown_results'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_shown_results()
	{
		return $this->shown_results;
	}

	/**
	 * Short description for 'get_shown_count'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_shown_count()
	{
		return count($this->shown_results);
	}

	/**
	 * Short description for 'get_result_counts'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_result_counts()
	{
		return $this->result_counts;
	}

	/**
	 * Short description for 'get_tag_weight_modifier'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public static function get_tag_weight_modifier()
	{
		return array_key_exists('tagmod', self::$plugin_weights) ? self::$plugin_weights['tagmod'] : 1.3;
	}

	/**
	 * Short description for 'get_custom_title'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_custom_title()
	{
		return $this->custom_title;
	}

	/**
	 * Short description for 'collect'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $force_generic Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function collect($force_generic = false)
	{
		if (!$this->terms->any() || !($positive_terms = $this->terms->get_positive_chunks()))
		{
			return;
		}

		$authz = new Authorization();
		$req = new Request($this->terms);
		$this->tags = $req->get_tags();

		$weighters = array('all' => array());
		$plugins = Plugin::byType('search');

		$this->custom_mode = true;
		// Poll custom result plugins, like the one that shows matching members at the top of the list
		if (!$force_generic)
		{
			foreach ($plugins as $plugin)
			{
				if (Plugin::isEnabled('search', $plugin->name))
				{
					$refl = new ReflectionClass("plgSearch$plugin->name");
					if ($refl->hasMethod('onSearchCustom'))
					{
						$this->current_plugin = $plugin->name;
						if ($this->custom_title = $refl->getMethod('onSearchCustom')->invokeArgs(NULL, array($req, &$this, $authz)))
						{
							break;
						}
					}
				}
			}
		}

		$this->custom_mode = false;

		foreach ($plugins as $plugin)
		{
			if (!Plugin::isEnabled('search', $plugin->name))
			{
				continue;
			}

			$refl = new ReflectionClass("plgSearch$plugin->name");
			$this->current_plugin = $plugin->name;
			$weighters[$plugin->name] = array();

			// generic resource plugin
			if ($refl->hasMethod('onSearch'))
			{
				$refl->getMethod('onSearch')->invokeArgs(NULL, array($req, &$this, $authz));
			}

			$this->result_counts[$plugin->name] = array(
				'friendly_name' =>
					$refl->hasMethod('getName')
						? $refl->getMethod('getName')->invoke(NULL)
						: ucwords($plugin->name),
				'plugin_name' => $plugin->name,
				'count' => 0
			);

			// custom results like Google does when you enter a stock symbol. tags used to be implemented this way, but they got moved into the core so other plugins could use them to find tagged results
			if ($refl->hasMethod('onSearchWidget'))
			{
				$refl->getMethod('onSearchWidget')->invokeArgs(NULL, array($req, &$this->widgets, $authz));
			}

			if ($refl->hasMethod('onSearchSort'))
			{
				$this->sorters[$plugin->name] = $refl->getMethod('onSearchSort');
			}
		}

		// Loop the plugins once more now that we have an exhaustive list of plugin types
		$plugin_types = array_keys($weighters);
		foreach ($plugins as $plugin)
		{
			if (!Plugin::isEnabled('search', $plugin->name))
			{
				continue;
			}

			$class = "plgSearch$plugin->name";
			$refl = new ReflectionClass($class);
			if ($refl->hasMethod('onSearchWeightAll'))
			{
				$weighters['all'][] = array($plugin->name, $refl->getMethod('onSearchWeightAll'));
			}

			foreach ($plugin_types as $type)
			{
				if ($refl->hasMethod("onSearchWeight$type"))
				{
					$weighters[$type][] = array($plugin->name, $refl->getMethod("onSearchWeight$type"));
				}
			}
		}
		$this->current_plugin = NULL;

		$this->highlighter = $this->terms->get_word_regex();
		foreach ($this->results as $res)
		{
			$this->process_result($res);
		}

		// call weighters
		$any_all_weighters = !!count($weighters['all']);
		@list($term_plugin, $term_section) = $this->terms->get_section();
		$flat_results = $this->processed_results;
		foreach ($flat_results as $res)
		{
			$fc_child_flag = 'plgSearch' . $res->get_plugin() . '::FIRST_CLASS_CHILDREN';
			if (!defined($fc_child_flag) || constant($fc_child_flag))
			{
				foreach ($res->get_children() as $child)
				{
					$flat_results[] = $child;
				}
			}
		}

		foreach ($flat_results as $res)
		{
			$plugin = $res->get_plugin();
			$this->result_counts[$plugin]['count']++;
			if ((!$term_plugin || $term_plugin == $plugin) && (!$term_section || $term_section == $res->get_section_key()))
			{
				if (array_key_exists($plugin, self::$plugin_weights))
				{
					$res->adjust_weight(self::$plugin_weights[$plugin], $plugin . ' base weight');
				}
				$used = array();
				foreach (array_key_exists($plugin, $weighters) ? array_merge($weighters['all'], $weighters[$plugin]) : $weighters['all'] as $weight_plugin)
				{
					list($name, $mtd) = $weight_plugin;
					if (!array_key_exists($name, $used))
					{
						$used[$name] = true;
						$adj = 2 * $mtd->invokeArgs(NULL, array($this->terms, &$res));
						if (array_key_exists($name, self::$plugin_weights))
						{
							// adj is 0..1
							// weight is 0..1
							$adj *= (0.5 + self::$plugin_weights[$name]);
						}
						$res->adjust_weight($adj, $name);
					}
				}
				$this->shown_results[] = $res;
			}
		}
		usort($this->shown_results, array($this, 'sort_results'));

		$links = array();
		foreach ($this->shown_results as $res)
		{
			$links[] = array(spl_object_hash($res), $res->has_parent(), $res->get_links());
		}

		$link_map = array();
		foreach ($links as $row)
		{
			list($id, $has_parent, $rlinks) = $row;
			foreach ($rlinks as $link)
			{
				if (array_key_exists($id, $link_map))
				{
					$link_map[$link] = array(array($id, $has_parent));
				}
				else
				{
					$link_map[$link][] = array($id, $has_parent);
				}
			}
		}
		$dont_show_because_nested_elsewhere = array();
		foreach ($link_map as $rows)
		{
			if (count($rows) > 0)
			{
				foreach ($rows as $row)
				{
					list($id, $has_parent) = $row;
					if ($has_parent)
					{
						$dont_show_because_nested_elsewhere[$id] = 1;
					}
				}
			}
		}

		$this->total_count = count($this->processed_results);
		foreach ($this->shown_results as $idx=>$res)
		{
			if (array_key_exists(spl_object_hash($res), $dont_show_because_nested_elsewhere))
			{
				unset($this->shown_results[$idx]);
				++$this->total_count;
			}
		}

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
			{
				if (array_key_exists($res->get_link(), $custom_links))
				{
					unset($this->shown_results[$idx]);
					--$this->result_counts[$res->get_plugin()]['count'];
					--$this->total_count;
				}
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
		foreach ($this->result_counts as $plugin => $def)
		{
			$this->result_counts[$plugin]['list_count'] = $this->result_counts[$plugin]['count'];
		}

		// determine section counts and adjust plugin counts to account for
		// folded-together results
		foreach ($this->processed_results as $parent)
		{
			$plugin = $parent->get_plugin();

			$ccount = count($parent->get_children());
			$this->total_list_count -= $ccount;
			$this->result_counts[$plugin]['list_count'] -= $ccount;

			if (!array_key_exists('sections', $this->result_counts[$plugin]))
			{
				$this->result_counts[$plugin]['sections'] = array();
			}

			foreach (array_merge(array($parent), $parent->get_children()) as $idx=>$res)
			{
				if ($idx > 0)
				{
					--$this->total_list_count;
					--$this->result_counts[$plugin]['list_count'];
				}

				$section = $res->get_section();
				if (!trim($section))
				{
					$section = 'Uncategorized';
				}
				$section_key = strtolower(str_replace(' ', '_', trim($section)));

				if (!array_key_exists($section_key, $this->result_counts[$plugin]['sections']))
				{
					$this->result_counts[$plugin]['sections'][$section_key] = array('name' => $section, 'count' => 1);
				}
				else
				{
					++$this->result_counts[$plugin]['sections'][$section_key]['count'];
				}
			}
		}

		// Recalculate the totals
		// [CDMHUB][#1034]
		$total = 0;
		foreach ($this->result_counts as $plugin => $def)
		{
			$total += $this->result_counts[$plugin]['count'];
		}
		$this->total_list_count = $this->total_count = $total;

		if ($this->limit > 0)
		{
			$this->shown_results = array_slice($this->shown_results, $this->offset, $this->limit);
		}
	}

	/**
	 * Short description for 'sort_results'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      mixed $b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function sort_results($a, $b)
	{
		$weight = array(
			'a' => 0,
			'b' => 0
		);
		if (($relevance_diff = $a->get_weight() - $b->get_weight()))
		{
			$weight[$relevance_diff > 0 ? 'a' : 'b'] += self::$plugin_weights['sortrelevance'];
		}
		if (($date_diff = $a->get_date() - $b->get_date()))
		{
			$weight[$date_diff > 0 ? 'a' : 'b'] += self::$plugin_weights['sortnewer'];
		}

		foreach ($this->sorters as $plugin=>$sorter)
		{
			if (!($res = $sorter->invoke(NULL, $a, $b)))
			{
				continue;
			}

			$weight[$res > 0 ? 'a' : 'b'] += array_key_exists($plugin, self::$plugin_weights) ? self::$plugin_weights[$plugin] : 1;
		}

		$diff = $weight['a'] - $weight['b'];
		return $diff == 0 ? 0 : $diff > 0 ? -1 : 1;
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $terms Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($terms)
	{
		parent::__construct();

		if (is_null(self::$plugin_weights) && $terms->any())
		{
			self::$plugin_weights = array();
			$this->_db->setQuery('SELECT plugin, weight FROM `#__ysearch_plugin_weights`');
			foreach ($this->_db->loadAssocList() as $weight)
			{
				self::$plugin_weights[$weight['plugin']] = $weight['weight'];
			}
		}

		$this->terms = $terms;
		$this->widgets = array();
	}

	/**
	 * Short description for 'add'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $res Parameter description (if any) ...
	 * @return     void
	 */
	public function add($res)
	{
		$reso = $this->current_plugin == 'resources';
		if (is_array($res))
		{
			$res = array_key_exists(0, $res) ? new AssocList($res) : new AssocScalar($res);
		}
		$res->set_plugin($this->current_plugin);

		if ($this->custom_mode)
		{
			$this->custom[] = $res;
		}
		else
		{
			$this->results[] = $res;
		}
	}

	/**
	 * Short description for 'process_result'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $res Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function process_result($res)
	{
		$res = $res->to_associative();

		if ($res instanceof Blank)
		{
			return;
		}

		if ($res->is_scalar())
		{
			$res->highlight($this->highlighter);
			$this->processed_results[] = $res;
		}
		else
		{
			foreach ($res as $subres)
			{
				$this->process_result($subres);
			}
		}
	}

	/**
	 * Short description for 'get_total_count'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_total_count()
	{
		return $this->total_count;
	}

	/**
	 * Short description for 'get_total_list_count'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_total_list_count()
	{
		return $this->total_list_count;
	}

	/**
	 * Short description for 'get_plugin_list_count'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function get_plugin_list_count()
	{
		@list($term_plugin, $term_section) = $this->terms->get_section();

		if ($term_plugin && $term_section)
		{
			return $this->result_counts[$term_plugin]['sections'][$term_section]['count'];
		}
		if ($term_plugin)
		{
			return $this->result_counts[$term_plugin]['list_count'];
		}

		return $this->total_list_count;
	}

	/**
	 * Short description for 'get_plugin_count'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function get_plugin_count()
	{
		list($term_plugin, $term_section) = $this->terms->get_section();

		if ($term_plugin)
		{
			return $this->result_counts[$term_plugin]['count'];
		}
		return $this->total_count;
	}

	/**
	 * Short description for 'get_shown'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_shown()
	{
		return $this->shown_results;
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
		return $this->shown_results[$this->pos];
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
		return isset($this->shown_results[$this->pos]);
	}
}

