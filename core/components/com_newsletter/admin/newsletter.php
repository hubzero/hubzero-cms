<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin;

if (!\User::authorise('core.manage', 'com_newsletter'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'newsletter.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailinglist.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailing.php';

// Include helpers
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

// Instantiate controller
$controllerName = \Request::getCmd('controller', 'newsletters');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	return \App::abort(404, \Lang::txt('JERROR_INVALID_CONTROLLER'));
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Menu items
$menuItems = array(
	'newsletters'  => \Lang::txt('COM_NEWSLETTER_NEWSLETTERS'),
	'mailings'     => \Lang::txt('COM_NEWSLETTER_MAILINGS'),
	'mailinglists' => \Lang::txt('COM_NEWSLETTER_LISTS'),
	'templates'    => \Lang::txt('COM_NEWSLETTER_TEMPLATES'),
	'tools'        => \Lang::txt('COM_NEWSLETTER_TOOLS')
);

foreach ($menuItems as $k => $v)
{
	$active = (\Request::getCmd('controller', 'newsletters') == $k) ? true : false;
	\Submenu::addEntry($v, \Route::url('index.php?option=com_newsletter&controller=' . $k), $active);
}

// Execute controller
$controller = new $controllerName();
$controller->execute();
