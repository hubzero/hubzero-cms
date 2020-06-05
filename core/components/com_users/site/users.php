<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Maintian backwards compatibility
if ($view = Request::getCmd('view'))
{
	if ($view != 'login')
	{
		Request::setVar('task', $view);
	}
}

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
		Request::setVar('task', $task);
	break;

	default:
		Request::setVar('task', '');
	break;
}

require_once __DIR__ . '/controllers/auth.php';

$controller = new Components\Users\Site\Controllers\Auth();
$controller->execute();
