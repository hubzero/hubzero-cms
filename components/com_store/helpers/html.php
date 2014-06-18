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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for misc functions
 */
class StoreHtml
{
	/**
	 * Get a default image for the store item
	 *
	 * @param      string $option   Component name
	 * @param      string $item     Item ID
	 * @param      string $root     Root path
	 * @param      string $wpath    Base path for files
	 * @param      string $alt      Image alt text
	 * @param      string $category Item category
	 * @return     string HTML
	 */
	public static function productimage($option, $item, $root, $wpath, $alt, $category)
	{
		if ($wpath)
		{
			$wpath = DS . trim($wpath, DS) . DS;
		}

		$d = @dir($root . $wpath . $item);

		$images = array();
		$html = '';

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($root . $wpath . $item . DS . $img_file) && substr($entry, 0, 1) != '.' && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png|swf#i", $img_file))
					{
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}
		else
		{
			if ($category == 'service')
			{
				$html = '<img src="/components/' . $option . '/assets/img/premiumservice.gif" alt="' . JText::_('COM_STORE_PREMIUM_SERVICE') . '" />';
			}
			else
			{
				$html = '<img src="/components/' . $option . '/assets/img/nophoto.gif" alt="' . JText::_('COM_STORE_MSG_NO_PHOTO') . '" />';
			}
		}

		sort($images);
		$els = '';
		$k = 0;
		$g = 0;

		for ($i=0, $n=count($images); $i < $n; $i++)
		{
			jimport('joomla.filesystem.file');

			$ext = JFile::getExt($images[$i]);
			$tn  = JFile::stripExt($images[$i]) . '-tn.';

			if (!is_file($root . $wpath . $item . DS . $tn . $ext))
			{
				$ext = 'gif';
			}

			$tn = $tn . $ext;

			if (is_file($root . $wpath . $item . DS . $tn))
			{
				$k++;
				$els .= '<a rel="lightbox" href="' . $wpath . $item . '/' . $images[$i] . '" title="' . $alt . '"><img src="' . $wpath . $item . '/' . $tn . '" alt="' . $alt . '" /></a>';
			}
		}

		if ($els)
		{
			$html .= $els;
		}
		return $html;
	}
}

