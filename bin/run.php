#!/usr/bin/php

<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
 * @TODO: add flag to accept date or date range
 * @TODO: add flag to target specific file
 */

// Ensure we're running from the command line
if (php_sapi_name() != 'cli')
{
	die('Restricted access');
}

// Declare our options
$shortopts  = "";
$shortopts .= "d::"; // direction (up|down)
$shortopts .= "r::"; // document root
$shortopts .= "e::"; // specific extension to run on
$shortopts .= "l::"; // error logging type
$shortopts .= "i";   // run migrate on a full scan of the file system, not just those files with dates after last run
$shortopts .= "f";   // override dry run and actually run the full migration
$shortopts .= "m";   // log migration only (don't actually run)
$shortopts .= "p";   // print contents of migration (don't run)
$shortopts .= "h";   // short code for help menu

$longopts   = array();
$longopts[] = "force";   // force update (irrelevent of whether or not the database thinks it's already been run)
$longopts[] = "email::"; // provide an email address to email log info to
$longopts[] = "print";   // long option for print
$longopts[] = "help";    // long option for help menu

// Grab the options
$options = getopt($shortopts, $longopts);

// Documentation
if(isset($options['help']) || isset($options['h']))
{
	showHelp();
	exit();
}

// Check arguments and establish defaults
// Direction, up or down
$direction = 'up';
if(isset($options['d']) && $options['d'] !== false)
{
	if($options['d'] == 'up' || $options['d'] == 'down')
	{
		$direction = $options['d'];
	}
	else
	{
		echo 'Error: Direction must be one of "up" or "down"' . "\n\n";
		exit();
	}
}

// Overriding default document root?
$directory = null;
if(isset($options['r']) && $options['r'] !== false)
{
	if(is_dir($options['r']) && is_readable($options['r']))
	{
		$directory = rtrim($options['r'], '/');
	}
	else
	{
		echo 'Error: Provided directory is not valid' . "\n\n";
		exit();
	}
}

// Forcing update
$force = false;
if(isset($options['force']))
{
	if(!isset($options['e']))
	{
		echo 'Error: You cannot specify the "force" option without specifying a specific extention.' . "\n\n";
		exit();
	}
	else
	{
		$force = true;
	}
}

// Logging only - record migration
$logOnly = false;
if(isset($options['m']))
{
	if(!isset($options['e']))
	{
		echo 'Error: You cannot specify the "Log only (-m)" option without specifying a specific extention.' . "\n\n";
		exit();
	}
	else
	{
		$logOnly = true;
	}
}

// Ignore dates
$ignoreDates = false;
if(isset($options['i']))
{
	$ignoreDates = true;
}

// Print contents
$print = false;
if(isset($options['p']) || isset($options['print']))
{
	$print = true;
}

// Specific extension
$extension = null;
if (isset($options['e']) && $options['e'] !== false)
{
	if (!preg_match('/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|^plg_[[:alnum:]]+_[[:alnum:]]+$|^core$/i', $options['e']))
	{
		echo 'Error: extension should match the pattern of com_*, mod_*, plg_*_*, or core.' . "\n\n";
		exit();
	}
	else
	{
		$extension = $options['e'];
	}
}

// Dryrun
$dryrun = true;
if(isset($options['f']))
{
	$dryrun = false;
}

// Email results
$email = false;
if(isset($options['email']) && !is_null($options['email']))
{
	if(!preg_match('/^[a-zA-Z0-9\.\_\-]+@[a-zA-Z0-9\.]+\.[a-zA-Z]{2,4}$/', $options['email']))
	{
		echo "Error: '{$options['email']}' does not appear to be a valid email address.\n\n";
		exit();
	}
	else
	{
		$email = $options['email'];
	}
}

// Log type
$log = array('stdout');
// If email is set, we have to include internal_log
if($email)
{
	$log[] = 'internal_log';
}
if(isset($options['l']) && $options['l'] !== false)
{
	$log[] = $options['l'];
	$log = array_unique($log);
}

$found = false;

// Include our migrations class
// Find the doc root to pull the migration class from
if (!is_null($directory))
{
	// A document root was provided
	$migrationClass = "{$directory}/libraries/Hubzero/Migration.php";

	if (is_file($migrationClass))
	{
		$found = true;
	}
}

if (is_null($directory) || !$found)
{
	if (is_file(dirname(__FILE__) . "/../libraries/Hubzero/Migration.php"))
	{
		$migrationClass = dirname(__FILE__) . "/../libraries/Hubzero/Migration.php";
	}
	else
	{
		$conf = '/etc/hubzero.conf';
		if (is_file($conf) && is_readable($conf))
		{
			$content = file_get_contents($conf);
			preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);
			if (isset($matches[1]))
			{
				$docroot = $matches[1];
				$migrationClass = rtrim($docroot, '/') . '/libraries/Hubzero/Migration.php';
			}
			else
			{
				echo "Error: could not find the document root in the configuration file.\n\n";
				exit();
			}
		}
		else
		{
			echo "Error: could not find the Hubzero configuration file. Try providing a document root using the '-r' option.\n\n";
			exit();
		}
	}
}

if (is_file($migrationClass))
{
	define('_JEXEC', 1);
	require_once $migrationClass;
}
else
{
	echo "Error: could not find the Migration class.\n\n";
	exit();
}

// Create migration object
$migration = new Hubzero_Migration($directory, $log);

// Make sure we got a migration object
if($migration === false)
{
	exit();
}

