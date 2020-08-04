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
 * Renders a list of template styles.
 */
class TemplateStyle extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'TemplateStyle';

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
			->select('*')
			->from('#__template_styles')
			->whereEquals('client_id', '0')
			->whereEquals('home', '0');

		$db->setQuery($query->toString());
		$data = $db->loadObjectList();

		$default = Builder\Select::option(0, App::get('language')->txt('JOPTION_USE_DEFAULT'), 'id', 'description');
		array_unshift($data, $default);

		$selected = $this->_getSelected();
		$html = Builder\Select::genericlist($data, $control_name . '[' . $name . ']', 'class="inputbox" size="6"', 'id', 'description', $selected);

		return $html;
	}

	/**
	 * Get the selected template style.
	 *
	 * @return  integer  The template style id.
	 */
	protected function _getSelected()
	{
		$id = App::get('request')->getVar('cid', 0);

		$db = App::get('db');
		$query = $db->getQuery()
			->select('template_style_id')
			->from('#__menu')
			->whereEquals('id', (int) $id[0]);
		$db->setQuery($query->toString());

		return $db->loadResult();
	}
}
