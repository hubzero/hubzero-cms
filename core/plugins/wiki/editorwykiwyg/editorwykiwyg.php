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
 * Wiki plugin class for displaying a WYSIWYG wiki editor
 */
class plgWikiEditorwykiwyg extends \Hubzero\Plugin\Plugin
{
	/**
	 * Flag for if scripts need to be pushed to the document or not
	 *
	 * @var  boolean
	 */
	private static $pushscripts = true;

	/**
	 * Initiate the editor. Push scripts to document if needed
	 *
	 * @return  string
	 */
	public function onInitEditor()
	{
		if (self::$pushscripts)
		{
			$this->css();
			$this->js();

			self::$pushscripts = false;
		}

		return '';
	}

	/**
	 * Display a wiki editor
	 *
	 * @param   string   $name     Name of field
	 * @param   string   $id       ID for field
	 * @param   string   $content  Field content
	 * @param   string   $cls      Field class
	 * @param   integer  $col      Number of columns
	 * @param   integer  $row      Number of rows
	 * @return  string   HTML
	 */
	public function onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)
	{
		$content = preg_replace('/^((?:<|&lt;)!-- \{FORMAT:(?:.*)\} --(?:>|&gt;))/i', '', $content);

		$cls = ($cls) ? 'wiki-toolbar-content ' . $cls : 'wiki-toolbar-content';
		$editor = '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $content . '</textarea>' . "\n";

		return $editor;
	}
}
