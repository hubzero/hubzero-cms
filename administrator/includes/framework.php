<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/*
 * Joomla! system checks.
 */

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/*
 * Installation check, and check on removal of the install directory.
 */
if (!file_exists( JPATH_CONFIGURATION . DS . 'configuration.php' ) ) {
	echo 'No configuration file found. Exiting...';
	exit();
}

//
// Joomla system startup.
//

// System includes.
require_once JPATH_LIBRARIES.'/import.php';

// Force library to be in JError legacy mode
JError::$legacy = true;
JError::setErrorHandling(E_NOTICE, 'message');
JError::setErrorHandling(E_WARNING, 'message');
JError::setErrorHandling(E_ERROR, 'message', array('JError', 'customErrorPage'));

// Botstrap the CMS libraries.
require_once JPATH_LIBRARIES.'/cms.php';

// Pre-Load configuration.
ob_start();
require_once JPATH_CONFIGURATION.'/configuration.php';
ob_end_clean();

if (!class_exists('JConfig'))
{
	echo 'Invalid configuration file. Exiting...';
	exit();
}

// System configuration.
$config = new JConfig();

/* if configuration just has an install key and no other properties then redirect into the installer */

if (count(get_object_vars($config)) <= 1)
{
	if( file_exists( JPATH_INSTALLATION . DS . 'index.php' ) ) {
		header( 'Location: installation/index.php' );
		exit();
	} else {
		echo 'No installation code available. Exiting...';
		exit();
	}
}

// Set the error_reporting
switch ($config->error_reporting)
{
	case 'default':
	case '-1':
		break;

	case 'none':
	case '0':
		error_reporting(0);
		break;

	case 'simple':
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		ini_set('display_errors', 1);
		break;

	case 'maximum':
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		break;

	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
		break;

	default:
		error_reporting($config->error_reporting);
		ini_set('display_errors', 1);
		break;
}

if (!isset($config->profile))
{
        $config->profile = 0;
}

define('JDEBUG', $config->debug);
define('JPROFILE', $config->debug || $config->profile);

unset($config);

/*
 * Joomla! framework loading.
 */

// System profiler.

if (JPROFILE) {
	jimport('joomla.error.profiler');
	$_PROFILER = JProfiler::getInstance('Application');
}

// Joomla! library imports.
jimport('joomla.application.menu');
jimport('joomla.environment.uri');
jimport('joomla.html.parameter');
jimport('joomla.utilities.utility');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.arrayhelper');
