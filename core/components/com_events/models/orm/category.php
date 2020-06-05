<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Event model for a category
 */
class Category extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'events';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__categories';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Returns all rows (unless otherwise limited)
	 *
	 * @param   string|array  $columns  The columns to select
	 * @return  \Hubzero\Database\Relational|static
	 */
	public static function all($columns = null)
	{
		return self::blank()->whereEquals('extension', 'com_events');
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string  $alias  The alias to load by
	 * @return  mixed
	 **/
	public static function oneByAlias($alias)
	{
		return self::blank()
			->whereEquals('extension', 'com_events')
			->whereEquals('alias', $alias)
			->row();
	}

	/**
	 * Get a list of responses
	 *
	 * @param   array    $filters  Filters to apply to query
	 * @return  object
	 */
	public function children($filters = array())
	{
		$categories = self::blank()->whereEquals('parent_id', $this->get('id'));

		if (isset($filters['state']))
		{
			$categories->whereEquals('published', $filters['state']);
		}

		if (isset($filters['access']))
		{
			$categories->whereIn('access', $filters['access']);
		}

		return $categories;
	}

	/**
	 * Get parent section
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent_id', 0));
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if (!$this->get('id'))
		{
			return true;
		}

		// Remove children
		foreach ($this->children()->rows() as $category)
		{
			if (!$category->destroy())
			{
				$this->addError($category->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a form
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/category.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('category', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$params = new Registry($this->get('params'));

		$data = $this->toArray();
		$data['params'] = $params->toArray();

		$form->bind($data);

		return $form;
	}
}
