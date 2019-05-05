<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . '/sessionclassgroup.php';

/**
 * Tool sessionclass model
 *
 * @uses \Hubzero\Database\Relational
 */
class SessionClass extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'tool';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__tool_session_classes';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'alias' => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('alias', function($data)
		{
			$record = self::oneByAlias($data['alias']);

			if ($record && $record->get('id'))
			{
				return Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_ALIAS');
			}

			return false;
		});

		$this->addRule('jobs', function($data)
		{
			$record = self::oneByJobs($data['jobs']);

			if ($record && $record->get('id'))
			{
				return Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_VALUE');
			}

			return false;
		});
	}

	/**
	 * Get relationship to sessionclass groups
	 *
	 * @return  object
	 */
	public function sessionclassgroups()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\SessionClassGroup', 'class_id');
	}

	/**
	 * Retrieves one row loaded by jobs field
	 *
	 * @param   integer  $jobs
	 * @return  object
	 */
	public static function oneByJobs($jobs)
	{
		return self::all()
			->whereEquals('jobs', $jobs)
			->row();
	}

	/**
	 * Create the 'default' entry
	 *
	 * @return  boolean
	 */
	public static function createDefault()
	{
		$record = self::oneByAlias('default');

		if ($record && $record->get('id'))
		{
			return true;
		}

		$record = self::blank()->set(array(
			'alias' => 'default',
			'jobs'  => 3
		));

		if (!$record->save())
		{
			return false;
		}

		return true;
	}
}
