<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
