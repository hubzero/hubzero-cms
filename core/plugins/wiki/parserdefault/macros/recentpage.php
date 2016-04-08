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
 * Wiki macro class for displaying a link to a recently created or updated page page.
 */
class RecentPageMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();

		$txt['wiki'] = Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE') . "\n\n" .
						Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_ARGUMENTS') . "\n\n" .
						' * ' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_LIMIT') . "\n" .
						' * ' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_CLASS') . "\n";

		$txt['html'] = '
			<p>' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE') . '</p>
			<p>' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_ARGUMENTS') . '</p>
			<ul>
				<li>' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_LIMIT') . '</li>
				<li>' . Lang::txt('PLG_WIKI_PARSERDEFAULT_MACRO_RECENT_PAGE_CLASS') . '</li>
			</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$limit = 1;
		$cls = '';
		$limitstart = 0;

		if ($this->args)
		{
			$args = explode(',', $this->args);
			if (isset($args[0]))
			{
				$args[0] = intval($args[0]);
				if ($args[0])
				{
					$limit = $args[0];
				}
			}
			if (isset($args[1]))
			{
				$cls = $args[1];
			}
			if (isset($args[2]))
			{
				$args[2] = intval($args[2]);
				if ($args[2])
				{
					$limitstart = $args[2];
				}
			}
		}

		$pages = \Components\Wiki\Models\Page::all()
			->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
			->order('modified', 'desc')
			->limit($limit)
			->start($limitstart);

		if ($this->domain)
		{
			$pages->whereEquals('scope', $this->domain);
			$pages->whereEquals('scope_id', $this->domain_id);
		}

		$rows = $pages->rows();

		$html = '';

		// Did we get a result from the database?
		if ($rows)
		{
			foreach ($rows as $row)
			{
				$txt = strip_tags($row->version->get('pagehtml'));
				$txt = str_replace(array("\n", "\r", "\t", '   '), ' ', $txt);
				$txt = trim($txt);

				$html .= '<div';
				if ($cls)
				{
					$html .= ' class="' . $cls . '"';
				}
				$html .= '>' . "\n";
				$html .= "\t" . '<h3><a href="' . Route::url($row->link()) . '">' . stripslashes($row->title) . '</a></h3>' . "\n";
				$html .= "\t" . '<p class="modified-date">';
				if ($row->get('version') > 1)
				{
					$html .= Lang::txt('PLG_WIKI_PARSERDEFAULT_MODIFIED_ON', Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')));
				}
				else
				{
					$html .= Lang::txt('PLG_WIKI_PARSERDEFAULT_CREATED_ON', Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')));
				}
				$html .= '</p>' . "\n";
				$html .= '<p>' . \Hubzero\Utility\String::truncate($txt, 300) . '</p>' . "\n";
				$html .= "\t" . '<p><a href="' . Route::url($row->link()) . '">' . Lang::txt('PLG_WIKI_PARSERDEFAULT_READ_MORE') . '</a></p>' . "\n";
				$html .= '</div>' . "\n";
			}

		}
		else
		{
			$html .= '<p class="warning">' . Lang::txt('PLG_WIKI_PARSERDEFAULT_NO_RESULTS') . '</p>' . "\n";
		}

		return $html;
	}
}
