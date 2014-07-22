<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Wiki macro class for listing files
 */
class FileIndex extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(\JURI::base(), '/');

		// What pages are we getting?
		if ($et)
		{
			$et = strip_tags($et);
			// Get pages with a prefix
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE LOWER(filename) LIKE " . $this->_db->quote(strtolower($et) . '%') . " AND pageid=" . $this->_db->quote($this->pageid) . " ORDER BY created ASC";
		}
		else
		{
			// Get all pages
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE pageid=" . $this->_db->quote($this->pageid) . " ORDER BY created ASC";
		}

		// Perform query
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		// Did we get a result from the database?
		if ($rows)
		{
			$config = JComponentHelper::getParams('com_wiki');
			if ($this->filepath != '')
			{
				$config->set('filepath', $this->filepath);
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$link = $live_site . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->pageid . DS . $row->filename;
				$fpath = JPATH_ROOT . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->pageid . DS . $row->filename;

				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="' . JRoute::_($link) . '">' . $row->filename . '</a> (' . (file_exists($fpath) ? \Hubzero\Utility\Number::formatBytes(filesize($fpath)) : '-- file not found --') . ') ';
				$huser = \JUser::getInstance($row->created_by);
				if ($huser->get('id'))
				{
					$html .= '- added by <a href="' . \JRoute::_('index.php?option=com_members&id=' . $huser->get('id')) . '">' . stripslashes($huser->get('name')) . '</a> ';
				}
				if ($row->created && $row->created != '0000-00-00 00:00:00')
				{
					$html .= \JHTML::_('date.relative', \JHTML::_('date', $row->created, \JFactory::getDBO()->getDateFormat())) . '. ';
				}
				$html .= ($row->description) ? '<span>"' . stripslashes($row->description) . '"</span>' : '';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}
		else
		{
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No ' . $et . ' files to display)';
		}
	}
}

