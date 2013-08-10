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

$dateFormat = ' %b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = ' M d, Y';
	$tz = false;
}

// Output HTML
$html  = '<div';
$html .= ($this->cssId) ? ' id="' . $this->cssId . '"' : '';
$html .= '>' . "\n";

if ($this->feed)
{
	$html .= "\t" . '<h3>' . $this->module->title;
	$html .= ' <a class="newsfeed" href="' . $this->feedlink . '" title="' . JText::_('MOD_WHATSNEW_SUBSCRIBE') . '">' . JText::_('MOD_WHATSNEW_NEWS_FEED') . '</a>';
	$html .= '</h3>' . "\n";
}

if (!$this->tagged)
{
	$rows = $this->rows;
	if (count($rows) > 0)
	{
		$count = 0;

		$html .= "\t" . '<ul>' . "\n";
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			$html .= "\t\t" . '<li class="new">';
			$html .= '<a href="' . JRoute::_($row->href) . '">' . stripslashes($row->title) . '</a><br />';
			$html .= '<span>' . JText::_('in') . ' ';
			$html .= ($row->area) ? JText::_(stripslashes($row->area)) : JText::_(strtoupper(stripslashes($row->section)));
			if ($row->publish_up)
			{
				$html .= ', ' . JHTML::_('date', $row->publish_up, $dateFormat, $tz);
			}
			$html .= '</span></li>' . "\n";

			$count++;
			if ($count >= 6)
			{
				break;
			}
		}
		$html .= "\t" . '</ul>' . "\n";
	}
	else
	{
		$html .= "\t" . '<p>' . JText::_('MOD_WHATSNEW_NO_RESULTS') . '</p>' . "\n";
	}
}
else
{
	$juser =& JFactory::getUser();
	$rows2 = $this->rows2;

	$html .= "\t" . '<p class="category-header-details">' . "\n";
	if (count($this->tags) > 0)
	{
		$html .= "\t\t" . '<span class="configure">[<a href="' . JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile#profile-interests') . '">' . JText::_('MOD_WHATSNEW_EDIT').'</a>]</span>' . "\n";
	}
	else
	{
		$html .= "\t\t" . '<span class="configure">[<a href="' . JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile#profile-interests') . '">' . JText::_('MOD_WHATSNEW_ADD_INTERESTS').'</a>]</span>' . "\n";
	}
	$html .= "\t\t" . '<span class="q">'.JText::_('MOD_WHATSNEW_MY_INTERESTS') . ': ' . $this->formatTags($this->tags) . '</span>' . "\n";
	$html .= "\t" . '</p>' . "\n";
	if (count($rows2) > 0)
	{
		$count = 0;

		$html .= "\t".'<ul class="expandedlist">'."\n";
		foreach ($rows2 as $row2)
		{
			if (empty($row2))
			{
				continue;
			}
			$html .= "\t" . ' <li class="new">';
			$html .= '<a href="' . JRoute::_($row2->href) . '">' . stripslashes($row2->title) . '</a><br />';
			$html .= '<span>' . JText::_('MOD_WHATSNEW_IN') . ' ';
			$html .= ($row2->section) ? JText::_($row2->area) : JText::_(strtoupper($row2->section));
			if ($row2->publish_up)
			{
				$html .= ', ' . JHTML::_('date', $row2->publish_up, $dateFormat, $tz);
			}
			$html .= '</span></li>' . "\n";

			$count++;
			if ($count >= 6)
			{
				break;
			}
		}
		$html .= "\t" . '</ul>' . "\n";
	}
	else
	{
		$html .= "\t" . '<p>' . JText::_('MOD_WHATSNEW_NO_RESULTS') . '</p>' . "\n";
	}
}
$html .= "\t" . '<p class="more"><a href="'.JRoute::_('index.php?option=com_whatsnew&period=' . $this->area . ':' . $this->period) . '">' . ($this->area ? JText::sprintf('MOD_WHATSNEW_VIEW_MORE_OF', $this->area) : JText::_('MOD_WHATSNEW_VIEW_MORE')) . '</a></p>' . "\n";
$html .= '</div>' . "\n";

echo $html;
