<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use stdClass;
use Request;
use Html;
use Lang;
use App;

/**
 * CategoryEdit form field
 */
class CategoryEdit extends Select
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var  string
	 */
	public $type = 'CategoryEdit';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$published = $this->element['published']? $this->element['published'] : array(0,1);
		$name = (string) $this->element['name'];

		// Let's get the id for the current item, either category or content item.
		// Load the category options for a given extension.

		// For categories the old category is the category id or 0 for new category.
		if ($this->element['parent'] || Request::getCmd('option') == 'com_categories')
		{
			$oldCat = Request::getInt('id', 0);
			$oldParent = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) Request::getCmd('extension', 'com_content');
		}
		else
		// For items the old category is the category they are in when opened or 0 if new.
		{
			$thisItem = Request::getInt('id', 0);
			$oldCat = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) Request::getCmd('option', 'com_content');
		}

		$db = App::get('db');
		$query = $db->getQuery();

		$query->select('a.id', 'value')
			->select('a.title', 'text')
			->select('a.level')
			->select('a.published')
			->from('#__categories', 'a')
			->joinRaw('#__categories AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left');

		// Filter by the extension type
		if ($this->element['parent'] == true || Request::getCmd('option') == 'com_categories')
		{
			$query->whereEquals('a.extension', $extension, 1)
				->orWhereEquals('a.parent_id', '0', 1)
				->resetDepth();
		}
		else
		{
			$query->whereEquals('a.extension', $extension);
		}

		// If parent isn't explicitly stated but we are in com_categories assume we want parents
		if ($oldCat != 0 && ($this->element['parent'] == true || Request::getCmd('option') == 'com_categories'))
		{
		// Prevent parenting to children of this item.
		// To rearrange parents and children move the children up, not the parents down.
			$query->joinRaw('#__categories AS p', 'p.id = '.(int) $oldCat, 'left');
			$query->whereRaw('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

			$rowQuery = $db->getQuery();
			$rowQuery->select('a.id', 'value')
				->select('a.title', 'text')
				->select('a.level')
				->select('a.parent_id')
				->from('#__categories', 'a')
				->whereEquals('a.id', (int) $oldCat);
			$db->setQuery($rowQuery->toString());
			$row = $db->loadObject();
		}

		// Filter on the published state
		if (is_numeric($published))
		{
			$query->whereEquals('a.published', (int) $published);
		}
		elseif (is_array($published))
		{
			\Hubzero\Utility\Arr::toInteger($published);
			$query->whereIn('a.published', $published);
		}

		$query->group('a.id')
			->group('a.title')
			->group('a.level')
			->group('a.lft')
			->group('a.rgt')
			->group('a.extension')
			->group('a.parent_id')
			->group('a.published')
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
			if ($this->element['parent'] == true || Request::getCmd('option') == 'com_categories')
			{
				if ($options[$i]->level == 0)
				{
					$options[$i]->text = Lang::txt('JGLOBAL_ROOT_PARENT');
				}
			}
			if ($options[$i]->published == 1)
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			}
			else
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' .$options[$i]->text . ']';
			}
		}

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
			// If you are only allowed to edit in this category but not edit.state, you should not get any
			// option to change the category parent for a category or the category for a content item,
			// but you should be able to save in that category.
			foreach ($options as $i => $option)
			{
				if (\User::authorise('core.edit.state', $extension . '.category.' . $oldCat) != true && !isset($oldParent))
				{
					if ($option->value != $oldCat)
					{
						unset($options[$i]);
					}
				}
				if (\User::authorise('core.edit.state', $extension . '.category.' . $oldCat) != true
					&& (isset($oldParent)) && $option->value != $oldParent)
				{
					unset($options[$i]);
				}

				// However, if you can edit.state you can also move this to another category for which you have
				// create permission and you should also still be able to save in the current category.
				if ((\User::authorise('core.create', $extension . '.category.' . $option->value) != true)
					&& ($option->value != $oldCat && !isset($oldParent)))
				{
					{
						unset($options[$i]);
					}
				}
				if ((\User::authorise('core.create', $extension . '.category.' . $option->value) != true)
					&& (isset($oldParent)) && $option->value != $oldParent)
				{
					{
						unset($options[$i]);
					}
				}
			}
		}
		if (($this->element['parent'] == true || Request::getCmd('option') == 'com_categories') &&
			(isset($row) && !isset($options[0])) && isset($this->element['show_root']))
		{
			if ($row->parent_id == '1')
			{
				$parent = new stdClass();
				$parent->text = Lang::txt('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
			array_unshift($options, Html::select('option', '0', Lang::txt('JGLOBAL_ROOT')));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
