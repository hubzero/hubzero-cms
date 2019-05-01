<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use App;

/**
 * Supports an HTML select list of menus
 */
class Menu extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Menu';

	/**
	 * Method to get the list of menus for the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('menutype', 'value')
			->select('title', 'text')
			->from('#__menu_types')
			->order('title', 'asc');

		$db->setQuery($query->toString());
		$menus = $db->loadObjectList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $menus);

		return $options;
	}
}
