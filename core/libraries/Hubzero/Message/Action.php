<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;

/**
 * Model class for message actions
 */
class Action extends Relational
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
	protected $table = '#__xmessage_action';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'element' => 'notempty'
	);

	/**
	 * Get records for specific type, element, component, and user
	 *
	 * @param   string   $type       Action type
	 * @param   string   $component  Component name
	 * @param   integer  $element    ID of element that needs action
	 * @param   integer  $uid        User ID
	 * @return  object
	 */
	public static function getActionItems($type, $component, $element, $uid)
	{
		$entries = self::all();

		$a = $entries->getTableName();
		$m = Message::blank()->getTableName();
		$r = Recipient::blank()->getTableName();

		return $entries
			->select($m . '.id')
			->join($r, $r . '.actionid', $a . '.id', 'inner')
			->join($m, $m . '.id', $r . '.mid', 'inner')
			->whereEquals($m . '.type', $type)
			->whereEquals($r . '.uid', $uid)
			->whereEquals($a . '.class', $component)
			->whereEquals($a . '.element', $element)
			->rows();
	}
}
