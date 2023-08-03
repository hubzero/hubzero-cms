<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;
use Lang;

/**
 * Model class for message component list
 * These are action items that are message-able
 */
class Component extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xmessage';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__xmessage_component';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'component';

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
		'component' => 'notempty',
		'action'    => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('component', function($data)
		{
			$this->connection->setQuery("SELECT element FROM `#__extensions` AS e WHERE e.type = 'component' ORDER BY e.name ASC");
			$extensions = $this->connection->loadColumn();
			if (!in_array($data['component'], $extensions))
			{
				return Lang::txt('Component does not exist.');
			}
			return false;
		});
	}

	/**
	 * Defines a belongs to one relationship between newsletter and story
	 *
	 * @return  object
	 */
	public function getRecords($filters = array())
	{
		$entries = self::all();

		$c = $entries->getTableName();
		$e = '#__extensions';

		$entries
			->select($c . '.*,' . $e . '.name')
			->join($e, $e . '.element', $c . '.component', 'inner')
			->whereEquals($e . '.type', 'component');

		if (isset($filters['component']) && $filters['component'])
		{
			$entries->whereEquals($e . '.element', $filters['component']);
		}

		return $entries
			->ordered($c . '.component', 'asc')
			->rows();
	}

	/**
	 * Get all records
	 *
	 * @return  array
	 */
	public function getComponents()
	{
		return self::all()
			->deselect()
			->select('component')
			->order('component', 'asc')
			->group('component')
			->rows();
	}
}
