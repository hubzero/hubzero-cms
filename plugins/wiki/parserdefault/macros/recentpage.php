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
 * Wiki macro class for displaying a link to a recently created or updated page page.
 */
class RecentPageMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();

		$txt['wiki'] = JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE') . "\n\n" .
						JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_ARGUMENTS') . "\n\n" .
						' * ' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_LIMIT') . "\n" .
						' * ' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_CLASS') . "\n";

		$txt['html'] = '
			<p>' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE') . '</p>
			<p>' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_ARGUMENTS') . '</p>
			<ul>
				<li>' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_LIMIT') . '</li>
				<li>' . JText::_('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_CLASS') . '</li>
			</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$limit = 1;
		$cls = '';
		$limitstart = 0;

		if ($this->args)
		{
			$args = explode(',', $this->args);
			if (isset($args[0]))
			{
				$args[0] = intval($args[0]);
				if ($args[0])
				{
					$limit = $args[0];
				}
			}
			if (isset($args[1]))
			{
				$cls = $args[1];
			}
			if (isset($args[2]))
			{
				$args[2] = intval($args[2]);
				if ($args[2])
				{
					$limitstart = $args[2];
				}
			}
		}

		$query = "SELECT wv.pageid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created, wv.pagehtml
					FROM `#__wiki_version` AS wv
					INNER JOIN `#__wiki_page` AS wp
						ON wp.id = wv.pageid
					WHERE wv.approved = 1
						AND wp.group_cn = '$this->domain'
						AND wp.scope = '$this->scope'
						AND wp.access != 1
						AND wp.state < 2
						AND wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_version` AS wv2 WHERE wv2.pageid = wv.pageid)
					ORDER BY created DESC
					LIMIT $limitstart, $limit";

		// Perform query
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		$html = '';

		// Did we get a result from the database?
		if ($rows)
		{
			foreach ($rows as $row)
			{
				$html .= '<div';
				if ($cls)
				{
					$html .= ' class="' . $cls . '"';
				}
				$html .= '>' . "\n";
				$html .= "\t" . '<h3><a href="' . JRoute::_('index.php?option=' . $this->option . '&pagename=' . $row->pagename . '&scope=' . $row->scope) . '">' . stripslashes($row->title) . '</a></h3>' . "\n";
				$html .= "\t" . '<p class="modified-date">';
				if ($row->version > 1)
				{
					$html .= JText::sprintf('PLG_WIKI_PARSERDEFAULT_MODIFIED_ON', JHTML::_('date', $row->created, JText::_('DATE_FORMAT_HZ1')));
				}
				else
				{
					$html .= JText::sprintf('PLG_WIKI_PARSERDEFAULT_CREATED_ON', JHTML::_('date', $row->created, JText::_('DATE_FORMAT_HZ1')));
				}
				$html .= '</p>' . "\n";
				$html .= $this->_shortenText($row->pagehtml);
				$html .= "\t" . '<p><a href="' . JRoute::_('index.php?option=' . $this->option . '&pagename=' . $row->pagename . '&scope=' . $row->scope) . '">' . JText::_('PLG_WIKI_PARSERDEFAULT_READ_MORE') . '</a></p>' . "\n";
				$html .= '</div>' . "\n";
			}

		}
		else
		{
			$html .= '<p class="warning">' . JText::_('PLG_WIKI_PARSERDEFAULT_NO_RESULTS') . '</p>' . "\n";
		}

		return $html;
	}

	/**
	 * Shorten a string to a max length, preserving whole words
	 *
	 * @param      string  $text      String to shorten
	 * @param      integer $chars     Max length to allow
	 * @param      integer $p         Wrap content in a paragraph tag?
	 * @return     string
	 */
	private function _shortenText($text, $chars=300, $p=1)
	{
		$text = strip_tags($text);
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		$text = str_replace("\t", ' ', $text);
		$text = str_replace('   ', ' ', $text);
		$text = trim($text);

		if (strlen($text) > $chars)
		{
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
			$text = substr($text, 0, strrpos($text, ' '));
			$text = $text . ' &#8230;';
		}

		if ($text == '')
		{
			$text = '&#8230;';
		}

		if ($p)
		{
			$text = '<p>' . $text . '</p>';
		}

		return $text;
	}
}

