<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Access;

use Hubzero\Database\Nested;

/**
 * Access asset
 */
class Asset extends Nested
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

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
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty',
		'name'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'parent_id'
	);

	/**
	 * Automatic fields to populate every time a row is touched
	 *
	 * @var  array
	 **/
	public $always = array(
		'rules'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('parent_id', function($data)
		{
			/*if (!isset($data['parent_id']) || $data['parent_id'] == 0)
			{
				return 'Entries must have a parent ID.';
			}*/

			if (isset($data['parent_id']) && $data['parent_id'])
			{
				$parent = self::oneOrNew($data['parent_id']);

				return $parent->get('id') ? false : 'The set parent does not exist.';
			}
			else
			{
				return false;
			}
		});
	}

	/**
	 * Generates automatic parent_id field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParentId($data)
	{
		if (!isset($data['parent_id']))
		{
			$data['parent_id'] = 0;
		}

		if ((!isset($data['id']) || !$data['id'])
		 && ($data['parent_id'] == 0))
		{
			$data['parent_id'] = self::getRootId();
		}

		return $data['parent_id'];
	}

	/**
	 * Generates automatic rules field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRules($data)
	{
		if (!isset($data['rules']))
		{
			$data['rules'] = '{}';
		}

		if (!is_string($data['rules']))
		{
			$data['rules'] = (string)$data['rules'];
		}

		return $data['rules'];
	}

	/**
	 * Method to load an asset by it's name.
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function oneByName($name)
	{
		$model = self::all()
			->whereEquals('name', $name)
			->row();

		if (!$model)
		{
			$model = self::blank();
		}

		return $model;
	}

	/**
	 * Method to load root node ID
	 *
	 * @return  integer
	 */
	public static function getRootId()
	{
		$result = self::all()
			->whereEquals('parent_id', 0)
			->row();

		if (!$result->get('id'))
		{
			$result = self::all()
				->whereEquals('lft', 0)
				->row();

			if (!$result->get('id'))
			{
				$result = self::all()
					->whereEquals('alias', 'root.1')
					->row();
			}
		}

		return $result->get('id');
	}
}
