<?php
/**
 * Test class for HUBzero migrations class
 *
 * @author Sam Wilson <samwilson@purdue.edu>
 */

if (!defined('_JEXEC'))
{
	define('_JEXEC', '1');
}

// Include migration class
require_once __DIR__ . '/../../../../../libraries/Hubzero/Migration.php';

/**
 * Test class for HUBzero migration utility primary 'migration' class
 */
class MigrationClassTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Variable holding our migration object instance created for each test during setup
	 *
	 * @var object
	 **/
	var $instance = null;

	/**
	 * Setup (run before every test)
	 */
	function setUp()
	{
		$this->instance = new Hubzero_Migration(__DIR__);
	}

	/**
	 * Tear down (run after every test)
	 */
	function tearDown()
	{
		unset($this->instance);
	}

	/**
	 * Test if $this->instance is an object
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance, "Migration instance isn't an object");
	}

	/**
	 * Test that instance is an instance of the migration class
	 */
	function testIsInstanceOfMigrationClass()
	{
		$this->assertTrue($this->instance instanceof Hubzero_Migration, "Instance isn't of 'Migration' class");
	}

	/**
	 * Test that our object has the necessary attributes
	 **/
	function testObjectHasNecessaryAttributes()
	{
		$this->assertClassHasAttribute('docroot', 'Hubzero_Migration', "Migration class does not have an attribute for docroot");
		$this->assertClassHasAttribute('files', 'Hubzero_Migration', "Migration class does not have an attribute for files");
	}

	/**
	 * Test that initialization sets a default docroot of the current directory, if none is provided
	 **/
	function testObjectHasDocrootOfCurrentDirectory()
	{
		$this->assertEquals(__DIR__, $this->instance->get('docroot'), 'Migration not instanciated with given docroot');
	}

	/**
	 * Test that instance created without a specific docroot finds a valid default
	 **/
	function testObjectGetsValidDefaultDocroot()
	{
		// Create an instance without specifying the docroot
		$migration = new Hubzero_Migration();
		$docroot = $migration->get('docroot');

		$this->assertTrue(!empty($docroot), 'Migration docroot is empty');
		$this->assertTrue(is_dir($docroot), 'Migration default docroot is not a valid directory');
	}

	/**
	 * Test that files is an array
	 **/
	function testFilesIsArray()
	{
		$this->assertTrue(is_array($this->instance->get('files')), "Migration files variable isn't an array");
	}

	/**
	 * Test that find migrations finds only intended files
	 **/
	function testFindReturnsCorrectFiles()
	{
		// Perform the serch for migration scripts
		$this->instance->find();
		$files = $this->instance->get('files');

		$php_extension = false;
		$sql_extension = false;

		foreach($files as $file)
		{
			$php_extension   = ($file == 'Migration20130101000000ComGroups.php') ? true : $php_extension;
			$sql_extension   = ($file == 'invalidextension.sql')                 ? true : $sql_extension;
		}

		// Given a sample file at samplemigrations/components/com_migrations/migrations/ComMigrations20130101.php
		$this->assertTrue($php_extension, "Find migrations didn't find the sample migration file");
		$this->assertFalse($sql_extension, "Find migrations included a file with a non PHP extension");
	}

	/**
	 * Test that find migrations with extension correct finds file
	 **/
	function testFindWithExtensionReturnsCorrectFiles()
	{
		// Perform the serch for migration scripts
		$this->instance->find('com_groups');
		$files = $this->instance->get('files');

		$correct_file = false;

		foreach($files as $file)
		{
			$correct_file = ($file == 'Migration20130101000000ComGroups.php') ? true : false;
		}

		// Given a sample file at samplemigrations/components/com_migrations/migrations/ComMigrations20130101.php
		$this->assertTrue($correct_file, "Find migrations didn't find the sample migration file when an extension was specified");
		$this->assertTrue(count($files) == 1, "Find migrations found too many files when an extension was specified");
	}

	/**
	 * Test that instantiation successfully gets a DBO
	 **/
	function testConstructGetsDbo()
	{
		$this->assertTrue(is_object($this->instance->get('db')), "Object doesn't have a DBO (see log for cause)");
		$this->assertTrue($this->instance->get('db') instanceof JDatabasePDO, "DBO isn't instance of JDatabasePDO");
	}

	/**
	 * Test running 'up' creates sample table from ComMigrations20130101.php
	 **/
	function testUpCreatesSampleTable()
	{
		// Find files
		$this->instance->find();

		// Run up migration
		$this->instance->migrate('up', true);

		// Check for existance of sample table
		try
		{
			$this->instance->get('db')->setQuery("SELECT * FROM `#__migrations_sample_table`");
			$result = $this->instance->get('db')->query();
		}
		catch (PDOException $e)
		{
			$result = null;
		}

		$this->assertNotNull($result, "Running 'up' didn't create sample table: '#__migrations_sample_table'");
	}

	/**
	 * Test database log entry was created for running up
	 **/
	function testUpCreatesDatabaseLogRecord()
	{
		// Check for log entry from sample table run
		try
		{
			// Path of file we should expect to find in the database log entry
			$file = 'Migration20130101000000ComGroups.php';
			$date = JFactory::getDate()->toSql();

			$query  = "SELECT * FROM migrations WHERE `file` = ";
			$query .= $this->instance->get('db')->Quote($file);
			$query .= " AND `date` = " . $this->instance->get('db')->Quote($date);

			// Prepare and execute query
			$this->instance->get('db')->setQuery($query);
			$this->instance->get('db')->query();

			// Get result
			$count = $this->instance->get('db')->getNumRows();
		}
		catch (PDOException $e)
		{
			$count = 0;
		}

		$this->assertTrue($count > 0, "Running 'up' didn't create migrations log entry");
	}

	/**
	 * Test php log entry created by running up
	 **/
	function testUpCreatesPhpLogEntry()
	{
		$log = shell_exec("tail -4 " . ini_get('error_log'));

		$this->assertRegExp('/^\['.date("d-M-Y H:i:s").'\] running up().*Migration20130101000000ComGroups\.php/i', $log, "Running 'up' didn't create PHP log entry");
	}

	/**
	 * Test running up again doesn't do anything
	 **/
	function testUpAgainDoesNothing()
	{
		// Find files
		$this->instance->find();

		// Run up migration
		$this->instance->migrate('up');

		// Initialize count
		$count = 0;

		// Check for log entry from sample table run
		try
		{
			// Path of file we should expect to find in the database log entry
			$file = 'Migration20130101000000ComGroups.php';
			$date = JFactory::getDate()->toSql();

			$query  = "SELECT * FROM migrations WHERE `file` = ";
			$query .= $this->instance->get('db')->Quote($file);
			$query .= " AND `date` = " . $this->instance->get('db')->Quote($date);

			// Prepare and execute query
			$this->instance->get('db')->setQuery($query);
			$this->instance->get('db')->query();

			// Get result
			$count = $this->instance->get('db')->getNumRows();
		}
		catch (PDOException $e)
		{
			// Do nothing
		}

		$this->assertTrue($count === 1, "Running 'up' again created another entry");
	}

	/**
	 * Test running 'down' drops sample table from ComMigrations20130101.php
	 **/
	function testDownDropsSampleTable()
	{
		// Find files
		$this->instance->find();

		// Run down migration
		$this->instance->migrate('down', true);

		// Check for lack of existance of sample table
		try
		{
			$this->instance->get('db')->setQuery("SELECT * FROM `#__migrations_sample_table`");
			$result = $this->instance->get('db')->query();
		}
		catch (PDOException $e)
		{
			$result = false;
		}

		$this->assertFalse($result, "Running 'down' didn't drop sample table: '#__migrations_sample_table'");
	}

	/**
	 * Test cleanup database log entries
	 *
	 * @FIXME: I'm sure there's a more 'correct' way to do this (test suite tear down?)
	 **/
	function testCleanupDbLogEntries()
	{
		// Check for log entry from sample table run
		try
		{
			// Files we should expect to find in the database log entry
			$file   = array();
			$file[] = 'Migration20130101000000ComGroups.php';
			$file[] = 'Migration20130115000000ModMygroups.php';
			$file[] = 'Migration20130125000000PlgMembersAccount.php';

			// Prepare and execute query
			$query  = "DELETE FROM migrations WHERE `file` = ";
			$query .= $this->instance->get('db')->Quote($file[0]);
			$query .= " OR `file` = " . $this->instance->get('db')->Quote($file[1]);
			$query .= " OR `file` = " . $this->instance->get('db')->Quote($file[2]);

			// Prepare and execute query
			$this->instance->get('db')->setQuery($query);
			$this->instance->get('db')->query();
		}
		catch (PDOException $e)
		{
			$this->instance->log('Test cleanup failed');
		}

		$this->instance->log('PHPUnit: Successfully removed database entries created during testing');
	}
}