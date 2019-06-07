<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

require_once __DIR__ . '/sessionclass.php';

/**
 * Tool preferences model
 *
 * @uses \Hubzero\Database\Relational
 */
class Preference extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'users_tool';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'user_id' => 'positive|nonzero'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $_params = null;

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->_params))
		{
			$this->_params = new Registry($this->get('params'));
		}

		return $this->_params;
	}

	/**
	 * Get relationship to sessionclass
	 *
	 * @return  object
	 */
	public function sessionclass()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Sessionclass', 'class_id');
	}

	/**
	 * Save content
	 *
	 * @return  boolean  True on success.
	 */
	public function save()
	{
		if (is_object($this->_params))
		{
			$this->set('params', $this->_params->toString());
		}

		return parent::save();
	}

	/**
	 * Retrieves one row loaded by user_id field
	 *
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByUser($user_id)
	{
		return self::blank()
			->whereEquals('user_id', $user_id)
			->row();
	}

	/**
	 * Update all quotas of a certain class ID to reflect a change in class defaults
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public static function updateUsersByClassId($id)
	{
		$class = SessionClass::oneOrNew($id);

		if (!$class->get('id'))
		{
			return false;
		}

		$records = self::all()
			->whereEquals('class_id', $class->get('id'))
			->rows();

		if ($records && count($records) > 0)
		{
			foreach ($records as $preference)
			{
				$preference->set('jobs', $class->get('jobs'));
				$preference->save();
			}
		}

		return true;
	}

	/**
	 * Upon deletion of a class, restore all users of that class to the default class
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public static function restoreDefaultClass($id)
	{
		$class = SessionClass::oneByAlias('default');

		if (!$class->get('id'))
		{
			return false;
		}

		$records = self::all()
			->whereEquals('class_id', $id)
			->rows();

		if ($records && count($records) > 0)
		{
			foreach ($records as $preference)
			{
				$preference->set('jobs', $class->get('jobs'));
				$preference->set('class_id', $class->get('id'));
				$preference->save();
			}
		}

		return true;
	}
}
