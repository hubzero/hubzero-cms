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

jimport('joomla.application.component.model');

include_once __DIR__ . '/document_metadata.php';

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class SearchModelTerms extends JModel
{

	/**
	 * Description for 'raw'
	 *
	 * @var unknown
	 */
	private $raw, $positive_chunks, $optional_chunks = array(), $forbidden_chunks = array(), $mandatory_chunks = array(), $section = NULL, $quoted = array();

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $raw Parameter description (if any) ...
	 * @return     void
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
		JFactory::getApplication()->triggerEvent('onSearchExpandTerms', array(&$chunks));

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
		if (preg_match('/^([_.a-z:]+):/', $raw, $match))
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

