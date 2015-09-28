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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use App;

/**
 * Helper for making easy links and getting urls that depend on the routes and router.
 */
class Editor extends AbstractHelper
{
	/**
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $content  The contents of the text area.
	 * @param   integer  $col      The number of columns for the textarea.
	 * @param   integer  $row      The number of rows for the textarea.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   array    $params   Associative array of editor parameters.
	 * @return  string
	 */
	public function __invoke($name='', $content='', $col=35, $row=15, $id=null, $params=array())
	{
		if (!count(func_get_args()))
		{
			return $this->instance();
		}

		$width   = '';
		$height  = '';
		$buttons = true;
		$asset   = null;
		$author  = null;

		if (!App::isAdmin())
		{
			$buttons = false;
		}
		else
		{
			if (isset($params['buttons']))
			{
				$buttons = $params['buttons'];
				unset($params['buttons']);
			}
		}

		if (!$name)
		{
			App::abort(500, \Lang::txt('Editor must have a name'));
		}

		$id = $id ?: str_replace(array('[', ']'), '', $name);

		return $this->instance()->display($name, $content, $width, $height, intval($col), intval($row), $buttons, $id, $asset, $author, $params);
	}

	/**
	 * Get the editor object.
	 *
	 * @return  object
	 */
	public function instance()
	{
		try
		{
			$editor = App::get('editor');
		}
		catch (Exception $e)
		{
			$editor = \Hubzero\Html\Editor::getInstance('none');
		}

		return $editor;
	}
}
