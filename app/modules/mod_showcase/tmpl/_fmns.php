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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

foreach ($item_fmns as $fmn)
{
	// Get group associated with FMN for logo
	$group = Hubzero\User\Group::getInstance($fmn->group_cn);
	$path = PATH_APP . '/site/groups/' . $group->get('gidNumber') . '/uploads/' . $group->get('logo');
	$logo = ($group->get('logo') ? with(new Hubzero\Content\Moderator($path))->getUrl() : '');
	
	// Get status of fmn and set as class for element
	// Also set tag if autotagging and not overriden by user
	$tag = $item["tag"];
	$set_tag = (!$tag) && ($this->autotag);
	if ($this->_isFuture($fmn->start_date)) {
		$cls = ' upcoming';
		$tag = ($set_tag ? 'Upcoming FMN' : $tag);
		if ($fmn->reg_status) {
			$cls .= ' open';
			$tag = ($set_tag ? $tag . ' - Open for applications!' : $tag);
		}
	} elseif ($this->_isPast($fmn->start_date) &&
						$this->_isFuture($fmn->stop_date)) {
			$cls = ' current';
			$tag = ($set_tag ? 'Current FMN' : $tag);
	} else {
		$cls = '';
	}
	
	echo '<div class="' . $item['class'] . ' fmn' . $cls . '">
';
  echo '  <a href="' . Route::url('groups' . DS . $fmn->group_cn) . '">';
  echo '    <div class="fmn-img">';
	if ($logo) {
		echo '      <img src="' . $logo . '" alt="' . $fmn->name . '" />';
	}
	echo '    </div>';
	echo '  </a>';
	
	if ($tag) {
		if ($item['tag-target'])
		{
			echo '  <a href="' . $item['tag-target'] . '">';
		}
		echo '    <div class="fmn-tag">';
		echo '      <span>' . $tag . '</span>';
		echo '    </div>';
		if ($item['tag-target'])
		{
			echo '  </a>';
		}
	}
	
	echo '  <div class="fmn-title">';
	echo '    <a href="' . Route::url('groups' . DS . $fmn->group_cn) . '">';
	echo '      <span>' . $fmn->name . '</span>';
	echo '    </a>';
	echo '  </div>';
	echo '</div>';
}
?>
