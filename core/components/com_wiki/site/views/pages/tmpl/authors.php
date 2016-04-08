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

// No direct access.
defined('_HZEXEC_') or die();

if ($this->page->param('mode', 'wiki') == 'knol'
 && !$this->page->param('hide_authors', 0))
{
	$author = $this->escape(stripslashes($this->page->creator()->get('name', Lang::txt('COM_WIKI_UNKNOWN'))));

	$auths = array();
	$auths[] = ($this->page->creator()->get('public') ? '<a href="' . Route::url($this->page->creator()->getLink()) . '">' . $author . '</a>' : $author);

	foreach ($this->page->authors()->rows() as $auth)
	{
		if ($auth->get('user_id') == $this->page->get('created_by'))
		{
			continue;
		}

		$name = $this->escape(stripslashes($auth->get('name')));
		$name = ($auth->get('public') ? '<a href="' . Route::url($auth->getLink()) . '">' . $name . '</a>' : $name);

		$auths[] = $name;
	}
	?>
	<p class="topic-authors"><?php echo Lang::txt('COM_WIKI_BY_AUTHORS', implode(', ', $auths)); ?></p>
	<?php
}