// Find migration files
echo "Running migration...";
if($migration->find($extension) === false)
{
	// Find failed, do nothing
	$migration->log("Migration find failed! See log messages for details.");
}
else // no errors during 'find', so continue
{
	// @TODO: add count of applicable migrations...not just total migrations
	//echo count($migration->get('files')) . " migrations found.";
	echo "\n";

	// Run migration itself
	if(!$result = $migration->migrate($direction, $force, $dryrun, $ignoreDates, $logOnly, $print))
	{
		$migration->log("Migration failed! See log messages for details.");
	}
	else
	{
		$migration->log("Success: " . ucfirst($direction) . " migration complete!");
	}
}

if (isset($result) && is_array($result) && $print)
{
	foreach ($result as $k)
	{
		echo "\n".$k['filename'].":\n";
		echo "\n".$k['content']."\n";
	}
}

// Email results if requested
if($email)
{
	echo "Emailing results to: {$email}\n\n";

	$headers = "From: Migrations <automator@" . php_uname("n") . ">";
	$subject = "Migration output - " . php_uname("n") . " [" . date("d-M-Y H:i:s") . "]";
	$message = implode("\n", $migration->get('internal_log'));

	// Send the message
	if(!mail($email, $subject, $message, $headers))
	{
		"Error: failed to send message!\n";
	}
}

// --------------------------------------------- //

// Help documentation
function showHelp()
{
	$help  = "";
	$help .= " usage: migration run [args]\n";
	$help .= "\n";
	$help .= " Overview:\n";
	$help .= "       Run a migration. This includes searching for migration files,\n";
	$help .= "       depending on the options provided.\n";
	$help .= "       \n";
	$help .= " Arguments:\n";
	$help .= "   -d: direction [up|down]\n";
	$help .= "       If not specified, defaults to 'up'.\n";
	$help .= "       Example: -d=up, -d=\"down\".\n";
	$help .= "       \n";
	$help .= "   -r: document root\n";
	$help .= "       Specify the document root through which the the application\n";
	$help .= "       will search for migrations directories.  The primary use case\n";
	$help .= "       for this is specifying an alternate directory for testing.\n";
	$help .= "       By default, it will look in the /etc/hubzero.conf file for\n";
	$help .= "       the document root specified there.\n";
	$help .= "       Example: -r=\"/www/myhub/unittests/migrations\"\n";
	$help .= "       \n";
	$help .= "   -e: extension\n";
	$help .= "       Explicity give the extension on which the migration should be run.\n";
	$help .= "       This could be one of 'com_componentname', 'mod_modulename',\n";
	$help .= "       or 'plg_plugingroup_pluginname'. This option is required \n";
	$help .= "       when using the force (--force) option and the log only option (-m).\n";
	$help .= "       Example: -e=com_courses, -e=\"plg_members_dashboard\"\n";
	$help .= "       \n";
	$help .= "   -i: ignore dates\n";
	$help .= "       Using this option will scan for and run all migrations that haven't\n";
	$help .= "       previously been run, irrespective of the date of the migration.\n";
	$help .= "       This differs from the default behavior in that normally, only files\n";
	$help .= "       dated after the last run date will be eligable to be included in the\n";
	$help .= "       migration. This option also differs from force mode (--force) in that it\n";
	$help .= "       will find all migrations, but only run those that haven't been run\n";
	$help .= "       before (whereas --force will run them irrespective of whether or not it\n";
	$help .= "       thinks they've already been run). You do not have to use -e with this\n";
	$help .= "       option. This option may be usefill in checking if any migrations have\n";
	$help .= "       missed over the course of time.\n";
	$help .= "       \n";
	$help .= "   -f: full run\n";
	$help .= "       By default, using the migration command without any options will run\n";
	$help .= "       in dry-run mode (meaning no changes will actually be made), displaying\n";
	$help .= "       the migrations that would be run, were the command to be fully executed\n";
	$help .= "       Use the '-f' (full run) option to do the full migration run.\n";
	$help .= "       \n";
	$help .= "   -m: log only\n";
	$help .= "       Using this option, a migration will run as normal, and log entries\n";
	$help .= "       will be created, but the SQL itself will not be run. As a general\n";
	$help .= "       precaution, this should not be run without the extension option (-e).\n";
	$help .= "       The primary use case for this option would be marking a migration\n";
	$help .= "       as run in the event that it had already been run (manually), yet\n";
	$help .= "       not logged in the database.\n";
	$help .= "       \n";
	$help .= "   -p, --print: print\n";
	$help .= "       Simply print contents of effected migration files. This will not\n";
	$help .= "       actually run the migrations returned by the command. This should\n";
	$help .= "       most likely be used in combination with other options in order\n";
	$help .= "       to limit the number of results returned. This command is also\n";
	$help .= "       available as a long option (--print).\n";
	$help .= "       \n";
	$help .= "   -l: log type [error_log|stdout|internal_log]\n";
	$help .= "       Include additional logging mechanisms. Options include STDOUT,\n";
	$help .= "       PHP error log, and an internal log. STDOUT is included by default\n";
	$help .= "       when running this program from the command line, and internal log\n";
	$help .= "       will also be included when you specify an email address.\n";
	$help .= "       Example: -l=error_log\n";
	$help .= "       \n";
	$help .= "   --force: force mode\n";
	$help .= "       This option should be used carefully. It will run a migration,\n";
	$help .= "       even if it thinks it has already been run. When using this option,\n";
	$help .= "       you must also give a specific extension using the (-e) option.\n";
	$help .= "       \n";
	$help .= "   --email\n";
	$help .= "       Specify an email address to receive the output of this run.\n";
	$help .= "       Example: --email=sampleuser@hubzero.org\n";
	$help .= "       \n";
	$help .= "   -h, --help\n";
	$help .= "       Show help documentation\n";
	$help .= "       \n";

	echo $help;
}
