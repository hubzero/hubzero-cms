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
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(Request::base(), '/');

		// What pages are we getting?
		if ($et)
		{
			$et = strip_tags($et);
			// Get pages with a prefix
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE LOWER(filename) LIKE '" . strtolower($et) . "%' AND pageid='" . $this->pageid . "' ORDER BY created ASC";
		}
		else
		{
			// Get all pages
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE pageid='" . $this->pageid . "' ORDER BY created ASC";
		}

		// Perform query
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		// Did we get a result from the database?
		if ($rows)
		{
			$config = Component::params('com_wiki');
			if ($this->filepath != '')
			{
				$config->set('filepath', $this->filepath);
			}

			$page = new \Components\Wiki\Models\Page($this->pageid);
			if ($page->get('namespace') == 'help')
			{
				$page->set('scope', ($page->get('scope') ? rtrim($this->scope, '/') . '/' . ltrim($page->get('scope'), '/') : $this->scope));
				$page->set('group_cn', $this->domain);
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$page->set('pagename', $page->get('pagename') . '/' . 'File:' . $row->filename);
				$link  = $page->link(); //$live_site . substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->pageid . DS . $row->filename;
				$fpath = PATH_APP . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->pageid . DS . $row->filename;

				$html .= '<li><a href="' . Route::url($link) . '">' . $row->filename . '</a> (' . (file_exists($fpath) ? \Hubzero\Utility\Number::formatBytes(filesize($fpath)) : '-- file not found --') . ') ';
				$huser = User::getInstance($row->created_by);
				if ($huser->get('id'))
				{
					$html .= '- added by <a href="' . Route::url('index.php?option=com_members&id=' . $huser->get('id')) . '">' . stripslashes($huser->get('name')) . '</a> ';
				}
				if ($row->created && $row->created != '0000-00-00 00:00:00')
				{
					$html .= Date::of($row->created)->relative() . '. ';
				}
				$html .= ($row->description) ? '<span>"' . stripslashes($row->description) . '"</span>' : '';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}
		else
		{
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No ' . $et . ' files to display)';
		}
	}
}

