<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use Exception;

/**
 * Renders a SQL element
 */
class Sql extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Sql';

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
		$db = \App::get('db');
		$db->setQuery((string) $node['query']);

		$key = (string) $node['key_field'];
		$key = $key ?: 'value';

		$val = (string) $node['value_field'];
		$val = $val ?: $name;

		$options = $db->loadObjectlist();

		// Check for an error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		if (!$options)
		{
			$options = array();
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array(
				'id'          => $control_name . $name,
				'list.attr'   => 'class="inputbox"',
				'list.select' => $value,
				'option.key'  => $key,
				'option.text' => $val
			)
		);
	}
}
