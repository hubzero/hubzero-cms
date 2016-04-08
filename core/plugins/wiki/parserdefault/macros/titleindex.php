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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a list of pages
 */
class TitleIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.</p><p>The list may have a sorting applied by adding the sort=[title,created(oldest to newest),modified(newest to oldest)] argument. For example, <code>[[TitleIndex(sort=modified)]]</code> will list all pages by their last modified date (most recent to oldest). If you have a page prefix, simply add a comma and the sort parameter <em>after</em>. For example: <code>[[TitleIndex(Help, sort=modified)]]</code></p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		$sort = '';
		if ($et)
		{
			$et = strip_tags($et);

			if (strstr($et, ','))
			{
				$attribs = explode(',', $et);
				$et = trim($attribs[0]);
				$sort = strtolower(trim($attribs[1]));
			}

			if (strtolower($et) == 'sort=modified'
			 || strtolower($et) == 'sort=created'
			 || strtolower($et) == 'sort=title')
			{
				$sort = $et;
				$et = '';
			}
		}

		$pages = \Components\Models\Wiki\Page::all()
			->whereEquals('state', 1);

		if ($et)
		{
			$pages->whereLike('pagename', strtolower($et) . '%');
		}

		if ($this->domain && substr(strtolower($et), 0, 4) != 'help')
		{
			$pages->whereEquals('scope', $this->domain);
			$pages->whereEquals('scope_id', $this->domain_id);
		}

		switch ($sort)
		{
			case 'sort=created':
				$pages->order('created', 'asc');
			break;
			case 'sort=modified':
				$pages->order('modified', 'asc');
			break;
			case 'sort=title':
			default:
				$pages->order('title', 'asc');
			break;
		}

		$rows = $pages->rows();

		// Did we get a result from the database?
		if ($rows)
		{
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				if ($row->get('pagename') == $this->get('pagename'))
				{
					continue;
				}

				if ($row->get('namespace') == 'Help')
				{
					$row->set('path', ($row->get('path') ? rtrim($this->scope, '/') . '/' . ltrim($row->get('path'), '/') : $this->scope));
					$row->set('scope', $this->domain);
					$row->set('scope_id', $this->domain_id);
				}

				$html .= '<li><a href="' . Route::url($row->link()) . '">';
				$html .= stripslashes($row->get('title', $row->get('pagename')));
				$html .= '</a></li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}

		// Return error message
		return '(No ' . $et . ' pages to display)';
	}
}
