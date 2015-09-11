<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

// Output HTML
$html  = '<div class="' . $this->module->module . '"' . ($this->cssId ? ' id="' . $this->cssId . '"' : '') . '>' . "\n";

if ($this->feed)
{
	$html .= '<ul class="module-nav">';
	$html .= '<li><a class="newsfeed" href="' . $this->feedlink . '" title="' . Lang::txt('MOD_WHATSNEW_SUBSCRIBE') . '">' . Lang::txt('MOD_WHATSNEW_NEWS_FEED') . '</a></li>';
	$html .= '</ul>';
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
			$html .= '<a href="' . Route::url($row->href) . '">' . $this->escape(stripslashes($row->title)) . '</a><br />';
			$html .= '<span>' . Lang::txt('in') . ' ';
			$html .= ($row->area) ? Lang::txt(stripslashes($row->area)) : Lang::txt(strtoupper(stripslashes($row->section)));
			if ($row->publish_up)
			{
				$html .= ', ' . Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
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
		$html .= "\t" . '<p>' . Lang::txt('MOD_WHATSNEW_NO_RESULTS') . '</p>' . "\n";
	}
}
else
{
	$rows2 = $this->rows2;

	$html .= "\t" . '<p class="category-header-details">' . "\n";
	if (count($this->tags) > 0)
	{
		$html .= "\t\t" . '<span class="configure">[<a href="' . Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile#profile-interests') . '">' . Lang::txt('MOD_WHATSNEW_EDIT').'</a>]</span>' . "\n";
	}
	else
	{
		$html .= "\t\t" . '<span class="configure">[<a href="' . Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile#profile-interests') . '">' . Lang::txt('MOD_WHATSNEW_ADD_INTERESTS') . '</a>]</span>' . "\n";
	}
	$html .= "\t\t" . '<span class="q">' . Lang::txt('MOD_WHATSNEW_MY_INTERESTS') . ': ' . $this->formatTags($this->tags) . '</span>' . "\n";
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
			$html .= '<a href="' . Route::url($row2->href) . '">' . $this->escape(stripslashes($row2->title)) . '</a><br />';
			$html .= '<span>' . Lang::txt('MOD_WHATSNEW_IN') . ' ';
			$html .= ($row2->section) ? Lang::txt($row2->area) : Lang::txt(strtoupper($row2->section));
			if ($row2->publish_up)
			{
				$html .= ', ' . Date::of($row2->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
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
		$html .= "\t" . '<p>' . Lang::txt('MOD_WHATSNEW_NO_RESULTS') . '</p>' . "\n";
	}
}
$html .= "\t" . '<p class="more"><a href="' . Route::url('index.php?option=com_whatsnew&period=' . $this->area . ':' . $this->period) . '">' . ($this->area ? Lang::txt('MOD_WHATSNEW_VIEW_MORE_OF', $this->escape($this->area)) : Lang::txt('MOD_WHATSNEW_VIEW_MORE')) . '</a></p>' . "\n";
$html .= '</div>' . "\n";

echo $html;
