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

namespace Components\Search\Models\Basic;

jimport('joomla.application.component.model');

/**
 * Search terms
 */
class Terms extends \JModel
{

	/**
	 * Description for 'raw'
	 *
	 * @var unknown
	 */
	private $raw, $positive_chunks, $optional_chunks = array(), $forbidden_chunks = array(), $mandatory_chunks = array(), $section = NULL, $quoted = array();

	/**
	 * Constructor
	 *
	 * @param   string  $raw
	 * @return  void
	 */
	public function __construct($raw)
	{
		$this->raw = preg_replace('/^\s+|\s+$/', '', preg_replace('/\s+/', ' ', $raw));
		if ($this->is_set())
		{
			$this->parse_searchable_chunks();
		}
	}

	/**
	 * Short description for 'is_quoted'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $idx Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function is_quoted($idx)
	{
		return isset($this->quoted[$idx]) ? $this->quoted[$idx] : false;
	}

	/**
	 * Short description for 'get_raw'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_raw()
	{
		return $this->raw;
	}

	/**
	 * Short description for 'get_raw_without_section'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function get_raw_without_section()
	{
		if (!$this->section) return $this->raw;

		return preg_replace('/^'.implode(':', $this->section).':/', '', $this->raw);
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
		return $this->section;
	}

	/**
	 * Short description for 'get_optional_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_optional_chunks()
	{
		return $this->optional_chunks;
	}

	/**
	 * Short description for 'get_forbidden_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_forbidden_chunks()
	{
		return $this->forbidden_chunks;
	}

	/**
	 * Short description for 'get_mandatory_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_mandatory_chunks()
	{
		return $this->mandatory_chunks;
	}

	/**
	 * Short description for 'get_positive_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_positive_chunks()
	{
		if (!$this->positive_chunks)
		{
			$this->positive_chunks = array_unique(array_merge($this->mandatory_chunks, $this->optional_chunks));
		}
		return $this->positive_chunks;
	}

	/**
	 * Short description for 'get_stemmed_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function get_stemmed_chunks()
	{
		$chunks = $this->get_positive_chunks();
		foreach ($chunks as $term)
		{
			while (($stemmed = stem($term)) != $term)
			{
				$chunks[] = $stemmed;
				$term = $stemmed;
			}
		}
		$chunks = array_unique(array_merge(array_map('stem', $chunks), $chunks));
		\Event::trigger('onSearchExpandTerms', array(&$chunks));

		return array_unique($chunks);
	}

	/**
	 * Short description for 'is_set'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function is_set()
	{
		return !!$this->raw;
	}

	/**
	 * Short description for 'any'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function any()
	{
		return !!($this->optional_chunks || $this->mandatory_chunks);
	}

	/**
	 * Short description for 'parse_searchable_chunks'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function parse_searchable_chunks()
	{
		$accumulating_phrase = false;
		$partial = '';
		$sign = '';
		$raw = trim(strtolower($this->raw));
		if (preg_match('/^([_.\-,a-z:]+):/', $raw, $match))
		{
			$this->section = explode(':', $match[1]);
			$raw = preg_replace('/^'.preg_quote($match[1]).':/', '', $raw);
		}
		else if (array_key_exists('section', $_GET))
		{
			$this->section = array($_GET['section']);
		}

		$raw = preg_replace('#[^-:/\\+"[:alnum:] ]#', '', preg_replace('/\s+/', ' ', trim($raw)));
		for ($idx = 0, $len = strlen($raw); $idx < $len; ++$idx)
		{
			$cur = $raw[$idx];
			if ($accumulating_phrase)
			{
				if ($cur == '"')
				{
					$accumulating_phrase = false;
					$this->add_chunk($partial, $sign, true);
				}
				else
				{
					$partial .= $cur;
				}
			}
			else if ($cur == '"')
			{
				if ($partial)
				{
					$this->add_chunk($partial, $sign);
				}
				$accumulating_phrase = true;
			}
			else if ($cur == ' ')
			{
				$this->add_chunk($partial, $sign);
			}
			else if ($cur == '+' && $partial == '')
			{
				$sign = '+';
			}
			else if ($cur == '-' && $partial == '')
			{
				$sign = '-';
			}
			else if ($cur != ' ')
			{
				$partial .= $cur;
			}
		}
		$this->add_chunk($partial, $sign);
	}

	/**
	 * Short description for 'add_chunk'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string &$partial Parameter description (if any) ...
	 * @param      string &$sign Parameter description (if any) ...
	 * @param      boolean $quoted Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function add_chunk(&$partial, &$sign = '', $quoted = false)
	{
		if (!$partial) return;

		if (DocumentMetadata::is_stop_word($partial))
		{
			$partial = '';
			$sign = '';
			return;
		}

		if ($sign == '-')
		{
			$this->forbidden_chunks[] = $partial;
		}
		else if ($sign == '+')
		{
			$this->mandatory_chunks[] = $partial;
		}
		else
		{
			$this->optional_chunks[] = $partial;
			$this->quoted[count($this->optional_chunks) - 1] = $quoted;
		}

		$partial = '';
		$sign = '';
	}

	/**
	 * Short description for 'get_word_regex'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function get_word_regex()
	{
		$chunks = $this->get_stemmed_chunks();
		usort($chunks, create_function('$a, $b', '$al = strlen($a); $bl = strlen($b); if ($al == $bl) return 0; return $al > $bl ? -1 : 1;'));
		return '('.join('|', array_map('preg_quote', $chunks)).'[[:alpha:]]*)';
	}

	/**
	 * Short description for '__toString'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function __toString()
	{
		return $this->raw;
	}
}

