<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Pdo as PdoDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Mysql (Pdo) database driver
 */
class Mysql extends PdoDriver
{
	/**
	 * Constructs a new database object based on the given params
	 *
	 * @param   array  $options  The database connection params
	 * @return  void
	 */
	public function __construct($options)
	{
		// Add "extra" options as needed
		if (!isset($options['extras']))
		{
			$options['extras'] = [];
		}

		// Check if we're trying to make an SSL connection
		if (isset($options['ssl_ca']) && $options['ssl_ca'] && $options['host'] != 'localhost')
		{
			$options['extras'][\PDO::MYSQL_ATTR_SSL_CA] = $options['ssl_ca'];
		}

		// Establish connection string
		if (!isset($options['dsn']))
		{
			$options['dsn']  = "mysql:host={$options['host']};charset=utf8";
			if (isset($options['port']))
			{
				$options['dsn'] .= ";port={$options['port']}";
			}
			$options['dsn'] .= (isset($options['database']) && $options['database']) ? ";dbname={$options['database']}" : '';
		}

		if (substr($options['dsn'], 0, 6) != 'mysql:')
		{
			throw new ConnectionFailedException('MySQL DSN for PDO connection does not appear to be valid.', 500);
		}

		// Call parent construct
		parent::__construct($options);
	}
}
