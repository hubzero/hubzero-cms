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
 * Renders a languages element
 */
class Languages extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Languages';

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
		$client = (string) $node['client'];

		$languages = App::get('language')->createLanguageList($value, constant('JPATH_' . strtoupper($client)), true);
		array_unshift($languages, Builder\Select::option('', App::get('language')->txt('JOPTION_SELECT_LANGUAGE')));

		return Builder\Select::genericlist(
			$languages,
			$control_name . '[' . $name . ']',
			array(
				'id' => $control_name . $name,
				'list.attr' => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
