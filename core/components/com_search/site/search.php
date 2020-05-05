<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Site;

$config = \Component::params('com_search');

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', $config->get('engine', 'basic')));

if ($controllerName != 'basic')
{
	$controllerName = 'solr';
}

// Are we falling back to the default engine?
$fallback = \App::get('session')->get('searchfallback');
if ($fallback && intval($fallback) <= time())
{
	// Don't fallback if the time limit has expired
	$fallback = null;
}

// Are we explicitly forcing the engine?
if ($force = \Request::getCmd('engine'))
{
	$fallback = null;
	$controllerName = $force;
}

if ($fallback || !file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'basic';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
