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

// Grab the command and the arguments
if (isset($argv[1]))
{
	switch ($argv[1])
	{
		case 'create':
			$command = 'create';
			$opts    = $argv;
			unset($opts[0]);
			unset($opts[1]);
		break;

		case 'run':
			$command = 'run';
			$opts    = $argv;
			unset($opts[0]);
			unset($opts[1]);
		break;

		default:
			$command = 'run';
			$opts    = $argv;
			unset($opts[0]);
		break;
	}
}
else
{
	$command = 'run';
	$opts    = $argv;
	unset($opts[0]);
}

// Put the options back together in a string
$options = implode(" ", $opts);
$command = escapeshellcmd($command);

// Run the command
echo shell_exec(getDocroot() . "/bin/{$command}.php {$options}");

// Done
exit();

// --------------------------------------------- //

function getDocroot()
{
	// Find the doc root to pull the migration class from
	$conf = '/etc/hubzero.conf';
	if (is_file($conf) && is_readable($conf))
	{
		$content = file_get_contents($conf);
		preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);
		if (isset($matches[1]))
		{
			return rtrim($matches[1], '/');
		}
		else
		{
			echo "Error: could not find the document root in the configuration file.\n\n";
			exit();
		}
	}
	else
	{
		echo "Error: could not find the Hubzero configuration file.\n\n";
		exit();
	}
}