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

$pubs = $this->_getPublications($item["featured"]);

// Make sure we don't ask for too much
$n = min($item["n"], count($pubs));
if ($n < $item["n"]) {
	echo 'Showcase Module Error: Not enough requested publications!';
}

$i = 0;
foreach ($pubs as $pub)
{
	if ($i++ < $n) {
		echo '<div class="' . $item['class'] . ' resource' . ($item["featured"] ? ' featured' : '') . '">
';
		echo '  <div class="resource-img">';
		echo '    <a href="' . $pub->link() . '">';
		echo '      <img src="' . Route::url($pub->link('masterimage')) . '" alt="">';
		echo '    </a>';
		echo '  </div>';
		echo '  <div class="resource-title">';
		echo '    <a href="' . $pub->link() . '">';
		echo '      <span>' . $pub->get('title') . '</span>';
		echo '    </a>';
		echo '  </div>';
		echo '</div>';
	} else {
		break;
	}
}
?>