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
class Filelist extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Filelist';

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
		// path to images directory
		$path = PATH_ROOT . DS . (string) $node['directory'];

		$filter   = (string) $node['filter'];
		$exclude  = (string) $node['exclude'];
		$stripExt = (string) $node['stripext'];
		$files    = App::get('filesystem')->files($path, $filter);

		$options = array();

		if (!$node['hide_none'])
		{
			$options[] = Builder\Select::option('-1', App::get('language')->txt('JOPTION_DO_NOT_USE'));
		}

		if (!$node['hide_default'])
		{
			$options[] = Builder\Select::option('', App::get('language')->txt('JOPTION_USE_DEFAULT'));
		}

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $file))
					{
						continue;
					}
				}
				if ($stripExt)
				{
					$file = App::get('filesystem')->extension($file);
				}
				$options[] = Builder\Select::option($file, $file);
			}
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array('id' => 'param' . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
