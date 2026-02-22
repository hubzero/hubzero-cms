<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Answers extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_answers')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'questions');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
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
    }
}
