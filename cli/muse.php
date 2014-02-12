#!/usr/bin/php
<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
| ===================
| Include custom shim
| ===================
|
| All this is really doing is bootstrapping the Joomla framework
| Maybe we should just make a cli application environment?
|
*/

require __DIR__ . '/shim.php';

/*
| ============================
| Start up console application
| ============================
|
| Start by getting an instance of our console application.
| This will handle our command execution and rendering of the response.
| We'll pass in the argument parser class and our output class.
|
*/

$arguments = new Hubzero\Console\Arguments($argv);
$output    = new Hubzero\Console\Output();
$muse      = new Hubzero\Console\Application($arguments, $output);

/*
| ===================
| Execute the command
| ===================
|
| This will process the command line vars and execute the given command.
|
*/

$muse->execute();

/*
| ====
| Exit
| ====
|
| We're all done!  Go home...call it a day.
|
*/

exit();