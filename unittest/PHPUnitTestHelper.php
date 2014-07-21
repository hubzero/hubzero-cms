<?php

/**
 * PHP unit test helper class for HUBzero
 */
class PHPUnitTestHelper
{
	/**
	 * Standard setup process for tests running in 'site'
	 */
	public static function siteSetup()
	{
		// Trick joomla into thinking client is 'site'
		$app = JFactory::getApplication('site');

		return true;
	}

	/**
	 * Standard setup process for tests running using selenium
	 */
	public function seleniumSetup()
	{
		$this->setHost(SELENIUM_HOST);
		$this->setBrowser(SELENIUM_BROWSER);
		$this->setPort(SELENIUM_PORT);
		$this->setBrowserUrl(SELENIUM_BROWSER_URL);
	}

	/**
	 * Get testing DBO
	 */
	public static function getDBO()
	{
		// Define database connection parameters for test db
		$options = array(
			'user' => TEST_DB_USER,
			'password' => TEST_DB_PASSWORD,
			'database' => TEST_DB_DATABASE,
			'prefix' => TEST_DB_PREFIX
		);

		// Get db instance
		$db = JDatabase::getInstance($options);

		// Get the application environment
		$config = JFactory::getConfig();
		$environment = $config->getValue('config.application_env');

		// If that didn't work, and we're not on a production machine, get the default db
		if (get_class($db) != 'JDatabaseMySQL' && $environment != 'production')
		{
			$db = JFactory::getDBO();
		}
		elseif (get_class($db) != 'JDatabaseMySQL' && $environment == 'production')
		{
			die('You must setup a test database to run unit tests on a production system');
		}

		return $db;
	}
}