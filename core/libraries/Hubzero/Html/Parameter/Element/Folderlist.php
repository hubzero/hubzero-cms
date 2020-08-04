<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use App;

/**
 * Renders a filelist element
 */
class Folderlist extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Folderlist';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		// Initialise variables.
		$path = PATH_ROOT . DS . (string) $node['directory'];

		$filter  = (string) $node['filter'];
		$exclude = (string) $node['exclude'];
		$folders = App::get('filesystem')->folders($path, $filter);

		$options = array();
		foreach ($folders as $folder)
		{
			if ($exclude)
			{
				if (preg_match(chr(1) . $exclude . chr(1), $folder))
				{
					continue;
				}
			}
			$options[] = Builder\Select::option($folder, $folder);
		}

		if (!$node['hide_none'])
		{
			array_unshift($options, Builder\Select::option('-1', App::get('language')->txt('JOPTION_DO_NOT_USE')));
		}

		if (!$node['hide_default'])
		{
			array_unshift($options, Builder\Select::option('', App::get('language')->txt('JOPTION_USE_DEFAULT')));
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array('id' => 'param' . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
