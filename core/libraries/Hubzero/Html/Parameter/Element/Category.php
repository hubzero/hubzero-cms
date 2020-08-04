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
 * Renders a category element
 */
class Category extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Category';

	/**
	 * Fetch the element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string  HTML string for a calendar
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$extension = (string) $node['extension'];
		$class     = (string) $node['class'];
		$filter    = explode(',', (string) $node['filter']);

		if (!isset($extension))
		{
			// Alias for extension
			$extension = (string) $node['scope'];
			if (!isset($extension))
			{
				$extension = 'com_content';
			}
		}

		if (!$class)
		{
			$class = "inputbox";
		}

		if (count($filter) < 1)
		{
			$filter = null;
		}

		return Builder\Select::genericlist(
			Builder\Category::options($extension),
			$control_name . '[' . $name . ']',
			array(
				'class' => $class
			),
			'value',
			'text',
			(int) $value,
			$control_name . $name
		);
	}
}
