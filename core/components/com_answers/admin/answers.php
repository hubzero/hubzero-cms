<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Admin;

if (!\User::authorise('core.manage', 'com_answers'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'economy.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'question.php';

$controllerName = \Request::getCmd('controller', 'questions');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'questions';
}

\Submenu::addEntry(
	\Lang::txt('COM_ANSWERS_QUESTIONS'),
	\Route::url('index.php?option=com_answers'),
	($controllerName == 'questions')
);
\Submenu::addEntry(
	\Lang::txt('COM_ANSWERS_RESPONSES'),
	\Route::url('index.php?option=com_answers&controller=answers&qid=0'),
	($controllerName == 'answers')
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
