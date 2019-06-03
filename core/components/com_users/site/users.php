<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$task = Request::getCmd('task');

if (strstr($task, '.'))
{
	$task = explode('.', $task);
	$task = end($task);
}

$uri = new Hubzero\Utility\Uri(Request::current());
$uri ->setQuery(Request::query());

switch ($task)
{
	case 'reset':
	case 'remind':
	case 'unapproved':
	case 'userconsent':
		$uri->setVar('option', 'com_members');

		$url = $uri->toString();

		$redirect = new Hubzero\Http\RedirectResponse($url, 301);
		$redirect->setRequest(App::get('request'));
		$redirect->send();
	break;

	case 'logout':
	case 'factors':
	case 'userconsent':
	case 'link':
	case 'endsinglesignon':
	case 'spamjail':
	case 'login':
		//$uri->setVar('option', 'com_login');
		//$uri->setVar('task', $task);
		Request::setVar('option', 'com_login');
		Request::setVar('task', $task);
	break;

	default:
		//$uri->setVar('option', 'com_login');
		//$uri->delVar('task');
		Request::setVar('option', 'com_login');
		Request::setVar('task', '');
	break;
}
/*$url = $uri->toString();

$redirect = new Hubzero\Http\RedirectResponse($url, 301);
$redirect->setRequest(App::get('request'));
$redirect->send();*/
Lang::load('com_login', Component::path('com_login') . '/site');

require_once Component::path('com_login') . '/site/controllers/auth.php';

$controller = new Components\Login\Site\controllers\Auth();
$controller->execute();
