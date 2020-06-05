<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Models;

use Hubzero\Database\Relational;
use Hubzero\Form\Form;
use Filesystem;
use Lang;
use User;

require_once __DIR__ . '/item.php';
require_once __DIR__ . '/module.php';
require_once __DIR__ . '/menutype.php';

/**
 * Menu type model
 */
class Menu extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__menu_types';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'    => 'notempty',
		'menutype' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'menutype'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('menutype', function($data)
		{
			if (!$data['menutype'])
			{
				$data['menutype'] = $this->automaticMenutype($data);
			}

			$query = self::blank()
				->whereEquals('menutype', $data['menutype']);

			if (isset($data['id']) && $data['id'])
			{
				$query->where('id', '!=', $data['id']);
			}

			$total = $query->total();

			return !$total ? false : Lang::txt('A menu with the specified menutype already exists');
		});
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticMenutype($data)
	{
		$alias = (isset($data['menutype']) && $data['menutype'] ? $data['menutype'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Get a list of menu items
	 *
	 * @return  object
	 */
	public function items()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Item', 'menutype', 'menutype');
	}

	/**
	 * Get a list of menu items
	 *
	 * @return  object
	 */
	public function modules()
	{
		$query = Module::all()
			->whereEquals('module', 'mod_menu')
			->whereLike('params', '"menutype":' . json_encode($this->get('menutype')));

		return $query;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		if (!$this->isNew())
		{
			// Get the old value of the table just in case the 'menutype' changed
			$prev = self::oneOrNew($this->get('id'));

			if ($this->get('menutype') != $prev->get('menutype'))
			{
				// Get the user id
				$userId = User::get('id');

				// Verify that no items are checked out
				$checked_out = $prev->items()
					->where('checked_out', '!=', (int) $userId)
					->where('checked_out', '!=', 0)
					->total();

				if ($checked_out)
				{
					$this->addError(
						Lang::txt('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), Lang::txt('JLIB_DATABASE_ERROR_MENUTYPE_CHECKOUT'))
					);
					return false;
				}

				// Verify that no module for this menu are checked out
				$checked_out = $prev->modules()
					->where('checked_out', '!=', (int) $userId)
					->where('checked_out', '!=', 0)
					->total();

				if ($checked_out)
				{
					$this->addError(
						Lang::txt('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), Lang::txt('JLIB_DATABASE_ERROR_MENUTYPE_CHECKOUT'))
					);
					return false;
				}

				// Update the menu items
				foreach ($prev->items()->rows() as $item)
				{
					$item->set('menutype', $this->get('menutype'));

					if (!$item->save())
					{
						$this->addError(Lang::txt('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $item->getError()));
						return false;
					}
				}

				// Update the module items
				foreach ($prev->modules()->rows() as $module)
				{
					$module->params->set('menutype', $this->get('menutype'));
					$module->set('params', $module->params->toString());

					if (!$module->save())
					{
						$this->addError(Lang::txt('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $module->getError()));
						return false;
					}
				}
			}
		}

		return parent::save();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		if ($this->isNew())
		{
			return true;
		}

		// Get the user id
		$userId = User::get('id');

		// Verify that no items are checked out
		$checked_out = $this->items()
			->where('checked_out', '!=', (int) $userId)
			->where('checked_out', '!=', 0)
			->total();

		if ($checked_out)
		{
			$this->addError(
				Lang::txt('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), Lang::txt('JLIB_DATABASE_ERROR_MENUTYPE_CHECKOUT'))
			);
			return false;
		}

		// Verify that no module for this menu are checked out
		$checked_out = $this->modules()
			->where('checked_out', '!=', (int) $userId)
			->where('checked_out', '!=', 0)
			->total();

		if ($checked_out)
		{
			$this->addError(
				Lang::txt('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), Lang::txt('JLIB_DATABASE_ERROR_MENUTYPE_CHECKOUT'))
			);
			return false;
		}

		// Delete the menu items
		foreach ($this->items()->rows() as $item)
		{
			if (!$item->destroy())
			{
				$this->addError($item->getError());
				return false;
			}
		}

		// Delete the module items
		foreach ($this->modules()->rows() as $module)
		{
			if (!$module->destroy())
			{
				$this->addError($module->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a count of published menu items
	 *
	 * @return  integer
	 */
	public function countPublishedItems()
	{
		$total = $this->items()
			->whereEquals('published', self::STATE_PUBLISHED)
			->total();

		return (int)$total;
	}

	/**
	 * Get a count of unpublished menu items
	 *
	 * @return  integer
	 */
	public function countUnpublishedItems()
	{
		$total = $this->items()
			->whereEquals('published', self::STATE_UNPUBLISHED)
			->total();

		return (int)$total;
	}

	/**
	 * Get a count of unpublished menu items
	 *
	 * @return  integer
	 */
	public function countTrashedItems()
	{
		$total = $this->items()
			->whereEquals('published', -2)
			->total();

		return (int)$total;
	}

	/**
	 * Get a form
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/menu.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('menu', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->toArray();
		$form->bind($data);

		return $form;
	}

	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 */
	public function rebuild()
	{
		// Initialiase variables.
		$items = Item::blank();

		if (!$items->rebuild(1))
		{
			$this->addError($items->getError());
			return false;
		}

		return true;
	}

	/**
	 * Gets a list of all mod_mainmenu modules and collates them by menutype
	 *
	 * @return  array
	 */
	public static function getModules()
	{
		$m = Module::blank()->getTableName();

		$db = \App::get('db');

		$query = $db->getQuery();
		$query->from($m, 'a');
		$query->select('a.id');
		$query->select('a.title');
		$query->select('a.params');
		$query->select('a.position');
		$query->whereEquals('module', Module::MODULE_NAME);
		$query->select('ag.title', 'access_title');
		$query->join('#__viewlevels AS ag', 'ag.id', 'a.access', 'left');

		$db->setQuery($query->toString());

		$modules = $db->loadObjectList();

		$result = array();

		foreach ($modules as &$module)
		{
			$params = new \Hubzero\Config\Registry($module->params);

			$menuType = $params->get('menutype');
			if (!isset($result[$menuType]))
			{
				$result[$menuType] = array();
			}
			$result[$menuType][] = &$module;
		}

		return $result;
	}

	/**
	 * Load a record by menutype
	 *
	 * @param   string  $menutype
	 * @return  object
	 */
	public static function oneByMenutype($menutype)
	{
		$row = self::all()
			->whereEquals('menutype', $menutype)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
	}
}
