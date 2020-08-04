<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration;

use Hubzero\Database\Driver;

/**
 * Abstract migration macro class
 */
abstract class Macro
{
	/**
	 * Migration instance
	 *
	 * @var  object
	 */
	protected $migration = null;

	/**
	 * Database connection
	 *
	 * @var  object
	 */
	protected $db = null;

	/**
	 * Set the database connection
	 *
	 * @param   object  $database
	 * @return  object
	 */
	public function setDatabase(Driver $database)
	{
		$this->db = $database;

		return $this;
	}

	/**
	 * Set the migration object
	 *
	 * @param   object  $migration
	 * @return  object
	 */
	public function setMigration(Base $migration)
	{
		$this->migration = $migration;

		return $this;
	}

	/**
	 * Get the migration object
	 *
	 * @return  object
	 */
	public function getMigration()
	{
		return $this->migration;
	}

	/**
	 * Log a message
	 *
	 * @param   string  $message
	 * @param   string  $type
	 * @return  object
	 */
	public function log($message, $type='info')
	{
		return $this->getMigration()->log($message, $type);
	}
}
