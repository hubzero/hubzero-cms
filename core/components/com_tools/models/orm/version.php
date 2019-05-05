<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Tool version model
 *
 * @uses \Hubzero\Database\Relational
 */
class Version extends Relational
{
	/**
	 * Status constants
	 */
	const STATUS_DEV = 3;
	const STATUS_CURRENT = 1;

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'tool';

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $table = '#__tool_version';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'toolname' => 'notempty',
	);

	/**
	 * Get a list of versions
	 *
	 * @return  object
	 */
	public function tool()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Tool', 'toolname', 'toolname');
	}

	/**
	 * Retrieves one row loaded by toolname and revision fields
	 *
	 * @param   string  $toolname
	 * @param   string  $revision
	 * @return  mixed
	 */
	public static function oneByToolnameAndRevision($toolname, $revision)
	{
		$query = self::blank()
			->whereEquals('toolname', $toolname);

		if ($revision == 'current')
		{
			$query->whereEquals('state', self::STATUS_CURRENT);
		}
		else if ($revision == 'dev')
		{
			$query->whereEquals('state', self::STATUS_DEV);
		}
		else
		{
			$query->whereEquals('revision', $revision);
		}

		return $query->row();
	}

	/**
	 * Retrieves one row loaded by instance field
	 *
	 * @param   string  $instance
	 * @return  mixed
	 */
	public static function oneByInstance($instance)
	{
		return self::blank()
			->whereEquals('instance', $instance)
			->row();
	}

	/**
	 * Is the tool version the development version?
	 *
	 * @return  boolean
	 */
	public function isDev()
	{
		return ($this->get('state') == self::STATUS_DEV);
	}

	/**
	 * Is the tool version the current active version?
	 *
	 * @return  boolean
	 */
	public function isCurrent()
	{
		return ($this->get('state') == self::STATUS_CURRENT);
	}
}
