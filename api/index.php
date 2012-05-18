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

$_HUBZERO_API_START = microtime(true);

ini_set('display_errors','0');
error_reporting(E_ALL);
header("HTTP/1.1 404 Not Found");

if (function_exists('xdebug_disable'))
{
    xdebug_disable();
}

ini_set('magic_quotes_runtime','0');
ini_set('zend.ze1_compatibility_mode', '0');
ini_set('zlib.output_compression','0');
ini_set('output_hander','');
ini_set('implicit_flush','0');

/**
 * Description for ''_JEXEC''
 */
define('_JEXEC', 1);

/**
 * Description for ''DS''
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Description for ''JPATH_ROOT''
 */
define('JPATH_ROOT', dirname( dirname(__FILE__) ));

/**
 * Description for ''JPATH_BASE''
 */
define('JPATH_BASE', JPATH_ROOT);

/**
 * Description for ''JPATH_SITE''
 */
define('JPATH_SITE', JPATH_ROOT);

/**
 * Description for ''JPATH_CONFIGURATION''
 */
define('JPATH_CONFIGURATION', JPATH_ROOT);

/**
 * Description for ''JPATH_INSTALLATION''
 */
define('JPATH_INSTALLATION', JPATH_ROOT . DS . 'installation');

/**
 * Description for ''JPATH_ADMINISTRATOR''
 */
define('JPATH_ADMINISTRATOR', JPATH_ROOT . DS . 'administrator');

/**
 * Description for ''JPATH_API''
 */
define('JPATH_API', JPATH_ROOT . DS . 'api');

/**
 * Description for ''JPATH_XMLRPC''
 */
define('JPATH_XMLRPC', JPATH_ROOT . DS . 'xmlrpc');

/**
 * Description for ''JPATH_LIBRARIES''
 */
define('JPATH_LIBRARIES', JPATH_ROOT . DS . 'libraries');

/**
 * Description for ''JPATH_PLUGINS''
 */
define('JPATH_PLUGINS', JPATH_ROOT . DS . 'plugins');

/**
 * Description for ''JPATH_CACHE''
 */
define('JPATH_CACHE', JPATH_BASE . DS . 'cache');

require(JPATH_LIBRARIES.DS.'loader.php');
require(JPATH_LIBRARIES.DS.'Hubzero'.DS.'loader.php');

JLoader::import('joomla.base.object');
JLoader::import('joomla.factory');
JLoader::import('joomla.environment.uri');
JLoader::import('joomla.error.exception');
JLoader::import('joomla.error.error');

JError::setErrorHandling(E_ERROR, 'ignore');
JError::setErrorHandling(E_WARNING, 'ignore');
JError::setErrorHandling(E_NOTICE, 'ignore');

$mainframe = & JFactory::getApplication('api',array(),'Hubzero_');

$mainframe->initialise();

$mainframe->route();

$mainframe->authenticate();

$mainframe->dispatch();

$mainframe->render();
