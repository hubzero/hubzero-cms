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
		$output = shell_exec("migration run --help");

		$this->assertRegExp('/^[\s]*usage: migration.*/is', $output, "Migrate execution with --help flag did not result in help information being displayed");
	}

	/**
	 * Test that -d equal to something other than 'up' or 'down' gives an error
	 */
	function testMigrateWithBadDirectionGivesError()
	{
		$output = shell_exec("migration run -d=badarg");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with -d flag set to non up|down should give an error, but doesn't");
	}

	/**
	 * Test that specifying a bad document root gives an error
	 */
	function testMigrateWithBadDocumentRootGivesError()
	{
		$output = shell_exec("migration run -r=\"/baddir/baddir\"");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with invalid document root should give an error, but doesn't");
	}

	/**
	 * Test that supplying the force (-f) flag without an extension specified throws an error
	 */
	function testMigrateWithForceFlagButNoExtensionGivesError()
	{
		$output = shell_exec("migration run --force");

		$this->assertRegExp('/^[\s]*Error.*/is', $output, "Migrate execution with force flag but no extension specified should give an error, but doesn't");
	}

	/**
	 * Test that supplying a bad extension gives an error
	 */
	function testMigrateWithBadExtensionGivesError()
	{
		$output   = array();
		$output[] = shell_exec("migration run -e=com_courses_blah");
		$output[] = shell_exec("migration run -e=mod_courses_blah");
		$output[] = shell_exec("migration run -e=plg_blah");

		foreach ($output as $o)
		{
			$this->assertRegExp('/^[\s]*Error.*/is', $o, "Migrate execution with improper extension should give an error, but doesn't");
		}
	}
}