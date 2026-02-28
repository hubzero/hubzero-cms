<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Component;

use Hubzero\Framework\Base\Obj;
use Hubzero\Framework\Facades\RequestFacade as Request;
use Hubzero\Framework\Facades\ComponentFacade as Component;
use ReflectionClass;
use ReflectionMethod;

/**
 * Base controller for HubZero components.
 *
 * Simplified port of core/libraries/Hubzero/Component/SiteController.php.
 *
 * Task methods are discovered by reflection: any public method ending in
 * "Task" is registered as a callable task. The default task is "display".
 *
 * View name defaults to controller name, layout defaults to task name.
 * So controller "Entries" with task "display" renders:
 *   {component}/views/entries/tmpl/display.php
 */
class SiteController extends Obj
{
    protected ?string $_name = null;
    protected ?string $_task = null;
    protected ?string $_doTask = null;
    protected ?string $_controller = null;
    protected ?string $_option = null;
    protected ?string $_basePath = null;
    protected ?string $_redirect = null;
    protected ?string $_message = null;
    protected ?string $_messageType = 'message';

    /**
     * Task map: task name => method name (without "Task" suffix).
     */
    protected array $_taskMap = [
        '__default' => 'display',
    ];

    /**
     * The view instance for the current task.
     */
    public ?View $view = null;

    /**
     * Component parameters.
     */
    public mixed $config = null;

    public function __construct(array $config = [])
    {
        $r = new ReflectionClass($this);

        // Determine controller name from class
        if ($r->inNamespace()) {
            $this->_controller = strtolower($r->getShortName());
        }

        // Determine component name
        if (empty($this->_name)) {
            if (isset($config['name'])) {
                $this->_name = $config['name'];
            } else {
                $cls = $r->getName();

                if (str_contains($cls, '\\')) {
                    $segments = explode('\\', $cls);
                    // Components\{Name}\Site\Controllers\{Controller}
                    // The component name is segment[1]
                    $this->_name = strtolower($segments[1]);
                }
            }
        }

        // Set base path
        if (isset($config['base_path'])) {
            $this->_basePath = $config['base_path'];
        } else {
            // Default: two directories up from the controller file
            $this->_basePath = dirname(dirname($r->getFileName()));
        }

        $this->_option = 'com_' . $this->_name;

        // Auto-discover task methods
        $excludeMethods = get_class_methods(self::class);

        foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            if (
                (!in_array($name, $excludeMethods) || $name === 'displayTask')
                && str_ends_with(strtolower($name), 'task')
            ) {
                $taskName = substr($name, 0, -4);
                $this->_taskMap[strtolower($taskName)] = $taskName;
            }
        }

        // Component params
        $this->config = Component::params($this->_option);
    }

    /**
     * Determine and execute the requested task.
     */
    public function execute(): void
    {
        // Get task from request
        $this->_task = strtolower(Request::getCmd('task', Request::getWord('layout', '')));

        // Map to method name
        if (isset($this->_taskMap[$this->_task])) {
            $doTask = $this->_taskMap[$this->_task];
        } elseif (isset($this->_taskMap['__default'])) {
            $doTask = $this->_taskMap['__default'];
        } else {
            throw new \RuntimeException(
                sprintf('The requested task "%s" was not found.', $this->_task),
                404
            );
        }

        $this->_doTask = $doTask;

        // Determine view name and layout
        $name = $this->_controller ?: $doTask;
        $layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);

        // Create view
        $this->view = new View([
            'base_path' => $this->_basePath,
            'name' => $name,
            'layout' => $layout,
        ]);

        // Set common view variables
        $this->view
            ->set('option', $this->_option)
            ->set('task', $doTask)
            ->set('controller', $this->_controller);

        // Call the task method
        $methodName = $doTask . 'Task';
        $this->$methodName();
    }

    /**
     * Default display task — just renders the view.
     */
    public function displayTask(): void
    {
        $this->view->display();
    }

    /**
     * Register a task name to a method.
     */
    public function registerTask(string $task, string $method): static
    {
        $this->_taskMap[strtolower($task)] = $method;
        return $this;
    }

    /**
     * Register the default task.
     */
    public function registerDefaultTask(string $method): static
    {
        $this->_taskMap['__default'] = $method;
        return $this;
    }

    /**
     * Get the current task name.
     */
    public function getTask(): ?string
    {
        return $this->_task;
    }

    /**
     * Reset the view to a different name/layout.
     */
    public function setView(string $name, ?string $layout = null): void
    {
        $config = [
            'name' => $name,
            'base_path' => $this->_basePath,
        ];

        if ($layout) {
            $config['layout'] = $layout;
        }

        $this->view = new View($config);
        $this->view
            ->set('option', $this->_option)
            ->set('task', $this->_doTask)
            ->set('controller', $this->_controller);
    }

    /**
     * Set a redirect URL.
     */
    public function setRedirect(string $url, ?string $message = null, string $type = 'message'): void
    {
        $this->_redirect = $url;
        $this->_message = $message;
        $this->_messageType = $type;
    }
}
