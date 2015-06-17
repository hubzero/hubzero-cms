<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_JEXEC') or die;
/*
error_reporting(-1);
ini_set('error_reporting', E_ALL);*/
header("HTTP/1.1 404 Not Found");

if (function_exists('xdebug_disable'))
{
	xdebug_disable();
}

ini_set('zlib.output_compression','0');
ini_set('output_handler','');
ini_set('implicit_flush','0');

//
// Joomla system startup.
//

// System includes.
require_once JPATH_ROOT . '/core/bootstrap/autoload.php';

JLoader::import('cms.version.version');

//$jversion = new JVersion;
//define('JVERSION', $jversion->getShortVersion());
//unset($jversion);

JLoader::import('joomla.error.error');
JLoader::import('joomla.factory');
JLoader::import('joomla.base.object');
