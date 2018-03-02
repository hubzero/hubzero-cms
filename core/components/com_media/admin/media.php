<?php

namespace Components\Media\Admin;

if (!\User::authorise('core.manage', 'com_media'))
{
	return \App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$params = Component::params('com_media');

require_once dirname(__DIR__) . DS . 'models' . DS . 'files.php';
require_once __DIR__ . '/helpers/media.php';
require_once __DIR__ . DS . 'helpers' . DS . 'media.php';

$view = Request::getCmd('view');
$controllerName = \Request::getCmd('controller', 'media_test');

define('COM_MEDIA_BASE', PATH_APP . '/' . $params->get($path, 'site/media'));
define('COM_MEDIA_BASEURL', rtrim(Request::root(), '/') . substr(PATH_APP, strlen(PATH_ROOT)) . '/' . $params->get($path, 'site/media'));

\Submenu::addEntry(
	\Lang::txt('Thumbnail View'),
	\Route::url('index.php?option=com_media&controller=media'),
	($controllerName == 'media')
);

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'media';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
