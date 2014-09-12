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

// Run the command (we'll assume the scripts are all in the same directory)
echo shell_exec(dirname(__FILE__) . "/{$command}.php {$options}");

// Done
exit();