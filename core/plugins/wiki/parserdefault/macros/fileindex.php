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
 * Wiki macro class for listing files
 */
class FileIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(Request::base(), '/');

		// Get resource by ID
		$attach = \Components\Wiki\Models\Attachment::all()
			->whereEquals('page_id', $this->pageid);

		if ($et)
		{
			$et = strip_tags($et);

			$attach->whereLike('filename', strtolower($et) . '%');
		}

		$rows = $attach->rows();

		// Did we get a result from the database?
		if ($rows)
		{
			$config = Component::params('com_wiki');
			if ($this->filepath != '')
			{
				$config->set('filepath', $this->filepath);
			}

			$page = \Components\Wiki\Models\Page::oneOrFail($this->pageid);

			if ($page->get('namespace') == 'help')
			{
				$page->set('path', ($page->get('path') ? rtrim($this->scope, '/') . '/' . ltrim($page->get('path'), '/') : $this->scope));
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$page->set('pagename', $page->get('pagename') . '/' . 'File:' . $row->get('filename'));

				$link  = $page->link();
				$fpath = $row->filespace() . DS . $this->pageid . DS . $row->get('filename');

				$html .= '<li><a href="' . Route::url($link) . '">' . $row->get('filename') . '</a> (' . (file_exists($fpath) ? \Hubzero\Utility\Number::formatBytes(filesize($fpath)) : '-- file not found --') . ') ';
				$huser = $row->creator();
				if ($huser->get('id'))
				{
					$html .= '- added by <a href="' . Route::url('index.php?option=com_members&id=' . $huser->get('id')) . '">' . stripslashes($huser->get('name')) . '</a> ';
				}
				if ($row->get('created') && $row->get('created') != '0000-00-00 00:00:00')
				{
					$html .= Date::of($row->get('created'))->relative() . '. ';
				}
				$html .= $row->get('description') ? '<span>"' . stripslashes($row->get('description')) . '"</span>' : '';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}

		// Return error message
		return '(No ' . $et . ' files to display)';
	}
}
