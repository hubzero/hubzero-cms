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

	if (!$this->tags->total())
	{
		echo '';
		return;
	}

	$min_font_size = 1;
	$max_font_size = 1.8;

	if ($this->config->get('show_sizes', 0) == 1) 
	{
		$retarr = array();
		foreach ($tags as $tag)
		{
			$retarr[$tag->raw_tag] = $tag->count;
		}
		ksort($retarr);

		$max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
		$min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set

		// For ever additional tagged object from min to max, we add $step to the font size.
		$spread = $max_qty - $min_qty;
		if (0 == $spread) 
		{ // Divide by zero
			$spread = 1;
		}
		$step = ($max_font_size - $min_font_size)/($spread);
	}

	// build HTML
	$tll = array();
	foreach ($this->tags as $tag)
	{
		$class = '';
		switch ($tag->get('admin'))
		{
			case 1:
				$class = ' class="admin"';
			break;
		}

		if ($this->config->get('show_sizes', 0) == 2) 
		{
			$tll[$tag->get('tag')] = '<li' . $class . '><a href="javascript:void(0);" onclick="addtag(\'' . $this->escape($tag->get('tag')) . '\');">' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' <span>' . $tag->get('count') . '</span></a></li>';
		} 
		else 
		{
			$tll[$tag->get('tag')]  = '<li' . $class . '>';
			if ($this->config->get('show_sizes', 0) == 1) 
			{
				$size = $min_font_size + ($tag->get('count') - $min_qty) * $step;

				$tll[$tag->get('tag')] .= '<span style="font-size: ' . round($size, 1) . 'em;">';
			}
			$tll[$tag->get('tag')] .= '<a href="' . JRoute::_('index.php?option=com_tags&tag=' . $tag->get('tag')) . '">' . $this->escape(stripslashes($tag->get('raw_tag')));
			if ($this->config->get('show_tag_count', 0))
			{
				$tll[$tag->get('tag')] .= ' <span>' . $tag->get('count') . '</span>';
			}
			$tll[$tag->get('tag')] .= '</a>';
			if ($this->config->get('show_sizes') == 1) 
			{
				$tll[$tag->get('tag')] .= '</span>';
			}
			$tll[$tag->get('tag')] .= '</li>';
		}
	}
	if ($this->config->get('show_tags_sort', 'alpha') == 'alpha') 
	{
		ksort($tll);
	}

	$html  = '<ol class="tags">' . "\n";
	$html .= implode("\n", $tll);
	$html .= '</ol>' . "\n";

	echo $html;
