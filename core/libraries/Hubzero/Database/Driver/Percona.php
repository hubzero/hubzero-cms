<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

/**
 * Percona (Pdo) database driver
 *
 * Percona is a drop-in replacement for MySQL and
 * supports everything in the MySQL driver. But,
 * it does have some features unique to it.
 */
class Percona extends Mysql
{
	/**
	 * Set the database engine of the given table
	 *
	 * @param   string  $table   The table for which to retrieve the engine type
	 * @param   string  $engine  The engine type to set
	 * @return  bool
	 * @since   2.2.15
	 **/
	public function setEngine($table, $engine)
	{
		$supported = [
			// MySQL
			'innodb',
			'myisam',
			'archive',
			'merge',
			'memory',
			'csv',
			'federated',
			// Percona
			'xtradb'
		];

		if (!in_array(strtolower($engine), $supported))
		{
			throw new UnsupportedFeatureException(sprintf(
				'Unsupported engine type of "%s" specified. Engine type must be one of %s',
				$engine,
				implode(', ', $supported)
			));
		}

		$this->setQuery('ALTER TABLE ' . str_replace('#__', $this->tablePrefix, $this->quote($table, false)) . " ENGINE = " . $this->quote($engine));

		return $this->db->query();
	}
}
