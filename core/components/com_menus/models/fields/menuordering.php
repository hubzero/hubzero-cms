<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Exception;
use Lang;
use App;

/**
 * Form Field class
 */
class MenuOrdering extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'MenuOrdering';

	/**
	 * Method to get the list of siblings in a menu.
	 * The method requires that parent be set.
	 *
	 * @return  array  The field option objects or false if the parent field has not been set
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Get the parent
		$parent_id = $this->form->getValue('parent_id', 0);

		if (empty($parent_id))
		{
			return false;
		}

		$db = App::get('db');
		$query = $db->getQuery();

		$query->select('a.id', 'value')
			->select('a.title', 'text');
		$query->from('#__menu', 'a');

		$query->where('a.published', '>=', '0');
		$query->whereEquals('a.parent_id', (int) $parent_id);

		if ($menuType = $this->form->getValue('menutype'))
		{
			$query->whereEquals('a.menutype', $menuType);
		}
		else
		{
			$query->where('a.menutype', '!=', '');
		}

		$query->order('a.lft', 'asc');

		// Get the options.
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		$options = array_merge(
			array(array('value' => '-1', 'text' => Lang::txt('COM_MENUS_ITEM_FIELD_ORDERING_VALUE_FIRST'))),
			$options,
			array(array('value' => '-2', 'text' => Lang::txt('COM_MENUS_ITEM_FIELD_ORDERING_VALUE_LAST')))
		);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to get the field input markup
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		if ($this->form->getValue('id', 0) == 0)
		{
			return '<span class="readonly">' . Lang::txt('COM_MENUS_ITEM_FIELD_ORDERING_TEXT') . '</span>';
		}

		return parent::getInput();
	}
}
