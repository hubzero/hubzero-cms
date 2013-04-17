<?php
/**
 * Test class for HUBzero migrate command line utility
 * 
 * @author Sam Wilson <samwilson@purdue.edu>
 */

/**
 * Test class for HUBzero migration command line runner
 */
class MigrateUtilityTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test --help flag brings up help info
	 */
	function testMigrateWithHelpFlagShowsHelpInfo()
	{
		$output = shell_exec("migrate run --help");

		$this->assertRegExp('/^[\s]*usage: migrate.*/is', $output, "Migrate execution with --help flag did not result in help information being displayed");
	}

	/**
	 * Test that -d equal to something other than 'up' or 'down' gives an error
	 */
	function testMigrateWithBadDirectionGivesError()
	{
		$output = shell_exec("migrate run -d=badarg -n");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with -d flag set to non up|down should give an error, but doesn't");
	}

	/**
	 * Test that specifying a bad document root gives an error
	 */
	function testMigrateWithBadDocumentRootGivesError()
	{
		$output = shell_exec("migrate run -r=\"/baddir/baddir\" -n");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with invalid document root should give an error, but doesn't");
	}

	/**
	 * Test that supplying the force (-f) flag without an extension specified throws an error
	 */
	function testMigrateWithForceFlagButNoExtensionGivesError()
	{
		$output = shell_exec("migrate run -f -n");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with force flag but no extension specified should give an error, but doesn't");
	}

	/**
	 * Test that supplying a bad extension gives an error
	 */
	function testMigrateWithBadExtensionGivesError()
	{
		$output   = array();
		$output[] = shell_exec("migrate run -e=com_courses_blah -n");
		$output[] = shell_exec("migrate run -e=mod_courses_blah -n");
		$output[] = shell_exec("migrate run -e=plg_blah -n");

		foreach ($output as $o)
		{
			$this->assertRegExp('/^[\s]*Error.*/is', $o, "Migrate execution with improper extension should give an error, but doesn't");
		}
	}
}