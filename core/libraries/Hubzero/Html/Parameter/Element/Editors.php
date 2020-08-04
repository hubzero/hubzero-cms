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
 * Renders a editors element
 */
class Editors extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Editors';

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
		$db = App::get('db');

		$query = $db->getQuery()
			->select('element', 'value')
			->select('name', 'text')
			->from('#__extensions')
			->whereEquals('folder', 'editors')
			->whereEquals('type', 'plugin')
			->whereEquals('enabled', 1)
			->order('ordering', 'asc')
			->order('name', 'asc');

		$db->setQuery($query->toString());
		$editors = $db->loadObjectList();

		array_unshift($editors, Builder\Select::option('', App::get('language')->txt('JOPTION_SELECT_EDITOR')));

		return Builder\Select::genericlist(
			$editors,
			$control_name . '[' . $name . ']',
			array(
				'id'          => $control_name . $name,
				'list.attr'   => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
