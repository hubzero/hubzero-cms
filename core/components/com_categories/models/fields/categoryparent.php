<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use stdClass;
use Request;
use Html;
use Lang;
use App;

/**
 * CategoryParent form field
 */
class CategoryParent extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'CategoryParent';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$name = (string) $this->element['name'];

		// Let's get the id for the current item, either category or content item.
		// For categories the old category is the category id 0 for new category.
		if ($this->element['parent'])
		{
			$oldCat = Request::getInt('id', 0);
			$oldParent = $this->form->getValue($name);
		}
		else
		// For items the old category is the category they are in when opened or 0 if new.
		{
			$thisItem = Request::getInt('id', 0);
			$oldCat = $this->form->getValue($name);
		}

		$db    = App::get('db');
		$query = $db->getQuery();

		$query->select('a.id', 'value')
			->select('a.title', 'text')
			->select('a.level')
			->from('#__categories', 'a')
			->joinRaw('#__categories AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left');

		// Filter by the type
		if ($extension = $this->form->getValue('extension'))
		{
			$query->whereEquals('a.extension', $extension, 1)
				->orWhereEquals('a.parent_id', '0', 1)
				->resetDepth();
		}

		if ($this->element['parent'])
		{
		// Prevent parenting to children of this item.
			if ($id = $this->form->getValue('id'))
			{
				$query->joinRaw('#__categories AS p', 'p.id = '.(int) $id, 'left');
				$query->whereRaw('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

				$rowQuery = $db->getQuery();
				$rowQuery->select('a.id', 'value')
					->select('a.title', 'text')
					->select('a.level')
					->select('a.parent_id')
					->from('#__categories', 'a')
					->whereEquals('a.id', (int) $id);
				$db->setQuery($rowQuery->toString());
				$row = $db->loadObject();
			}
		}
		$query->whereIn('a.published', array(0, 1))
			->group('a.id')
			->group('a.title')
			->group('a.level')
			->group('a.lft')
			->group('a.rgt')
			->group('a.extension')
			->group('a.parent_id')
			->order('a.lft', 'ASC');

		// Get the options.
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new \Exception($db->getErrorMsg(), 500);
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if ($options[$i]->level == 0)
			{
				$options[$i]->text = Lang::txt('JGLOBAL_ROOT_PARENT');
			}

			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}

		// Initialise variables.

		// For new items we want a list of categories you are allowed to create in.
		if ($oldCat == 0)
		{
			foreach ($options as $i => $option)
			{
				// To take save or create in a category you need to have create rights for that category
				// unless the item is already in that category.
				// Unset the option if the user isn't authorised for it. In this field assets are always categories.
				if (\User::authorise('core.create', $extension . '.category.' . $option->value) != true)
				{
					unset($options[$i]);
				}
			}
		}
		// If you have an existing category id things are more complex.
		else
		{
			foreach ($options as $i => $option)
			{
				// If you are only allowed to edit in this category but not edit.state, you should not get any
				// option to change the category parent for a category or the category for a content item,
				// but you should be able to save in that category.
				if (\User::authorise('core.edit.state', $extension . '.category.' . $oldCat) != true)
				{
					if ($option->value != $oldCat)
					{
						echo 'y';
						unset($options[$i]);
					}
				}
				// However, if you can edit.state you can also move this to another category for which you have
				// create permission and you should also still be able to save in the current category.
				else if ((\User::authorise('core.create', $extension . '.category.' . $option->value) != true) && $option->value != $oldCat)
				{
					echo 'x';
					unset($options[$i]);
				}
			}
		}

		if (isset($row) && !isset($options[0]))
		{
			if ($row->parent_id == '1')
			{
				$parent = new stdClass();
				$parent->text = Lang::txt('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
