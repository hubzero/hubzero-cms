<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin;

if (!\User::authorise('core.manage', 'com_media'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$params = \Component::params('com_media');
$path = trim($params->get('file_path', 'site/media'), '/');
$path = $path ? $path . '/' : '';

define('COM_MEDIA_BASE', PATH_APP . '/' . $path);

$baseurl = rtrim(\Request::root(), '/') . substr(COM_MEDIA_BASE, strlen(PATH_ROOT));
define('COM_MEDIA_BASEURL', $baseurl);

require_once __DIR__ . DS . 'helpers' . DS . 'media.php';

$controllerName = \Request::getCmd('controller', 'media_test');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'media';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
