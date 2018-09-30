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

foreach ($item_newsletters as $newsletter)
{
	// https://stackoverflow.com/a/21947465
	$content = ($newsletter->template_id === '-1' ? $newsletter->html_content : $newsletter->primary()->rows()->first()->story);
	preg_match("/\<img.+src\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>/", $content, $matches);
	if (count($matches) > 1) {
		$img_link = $matches[1];
	} else {
		$img_link = '';
	}
	
	echo '<div class="' . $item['class'] . ' newsletter' . ($item["featured"] ? ' featured' : '') . '">
';
  echo '  <a href="' . Route::url('index.php?option=com_newsletter&id=' . $newsletter->id) . '">';
  echo '    <div class="newsletter-img">';
	echo '      <img src="' . $img_link . '" alt="">';
	echo '    </div>';
	echo '  </a>';
	if ($item['tag']) {
		if ($item['tag-target'])
		{
			echo '  <a href="' . $item['tag-target'] . '">';
		}
		echo '    <div class="newsletter-tag">';
		echo '      <span>' . $item['tag'] . '</span>';
		echo '    </div>';
		if ($item['tag-target'])
		{
			echo '  </a>';
		}
	}
	echo '  <div class="newsletter-title">';
	echo '    <a href="' . Route::url('index.php?option=com_newsletter&id=' . $newsletter->id) . '">';
	echo '      <span>' . $newsletter->name . '</span>';
	echo '    </a>';
	echo '  </div>';
	echo '</div>';
}
?>
