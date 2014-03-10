<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for inserting timezone data into mysql
 **/
class Migration20140305130635Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		function getRootDB()
		{
			$secrets   = DS . 'etc'  . DS . 'hubzero.secrets';
			$conf_file = DS . 'root' . DS . '.my.cnf';

			if (file_exists($secrets))
			{
				$conf = parse_ini_file($secrets);
				$user = 'root';
				$pw   = $conf['MYSQL-ROOT'];
			}

			if (file_exists($conf_file))
			{
				$conf = parse_ini_file($conf_file, true);
				$user = $conf['client']['user'];
				$pw   = $conf['client']['password'];
			}

			if (isset($user) && isset($pw))
			{
				// Instantiate a config object
				$jconfig = new JConfig();

				$db = JDatabase::getInstance(
					array(
						'driver'   => 'pdo',
						'host'     => $jconfig->host,
						'user'     => $user,
						'password' => $pw,
						'database' => $jconfig->db,
						'prefix'   => 'jos_'
					)
				);

				// Test the connection
				if (!$db->connected())
				{
					return false;
				}
				else
				{
					return $db;
				}
			}

			return false;
		}

		// Try to get elevated db access
		if (!$db = getRootDB())
		{
			$return = new stdClass();
			$return->error = new stdClass();
			$return->error->type = 'fatal';
			$return->error->message = 'This migration requires elevated privileges. Please try running again as root.';
			return $return;
		}

		$db->select('mysql');

		// Get file
		$file = JPATH_ROOT . DS . 'installation' . DS . 'sql' . DS . 'mysql' . DS . 'tzinfo.sql';
		$contents = file_get_contents($file);

		$db->setQuery($contents);
		$db->query();
	}
}