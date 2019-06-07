<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'version.php';

/**
 * Tool database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Tool extends Relational
{
	/**
	 * Status constants
	 */
	const STATUS_UNPUBLISHED = 0;
	const STATUS_REGISTERED  = 1;
	const STATUS_CREATED     = 2;
	const STATUS_UPLOADED    = 3;
	const STATUS_INSTALLED   = 4;
	const STATUS_UPDATED     = 5;
	const STATUS_APPROVED    = 6;
	const STATUS_PUBLISHED   = 7;
	const STATUS_RETIRED     = 8;
	const STATUS_ABANDONED   = 9;

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $table = '#__tool';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'toolname' => 'notempty',
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'registered',
		'registered_by'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticToolname($data)
	{
		$alias = $data['toolname'];
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 64)
		{
			$alias = substr($alias . ' ', 0, 64);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic registered field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticRegistered($data)
	{
		return (isset($data['registered']) && $data['registered'] ? $data['registered'] : Date::toSql());
	}

	/**
	 * Generates automatic registered by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticRegisteredBy($data)
	{
		return (isset($data['registered_by']) && $data['registered_by'] ? (int)$data['registered_by'] : (int)User::get('id'));
	}

	/**
	 * Get a list of versions
	 *
	 * @return  object
	 */
	public function versions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Version', 'toolname', 'toolname');
	}

	/**
	 * Retrieves one row loaded by toolname field
	 *
	 * @param   string  $toolname
	 * @return  mixed
	 */
	public static function oneByToolname($toolname)
	{
		return self::blank()
			->whereEquals('toolname', $toolname)
			->row();
	}

	/**
	 * Is the tool registered?
	 *
	 * @return  boolean
	 */
	public function isRegistered()
	{
		return ($this->get('state') >= self::STATUS_REGISTERED);
	}

	/**
	 * Is the tool created?
	 *
	 * @return  boolean
	 */
	public function isCreated()
	{
		return ($this->get('state') >= self::STATUS_CREATED);
	}

	/**
	 * Is the tool installed?
	 *
	 * @return  boolean
	 */
	public function isInstalled()
	{
		return ($this->get('state') >= self::STATUS_INSTALLED);
	}
}
