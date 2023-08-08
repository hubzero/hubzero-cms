<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		catch (\Exception $e)
		{
			$editor = \Hubzero\Html\Editor::getInstance('none');
		}

		return $editor;
	}
}
