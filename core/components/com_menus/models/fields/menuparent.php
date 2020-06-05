<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use App;
use Exception;

/**
 * Form Field class
 */
class MenuParent extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'MenuParent';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = App::get('db');
		$query = $db->getQuery();

		$query->select('a.id', 'value')
			->select('a.title', 'text')
			->select('a.level')
			->from('#__menu', 'a')
			->joinRaw('#__menu AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left');

		if ($menuType = $this->form->getValue('menutype'))
		{
			$query->whereEquals('a.menutype', $menuType);
		}
		else
		{
			$query->where('a.menutype', '!=', '');
		}

		// Prevent parenting to children of this item.
		if ($id = $this->form->getValue('id'))
		{
			$query->joinRaw('#__menu AS p', 'p.id = '.(int) $id, 'left');
			$query->whereRaw('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}

		$query->where('a.published', '!=', '-2');
		$query->group('a.id')
			->group('a.title')
			->group('a.level')
			->group('a.lft')
			->group('a.rgt')
			->group('a.menutype')
			->group('a.parent_id')
			->group('a.published');
		$query->order('a.lft', 'asc');

		// Get the options.
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level).$options[$i]->text;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
