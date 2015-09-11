<?php
/**
 * HUBzero CMS
 *
 * Copyright 2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SEARCH') . ': ' . Lang::txt('COM_SEARCH_SITEMAP'), 'search.png');
Toolbar::preferences('com_search', '550');
Toolbar::spacer();
Toolbar::help('search');

Html::behavior('framework');

$context = array();
if (array_key_exists('search-task', $_POST))
{
	foreach (Event::trigger('search.onSearchTask' . $_POST['search-task']) as $resp)
	{
		list($name, $html, $ctx) = $resp;
		echo $html;
		if (array_key_exists($name, $context))
		{
			$context[$name] = array_merge($context[$name], $ctx);
		}
		else
		{
			$context[$name] = $ctx;
		}
	}
}

foreach (Event::trigger('search.onSearchAdministrate', array($context)) as $plugin)
{
	list($name, $html) = $plugin;
	//echo '<h3>' . $name . '</h3>';
	echo $html;
}