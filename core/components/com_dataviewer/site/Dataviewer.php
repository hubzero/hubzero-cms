<?php

/**
 * Component entry point for the Dataviewer.
 *
 * Initializes configuration and dispatches to the appropriate
 * controller based on the task parameter.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

class Dataviewer extends AbstractComponent
{
    /**
     * Map legacy task names to controller classes.
     *
     * @var  array
     */
    protected $taskControllerMap = [
        'view'        => 'spreadsheet',
        'data'        => 'data',
        'file'        => 'files',
        'stream_file' => 'files',
        'gallery'     => 'files',
    ];

    /**
     * Execute the component.
     *
     * @return  void
     */
    protected function execute(): void
    {
        DvConfig::init();

        // Determine which controller to use from the task parameter.
        // The router puts the first URL segment into 'task'.
        $task = strtolower(Request::getCmd('task', 'view'));

        // Map task to controller name
        $controllerName = isset($this->taskControllerMap[$task])
            ? $this->taskControllerMap[$task]
            : 'spreadsheet';

        // For the data controller, the "task" should be "display" since
        // it only has one action. For files, preserve the task.
        // For spreadsheet, task is always "display" (the default).
        $controllerClass = __NAMESPACE__ . '\\Controllers\\'
            . ucfirst(strtolower($controllerName));

        if (!class_exists($controllerClass)) {
            // Fall back to legacy dispatch if controller not found
            Controller::dispatch();
            return;
        }

        $controller = new $controllerClass();
        $controller->execute();
        $controller->redirect();
    }
}
