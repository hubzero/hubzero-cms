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

$exclude = explode(',', $this->params->get('exclude', ''));
$exclude = array_map('trim', $exclude);

$tl = array();
if ($this->tags->count() > 0)
{
	$html  = '<ol class="tags">' . "\n";
	foreach ($this->tags as $tag)
	{
		if (!in_array($tag->get('raw_tag'), $exclude))
		{
			$tl[$tag->get('tag')] = "\t" . '<li><a class="tag" href="' . Route::url('index.php?option=com_tags&tag=' . $this->escape($tag->get('tag'))) . '">' . $this->escape($tag->get('raw_tag')) . '</a></li>';
		}
	}
	if ($this->params->get('sortby') == 'alphabeta')
	{
		ksort($tl);
	}
	$html .= implode("\n", $tl);
	$html .= '</ol>' . "\n";
	if ($this->params->get('morelnk'))
	{
		$html .= '<p class="more"><a href="' . Route::url('index.php?option=com_tags') . '">' . Lang::txt('MOD_TOPTAGS_MORE') . '</a></p>' . "\n";
	}
}
else
{
	$html  = '<p>' . $this->params->get('message', 'No tags found.') . '</p>' . "\n";
}
echo $html;