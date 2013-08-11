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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki helper class for misc. HTML
 */
class WikiHtml
{
	/**
	 * Generate a tag cloud for a wiki PAge
	 * 
	 * @param      array $tags Tags to build cloud from
	 * @return     string HTML
	 */
	public function tagCloud($tags)
	{
		if (count($tags) > 0) 
		{
			$tagarray = array();
			$tagarray[] = '<ol class="tags">';
			foreach ($tags as $tag)
			{
				$class = '';
				if (isset($tag['admin']) && $tag['admin'] == 1) 
				{
					$class = ' class="admin"';
				}
				$tag['raw_tag'] = str_replace('&amp;', '&', $tag['raw_tag']);
				$tag['raw_tag'] = str_replace('&', '&amp;', $tag['raw_tag']);
				$tagarray[] = "\t" . '<li' . $class . '><a href="' . JRoute::_('index.php?option=com_tags&tag=' . $tag['tag']) . '" rel="tag">' . $tag['raw_tag'] . '</a></li>';
			}
			$tagarray[] = '</ol>';

			$html = implode("\n", $tagarray);
		} 
		else 
		{
			$html = '<p>'.JText::_('COM_WIKI_PAGE_HAS_NO_TAGS').'</p>';
		}
		return $html;
	}

	/**
	 * Display page ranking
	 * 
	 * @param      string $stats  Extra stats to append
	 * @param      object $page   Wiki page
	 * @param      string $option Component name
	 * @return     string HTML
	 */
	public function ranking($stats, $page, $option)
	{
		$r = (10*$page->ranking);
		if (intval($r) < 10) 
		{
			$r = '0' . $r;
		}

		$html  = '<dl class="rankinfo">' . "\n";
		$html .= "\t" . '<dt class="ranking"><span class="rank-' . $r . '">This page has a</span> ' . number_format($page->ranking, 1) . ' Ranking</dt>' . "\n";
		$html .= "\t" . '<dd>' . "\n";
		$html .= "\t\t" . '<p>Ranking is calculated from a formula comprised of <a href="' . JRoute::_('index.php?option=' . $option . '&scope=' . $page->scope . '&pagename=' . $page->pagename . '&task=comments') . '">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a></p>' . "\n";
		$html .= "\t\t" . '<div>' . "\n";
		$html .= $stats;
		$html .= "\t\t" . '</div>' . "\n";
		$html .= "\t" . '</dd>' . "\n";
		$html .= '</dl>' . "\n";
		return $html;
	}

	/**
	 * Generate a linked list of page contributors
	 * 
	 * @param      object $page         WikiPage
	 * @param      object $params       Wiki parameters
	 * @param      array  $contributors List of page authors
	 * @return     string HTML
	 */
	public function authors($page, $params, $contributors=array())
	{
		$html = '';
		if ($params->get('mode') == 'knol' && !$params->get('hide_authors', 0)) 
		{
			$authors = $page->getAuthors();

			$author = 'Unknown';
			$ausername = '';
			$auser =& JUser::getInstance($page->created_by);
			if (is_object($auser)) 
			{
				$author = $auser->get('name');
				$ausername = $auser->get('username');
			}

			$auths = array();
			$auths[] = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $page->created_by) . '">' . $author . '</a>';
			foreach ($authors as $auth)
			{
				$auths[] = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $auth->user_id) . '">' . stripslashes($auth->name) . '</a>';
			}
			$auths = implode(', ', $auths);
			$html .= '<p class="topic-authors">' . JText::_('by') . ' ' . $auths . '</p>' . "\n";

			if (count($contributors) > 0) 
			{
				$cons = array();
				foreach ($contributors as $contributor)
				{
					if ($contributor != $page->created_by) 
					{
						$zuser =& JUser::getInstance($contributor);
						if (is_object($zuser)) 
						{
							if (!in_array($zuser->get('username'), $authors)) 
							{
								$cons[] = '<a href="' . JRoute::_('index.php?option=com_contributors&id=' . $contributor) . '">' . $zuser->get('name') . '</a>';
							}
						}
					}
				}
				$cons = implode(', ', $cons);
				$html .= ($cons) ? '<p class="topic-contributors">' . JText::_('COM_WIKI_PAGE_CONTRIBUTIONS_BY') . ' ' . $cons . '</p>' . "\n" : '';
			}
		}
		return $html;
	}

	/**
	 * Format an ID by prefixing 0s.
	 * This is used for directory naming
	 * 
	 * @param      integer $someid ID to format
	 * @return     string
	 */
	public function niceidformat($someid)
	{
		ximport('Hubzero_View_Helper_Html');
		return Hubzero_View_Helper_Html::niceidformat($someid);
	}

	/**
	 * Encode some basic characters
	 * 
	 * @param      string  $str    Text to convert
	 * @param      integer $quotes Include quotes?
	 * @return     string
	 */
	public function encode_html($str, $quotes=1)
	{
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}
}

