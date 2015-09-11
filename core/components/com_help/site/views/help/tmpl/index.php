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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//var to hold content
$content = '';

//loop through each component and pages group passed in
foreach ($this->pages as $component)
{
	//build content to return
	$content .= '<h2>' . Lang::txt('COM_HELP_COMPONENT_HELP', $component['name']) . '</h2>';

	//make sure we have pages
	if (count($component['pages']) > 0)
	{
		$content .= '<p>' . Lang::txt('COM_HELP_PAGE_INDEX_EXPLANATION', $component['name']) . '</p>';
		$content .= '<ul>';
		foreach ($component['pages'] as $page)
		{
			$name = str_replace('.' . $this->layoutExt, '', $page);

			$content .= '<li><a href="' . Route::url('index.php?option=com_help&component=' . str_replace('com_', '', $component['option']) . '&page=' . $name) . '">' . ucwords(str_replace('_', ' ', $name)) .'</a></li>';
		}
		$content .= '</ul>';
	}
	else
	{
		$content .= '<p>' . Lang::txt('COM_HELP_NO_PAGES_FOUND') . '</p>';
	}
}

echo $content;