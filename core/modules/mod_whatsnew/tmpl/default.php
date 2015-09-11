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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
