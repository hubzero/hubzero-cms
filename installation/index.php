<?php
/**
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
error_log("");
error_log($_SERVER['SCRIPT_NAME']);
error_log("");

// PHP 5 check
if (version_compare(PHP_VERSION, '5.2.4', '<')) {
	die('Your host needs to use PHP 5.2.4 or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 */
define('_JEXEC', 1);

/**
 * Constant that defines the base path of the installed Joomla site.
 */
define('JPATH_BASE', dirname(__FILE__));

// Set path constants.
$parts = explode(DIRECTORY_SEPARATOR, JPATH_BASE);
array_pop($parts);

define('JPATH_ROOT',			implode(DIRECTORY_SEPARATOR, $parts));
define('JPATH_SITE',			JPATH_ROOT);
define('JPATH_CONFIGURATION',	JPATH_ROOT);
define('JPATH_ADMINISTRATOR',	JPATH_ROOT . '/administrator');
define('JPATH_LIBRARIES',		JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS',			JPATH_ROOT . '/plugins');
define('JPATH_INSTALLATION',	JPATH_ROOT . '/installation');
define('JPATH_THEMES',			JPATH_BASE);
define('JPATH_CACHE',			JPATH_ROOT . '/cache');
define('JPATH_MANIFESTS',		JPATH_ADMINISTRATOR . '/manifests');

/*
 * Joomla system checks.
 */
error_reporting(E_ALL);
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/*
 * Check for existing configuration file.
 */
/*
if (file_exists(JPATH_CONFIGURATION.'/configuration.php') && (filesize(JPATH_CONFIGURATION.'/configuration.php') > 10) && !file_exists(JPATH_INSTALLATION.'/index.php')) {
	header('Location: ../index.php');
	exit();
}
*/

if (file_exists(JPATH_CONFIGURATION.'/configuration.php'))
{
	ob_start();
	require_once(JPATH_CONFIGURATION.'/configuration.php');
	ob_end_clean();

	if (class_exists('JConfig'))
	{
		// System configuration
		$CONFIG = new JConfig();

		if (count(get_object_vars($CONFIG)) > 1)
		{
			header( 'Location: ..' );
			exit();
		}

		if (empty($CONFIG->installkey))
		{
			echo 'Invalid configuration file. Exiting...';
			exit();
		}
	}
	else
	{
		echo 'Invalid configuration file. Exiting...';
		exit();
	}
}
else
{
	echo 'No configuration file found. Exiting...';
	exit();
}

//
// Joomla system startup.
//

// Bootstrap the Joomla Framework.
require_once JPATH_LIBRARIES.'/import.php';

// Botstrap the CMS libraries.
require_once JPATH_LIBRARIES.'/cms.php';

require_once JPATH_INSTALLATION."/models/rules/prefix.php";    // class JFormRulePrefix extends JFormRule
require_once JPATH_INSTALLATION."/models/fields/prefix.php";   // class JFormFieldPrefix extends JFormField
require_once JPATH_INSTALLATION."/models/fields/sample.php";   // class JFormFieldSample extends JFormFieldRadio
require_once JPATH_INSTALLATION."/models/fields/language.php"; // class JFormFieldLanguage extends JFormFieldList

// Joomla library imports.
jimport('joomla.database.table');
jimport('joomla.environment.uri');
jimport('joomla.utilities.arrayhelper');

// Create the application object.
$app = JFactory::getApplication('installation');

// Initialise the application.
$app->initialise();

// Render the document.
$app->render();

// Return the response.
echo $app;
