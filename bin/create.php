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

// Ensure we're running from the command line
if (php_sapi_name() != 'cli')
{
	die('Restricted access');
}

// Declare our options
$shortopts  = "";
$shortopts .= "e:"; // extension
$shortopts .= "h";   // help

$longopts   = array();
$longopts[] = "extension:"; // long option for specifying extension
$longopts[] = "editor:";    // long option for specifying editor for file
$longopts[] = "help";       // long option for help documentation

// Grab the options
$options = getopt($shortopts, $longopts);

// Documentation
if(isset($options['help']) || isset($options['h']))
{
	showHelp();
	exit();
}

// Extension
$extension = null;
if((isset($options['e']) && $options['e'] !== false) || (isset($options['extension']) && $options['extension'] !== false))
{
	$extension = (isset($options['e']) && $options['e'] !== false) ? $options['e'] : $options['extension'];

	if ($extension != 'core' && !validExtension($extension))
	{
		echo "Error: the extension provided ({$extension}) does not appear to be valid.\n\n";
		exit();
	}
}
else
{
	echo "Error: an extension should be provided.\n\n";
	exit();
}

// Editor
$editor = null;
if(isset($options['editor']) && $options['editor'] !== false)
{
	$editor = $options['editor'];
}
else
{
	$editor = (getenv('EDITOR')) ? getenv('EDITOR') : 'vi';
}

// Create filename varient of extension
$ext = '';
if (!preg_match('/core/i', $extension))
{
	$parts = explode('_', $extension);
	foreach ($parts as $part)
	{
		$ext .= ucfirst($part);
	}
}
else
{
	$ext = 'Core';
}

// Get document root
$docroot = getDocroot();

// Make sure a timezone is set
if (!ini_get('date.timezone'))
{
	date_default_timezone_set('UTC');
}

// Craft file/classname
$classname = 'Migration' . date("YmdHis") . $ext;
$filename  = $docroot . '/migrations/' . $classname . '.php';

// Copy the template file to our new file
$template = "{$docroot}/migrations/__migration.tmpl";
if (!copy($template, $filename))
{
	echo "Error: an problem occured copying {$template} to {$filename}.\n\n";
	exit();
}

// Replace variables
$contents = file_get_contents($filename);
$contents = str_replace('%=class_name=%', $classname, $contents);

// Write file
file_put_contents($filename, $contents);

// Open in editor
system("{$editor} {$filename} > `tty`");

echo "New migration script '{$filename}' successfully created!\n\n";
exit();

// --------------------------------------------- //

function validExtension($extension)
{
	$ext = explode("_", $extension);
	$dir = '';

	switch ($ext[0])
	{
		case 'com':
			$dir = getDocroot() . '/components/' . $extension;
		break;
		case 'mod':
			$dir = getDocroot() . '/modules/' . $extension;
		break;
		case 'plg':
			$dir = getDocroot() . '/plugins/' . $ext[1] . '/' . $ext[2];
		break;
	}

	return (is_dir($dir)) ? true : false;
}

function getDocroot()
{
	// Find the doc root to pull the migration class from
	$conf = '/etc/hubzero.conf';

	if (is_dir(dirname(dirname(__FILE__)) . "/migrations"))
	{
		return dirname(dirname(__FILE__));
	}
	elseif (is_file($conf) && is_readable($conf))
	{
		$content = file_get_contents($conf);
		preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);
		if (isset($matches[1]) && is_dir($matches[1]))
		{
			return rtrim($matches[1], '/');
		}
	}
	else
	{
		echo "Error: could not find the Hubzero configuration file, or make a reasonable guess at the document root.\n\n";
		exit();
	}
}

// Help documentation
function showHelp()
{
	$help  = "";
	$help .= " usage: migration create [args]\n";
	$help .= "\n";
	$help .= " Overview:\n";
	$help .= "       Create a migration script from the default template. An\n";
	$help .= "       extension should be provided.\n";
	$help .= "       \n";
	$help .= " Arguments:\n";
	$help .= "   -e, --extension: extension\n";
	$help .= "       Speicify the extension for which you are creating a migration\n";
	$help .= "       script. Those scripts not pertaining to a specific extension\n";
	$help .= "       should be given the extension 'core'.\n";
	$help .= "       \n";
	$help .= "   --editor: editor\n";
	$help .= "       Speicify the editor to use in creating the migration file.\n";
	$help .= "       \n";
	$help .= "   -h, --help\n";
	$help .= "       Show help documentation\n";
	$help .= "       \n";

	echo $help;
}