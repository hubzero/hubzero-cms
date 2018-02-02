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

$groups = $this->_getGroups($item["featured"]);

// Make sure we don't ask for too much
$n = min($item["n"], count($groups));
if ($n < $item["n"]) {
	echo 'Showcase Module Error: Not enough selected groups left!';
}

$i = 0;
foreach ($groups as $grp)
{
	$group = Hubzero\User\Group::getInstance($grp->gidNumber);
	if ($i++ < $n) {
		echo '<div class="' . $item['class'] . ' group' . ($item["featured"] ? ' featured' : '') . '">
';
		$path = PATH_APP . '/site/groups/' . $group->get('gidNumber') . '/uploads/' . $group->get('logo');

		if ($group->get('logo') && is_file($path)) {
			echo '  <div class="group-img">';
			echo '    <a href="' . Route::url('index.php?option=com_groups&cn='. $group->get('cn')) . '">';
			echo '      <img src="' . with(new Hubzero\Content\Moderator($path))->getUrl() . '" alt="' . $this->escape(stripslashes($group->get('description'))) . '" />';
			echo '    </a>';
			echo '  </div>';
		} else {
			echo '  <div class="group-description">';
			echo '    <a href="' . Route::url('index.php?option=com_groups&cn='. $group->get('cn')) . '">';
			echo '      <span>' . $this->escape(stripslashes($group->get('description'))) . '</span>';
			echo '    </a>';
			echo '  </div>';
		}

		// echo '    <a href="' . echo Route::url('index.php?option=' . $this->option . '&cn='. $group->get('cn')) . '">';
		// echo '    </a>';
		echo '</div>';
	} else {
		break;
	}
}
?>