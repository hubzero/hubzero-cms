<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;

/**
 * Renders a menu element
 */
class Menu extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Menu';

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
		require_once PATH_CORE . '/components/com_menus/admin/helpers/menus.php';
		$menuTypes = \MenusHelper::getMenuTypes();

		foreach ($menuTypes as $menutype)
		{
			$options[] = Builder\Select::option($menutype, $menutype);
		}
		array_unshift($options, Builder\Select::option(\App::get('language')->txt('JOPTION_SELECT_MENU')));

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array('id' => $control_name . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
