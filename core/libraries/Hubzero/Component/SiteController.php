<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Component\Exception\InvalidTaskException;
use Hubzero\Component\Exception\InvalidControllerException;
use Hubzero\Base\Obj;
use Hubzero\Document\Assets;
use Hubzero\Htmx\Htmx;
use Hubzero\Inertia\Inertia;
use ReflectionClass;
use ReflectionMethod;
use Lang;

/**
 * Base controller for components to extend.
 *
 * Accepts an array of configuration values to the constructor. If no config
 * passed, it will automatically determine the component and controller names.
 * Internally, sets the $database, $user, $view, and component $config.
 *
 * Executable tasks are determined by method name. All public methods that end in
 * "Task" (e.g., displayTask, editTask) are callable by the end user.
 *
 * View name defaults to controller name with layout defaulting to task name. So,
 * a $controller of "One" and a $task of "two" will map to:
 *
 *    /{component name}
 *        /views
 *            /one
 *                /tmpl
 *                    /two.php
 */
class SiteController extends Obj implements ControllerInterface
{
    use \Hubzero\Base\Traits\AssetAware;

    /**
     * The name of the component derived from the controller class name
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = null;

    /**
     * Container for storing overloaded data
     *
     * @public array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_data = array();

    /**
     * The task the component is to perform
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_task = null;

    /**
     * A list of executable tasks
     *
     * @public array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_taskMap = array(
        '__default' => 'display'
    );

    /**
     * The name of the task to be executed
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_doTask = null;

    /**
     * The name of this controller
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_controller = null;

    /**
     * The name of this component
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_option = null;

    /**
     * The base path to this component
     *
     * @public string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_basePath = null;

    /**
     * Redirection URL
     *
     * @public string
     * @deprecated
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_redirect = null;

    /**
     * The message to display
     *
     * @public string
     * @deprecated
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_message = null;

    /**
     * Message type
     *
     * @public string
     * @deprecated
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_messageType = 'message';

    /**
     * Constructor
     *
     * @param   array  $config  Optional configurations to be used
     * @return  void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __construct($config = array())
    {
        $this->_redirect    = null;
        $this->_message     = null;
        $this->_messageType = 'message';

        // Get the reflection info
        $r = new ReflectionClass($this);

        // Is it namespaced?
        if ($r->inNamespace()) {
            // It is! This makes things easy.
            $this->_controller = strtolower($r->getShortName());
        }

        // Set the name
        if (empty($this->_name)) {
            if (isset($config['name'])) {
                $this->_name = $config['name'];
            } else {
                $segments = null;
                $cls = $r->getName();

                // If namespaced...
                if (strstr($cls, '\\')) {
                    $segments = explode('\\', $cls);
                } elseif (preg_match('/(.*)Controller(.*)/i', $cls, $segments)) {
                // If matching the pattern of ComponentControllerName
                    $this->_controller = isset($segments[2]) ? strtolower($segments[2]) : null;
                } else {
                // Uh-oh!
                    throw new InvalidControllerException(
                        Lang::txt('Controller::__construct() : Can\'t get or parse class name.'),
                        500
                    );
                }

                $this->_name = strtolower($segments[1]);
            }
        }

        // Set the base path
        if (array_key_exists('base_path', $config)) {
            $this->_basePath = $config['base_path'];
        } else {
            // Set base path relative to the controller file rather than
            // an absolute path. This gives us a little more flexibility.
            $this->_basePath = dirname(dirname($r->getFileName()));
        }

        // Set the component name
        $this->_option = 'com_' . $this->_name;

        // Determine the methods to exclude from the base class.
        $xMethods = get_class_methods('\\Hubzero\\Component\\SiteController');

        // Get all the public methods of this class
        foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            // Ensure task isn't in the exclude list and ends in 'Task'
            if (
                (!in_array($name, $xMethods) || $name == 'displayTask')
                && substr(strtolower($name), -4) == 'task'
            ) {
                // Remove the 'Task' suffix
                $name = substr($name, 0, -4);
                // Auto register the methods as tasks.
                $this->_taskMap[strtolower($name)] = $name;
            }
        }

        // get language object & get any loaded lang for option
        $lang   = \Lang::getRoot();
        $loaded = $lang->getPaths($this->_option);

        // Load language file if we dont have one yet
        if (!isset($loaded) || empty($loaded)) {
            $lang->load($this->_option, $this->_basePath . '/../..');
        }

        // Set some commonly used vars
        //
        // [!] Deprecated
        //     These will be going away in a future version. Do not use.
        $this->juser    = \User::getInstance();
        $this->database = \App::get('db');
        $this->config   = \Component::params($this->_option);
    }

    /**
     * Method to set an overloaded variable to the component
     *
     * @param   string  $property  Name of overloaded variable to add
     * @param   mixed   $value     Value of the overloaded variable
     * @return  void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }

    /**
     * Method to get an overloaded variable of the component
     *
     * @param   string  $property  Name of overloaded variable to retrieve
     * @return  mixed   Value of the overloaded variable
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __get($property)
    {
        if (isset($this->_data[$property])) {
            return $this->_data[$property];
        }
    }

    /**
     * Method to check if a poperty is set
     *
     * @param   string  $property  Name of overloaded variable to add
     * @return  boolean
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __isset($property)
    {
        return isset($this->_data[$property]);
    }

    /**
     * Determines task being called and attempts to execute it
     *
     * @return  void
     */
    public function execute()
    {
        // Incoming task
        $this->_task = strtolower(\Request::getCmd('task', \Request::getWord('layout', '')));

        // Check if the task is in the taskMap
        if (isset($this->_taskMap[$this->_task])) {
            $doTask = $this->_taskMap[$this->_task];
        } elseif (isset($this->_taskMap['__default'])) {
        // Check if the default task is set
            $doTask = $this->_taskMap['__default'];
        } else {
        // Raise an error (hopefully, this shouldn't happen)
            throw new InvalidTaskException(Lang::txt('The requested task "%s" was not found.', $this->_task), 404);
        }

        $name = $this->_controller;
        $layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
        if (!$this->_controller) {
            $cls = get_class($this);
            // Attempt to parse the controller name from the class name
            if (
                (ucfirst($this->_name) . 'Controller') != $cls
                && preg_match('/(\w)Controller(.*)/i', $cls, $r)
            ) {
                $this->_controller = strtolower($r[2]);
                $name   = $this->_controller;
                $layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
            } elseif (preg_match('/(.?)Controllers\\\(.*)/i', $cls, $r)) {
            // Namepsaced component
                $this->_controller = strtolower($r[2]);
                $name   = $this->_controller;
                $layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
            } else {
            // No controller name found - single controller component
                $name = $doTask;
            }
        }

        // Instantiate a view with layout the same name as the task
        $this->view = new View(array(
            'base_path' => $this->_basePath,
            'name'      => $name,
            'layout'    => $layout
        ));

        // Set some commonly used vars
        $this->view->set('option', $this->_option)
                    ->set('task', $doTask)
                    ->set('controller', $this->_controller);

        // Record the actual task being fired
        $doTask .= 'Task';

        // On before do task hook
        $this->_onBeforeDoTask();

        // Call the task
        $this->$doTask();
    }

    /**
     * Reset the view object
     *
     * @param   string  $name    The name of the view
     * @param   string  $layout  The name of the layout (optional)
     * @return  void
     */
    public function setView($name, $layout = null)
    {
        $config = array(
            'name' => $name
        );
        if ($layout) {
            $config['layout'] = $layout;
        }
        $this->view = new View($config);

        // Set some commonly used vars
        $this->view->set('option', $this->_option);
        $this->view->set('task', $name);
        $this->view->set('controller', $this->_controller);
    }

    /**
     * Get the last task that is being performed or was most recently performed.
     *
     * @return  string  The task that is being performed or was most recently performed.
     */
    public function getTask()
    {
        return $this->_task;
    }

    /**
     * Register (map) a task to a method in the class.
     *
     * @param   string  $task    The task.
     * @param   string  $method  The name of the method in the derived class to perform for this task.
     * @return  object  Supports chaining.
     */
    public function registerTask($task, $method)
    {
        if (in_array(strtolower($method), $this->_taskMap)) {
            $this->_taskMap[strtolower($task)] = $method;
        }

        return $this;
    }

    /**
     * Unregister (unmap) a task in the class.
     *
     * @param   string  $task  The task.
     * @return  object  Supports chaining.
     */
    public function unregisterTask($task)
    {
        unset($this->_taskMap[strtolower($task)]);

        return $this;
    }

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string  $method  The name of the method in the derived class to perform if a named task is not found.
     * @return  object  Supports chaining.
     */
    public function registerDefaultTask($method)
    {
        return $this->registerTask('__default', $method);
    }

    /**
     * Disable default task, remove __default from the taskmap
     *
     * When default task disabled the controller will give a 404 error if the method called doesn't exist
     *
     * @return  void
     */
    public function disableDefaultTask()
    {
        return $this->unregisterTask('__default');
    }

    /**
     * Method to redirect the application to a new URL and optionally include a message
     *
     * @param   string  $url   URL to redirect to. Optional.
     * @param   string  $msg   Message to display on redirect. Optional.
     * @param   string  $type  Message type. Optional, defaults to 'message'.
     * @return  void
     * @deprecated
     */
    public function redirect($url = null, $msg = null, $type = null)
    {
        if ($url) {
            $this->setRedirect($url, $msg, $type);
        }

        if ($this->_redirect != null) {
            \App::redirect($this->_redirect, $this->_message, $this->_messageType);
        }
    }

    /**
     * Set a URL for browser redirection.
     *
     * @param   string  $url   URL to redirect to.
     * @param   string  $msg   Message to display on redirect. Optional, defaults to
     *                         value set internally by controller, if any.
     * @param   string  $type  Message type. Optional, defaults to 'message'.
     * @return  object
     * @deprecated
     */
    public function setRedirect($url, $msg = null, $type = null)
    {
        $this->_redirect = $url;
        if ($msg !== null) {
            // controller may have set this directly
            $this->_message = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage.
        if (empty($type)) {
            if (empty($this->_messageType)) {
                $this->_messageType = 'message';
            }
        } else {
        // If the type is explicitly set, set it.
            $this->_messageType = $type;
        }

        return $this;
    }

    /**
     * Set a URL for browser redirection.
     *
     * @param   string  $msg   Message to display on redirect. Optional, defaults to
     *                         value set internally by controller, if any.
     * @param   string  $type  Message type. Optional, defaults to 'message'.
     * @return  object
     * @deprecated
     */
    public function setMessage($msg, $type = 'message')
    {
        // controller may have set this directly
        $this->_message     = $msg;
        $this->_messageType = $type;

        return $this;
    }

    /**
     * Method to check admin access permission
     *
     * @return  boolean  True on success
     * @deprecated
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _authorize()
    {
        // Check if they are logged in
        if ($this->juser->isGuest()) {
            return false;
        }

        if ($this->juser->authorise('core.admin', $this->_option)) {
            return true;
        }

        return false;
    }

    /**
     * Perform before actually calling the given task
     *
     * @return  void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _onBeforeDoTask()
    {
        // Do nothing - override in subclass
    }

    /**
     * Inertia helper: detect Inertia request.
     *
     * @return  bool
     */
    protected function isInertiaRequest(): bool
    {
        return Inertia::isInertiaRequest();
    }

    /**
     * Inertia helper: render component payload/response.
     *
     * @param   string  $component
     * @param   array   $props
     * @return  array|null
     */
    protected function inertiaRender(string $component, array $props = []): ?array
    {
        return Inertia::render($component, $props);
    }

    /**
     * Inertia helper: render a canonical root mount node for HTML response templates.
     *
     * @param   string  $component
     * @param   array   $props
     * @param   string  $mountId
     * @return  string
     */
    protected function inertiaRootNode(string $component, array $props = array(), string $mountId = 'app'): string
    {
        return Inertia::renderRootNode($component, $props, $mountId);
    }

    /**
     * Inertia helper: share global props for request.
     *
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    protected function inertiaShare($key, $value = null): void
    {
        Inertia::share($key, $value);
    }

    /**
     * Inertia helper: redirect with Inertia-aware semantics.
     *
     * @param   string  $url
     * @param   int     $status
     * @return  void
     */
    protected function inertiaRedirect(string $url, int $status = 302): void
    {
        Inertia::redirect($url, $status);
    }

    /**
     * Inertia helper: send location response.
     *
     * @param   string  $url
     * @return  void
     */
    protected function inertiaLocation(string $url): void
    {
        Inertia::location($url);
    }
    /**
     * Inertia helper: attach debug context metadata.
     *
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    protected function inertiaDebugContext($key, $value = null): void
    {
        Inertia::debugContext($key, $value);
    }

    /**
     * Inertia helper: check whether debug mode is enabled.
     *
     * @return  bool
     */
    protected function isInertiaDebugEnabled(): bool
    {
        return Inertia::isDebugEnabled();
    }

    /**
     * Inertia helper: retrieve current debug snapshot.
     *
     * @return  array
     */
    protected function inertiaDebugSnapshot(): array
    {
        return Inertia::debugSnapshot();
    }

    /**
     * Inertia helper: access parsed request details.
     *
     * @return  array
     */
    protected function inertiaRequestDetails(): array
    {
        return Inertia::requestDetails();
    }

    /**
     * Inertia helper: render reusable debug panel markup.
     *
     * @param   array  $snapshot
     * @param   array  $options
     * @return  string
     */
    protected function inertiaDebugPanel(array $snapshot = array(), array $options = array()): string
    {
        return Inertia::renderDebugPanel($snapshot, $options);
    }

    /**
     * Inertia helper: emit debug header immediately.
     *
     * @return  void
     */
    protected function inertiaEmitDebugHeader(): void
    {
        Inertia::emitDebugHeader();
    }

    /**
     * Inertia helper: start a profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    protected function inertiaProfileStart(string $label): void
    {
        Inertia::profileStart($label);
    }

    /**
     * Inertia helper: stop a profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    protected function inertiaProfileStop(string $label): void
    {
        Inertia::profileStop($label);
    }

    /**
     * Inertia helper: retrieve profile timing snapshot.
     *
     * @return  array
     */
    protected function inertiaProfileSnapshot(): array
    {
        return Inertia::profileSnapshot();
    }

    /**
     * Inertia helper: emit profile header.
     *
     * @return  void
     */
    protected function inertiaEmitProfileHeader(): void
    {
        Inertia::emitProfileHeader();
    }

    /**
     * Inertia helper: preserve debug mode in outbound request params.
     *
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    protected function inertiaDebugParams(array $params, string $name = 'inertia_debug', string $value = '1'): array
    {
        return Inertia::preserveDebugParam($params, $name, $value);
    }

    /**
     * Inertia helper: render hidden debug field for forms.
     *
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    protected function inertiaDebugInput(string $name = 'inertia_debug', string $value = '1'): string
    {
        return Inertia::renderDebugHiddenInput($name, $value);
    }

    /**
     * HTMX helper: detect HTMX request.
     *
     * @return  bool
     */
    protected function isHtmxRequest(): bool
    {
        return Htmx::isHtmxRequest();
    }

    /**
     * HTMX helper: detect boosted navigation request.
     *
     * @return  bool
     */
    protected function isHtmxBoosted(): bool
    {
        return Htmx::isHtmxBoosted();
    }

    /**
     * HTMX helper: detect history restore request.
     *
     * @return  bool
     */
    protected function isHtmxHistoryRestoreRequest(): bool
    {
        return Htmx::isHtmxHistoryRestoreRequest();
    }

    /**
     * HTMX helper: add HX-Trigger response header.
     *
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    protected function htmxTrigger(string $event, $payload = array()): void
    {
        Htmx::trigger($event, $payload);
    }

    /**
     * HTMX helper: add HX-Trigger-After-Settle response header.
     *
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    protected function htmxTriggerAfterSettle(string $event, $payload = array()): void
    {
        Htmx::triggerAfterSettle($event, $payload);
    }

    /**
     * HTMX helper: add HX-Trigger-After-Swap response header.
     *
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    protected function htmxTriggerAfterSwap(string $event, $payload = array()): void
    {
        Htmx::triggerAfterSwap($event, $payload);
    }

    /**
     * HTMX helper: redirect with HX-Redirect fallback.
     *
     * @param   string  $url
     * @return  void
     */
    protected function htmxRedirect(string $url): void
    {
        Htmx::redirect($url);
    }

    /**
     * HTMX helper: set HX-Location response header.
     *
     * @param   string  $url
     * @param   array   $options
     * @return  void
     */
    protected function htmxLocation(string $url, array $options = array()): void
    {
        Htmx::location($url, $options);
    }

    /**
     * HTMX helper: set HX-Reswap response header.
     *
     * @param   string  $strategy
     * @return  void
     */
    protected function htmxReswap(string $strategy): void
    {
        Htmx::reswap($strategy);
    }

    /**
     * HTMX helper: set HX-Retarget response header.
     *
     * @param   string  $selector
     * @return  void
     */
    protected function htmxRetarget(string $selector): void
    {
        Htmx::retarget($selector);
    }

    /**
     * HTMX helper: set HX-Push-Url response header.
     *
     * @param   string|bool  $url
     * @return  void
     */
    protected function htmxPushUrl($url): void
    {
        Htmx::pushUrl($url);
    }

    /**
     * HTMX helper: set HX-Replace-Url response header.
     *
     * @param   string|bool  $url
     * @return  void
     */
    protected function htmxReplaceUrl($url): void
    {
        Htmx::replaceUrl($url);
    }

    /**
     * HTMX helper: force full refresh response.
     *
     * @return  void
     */
    protected function htmxRefresh(): void
    {
        Htmx::refresh();
    }

    /**
     * HTMX helper: set HX-Reselect response header.
     *
     * @param   string  $selector
     * @return  void
     */
    protected function htmxReselect(string $selector): void
    {
        Htmx::reselect($selector);
    }

    /**
     * HTMX helper: ensure response includes Vary: HX-Request.
     *
     * @return  void
     */
    protected function htmxVaryOnRequest(): void
    {
        Htmx::varyOnRequest();
    }

    /**
     * HTMX helper: access parsed request details.
     *
     * @return  array
     */
    protected function htmxRequestDetails(): array
    {
        return Htmx::requestDetails();
    }

    /**
     * HTMX helper: safe same-origin path from HX-Current-URL.
     *
     * @return  string|null
     */
    protected function htmxCurrentUrlAbsPath(): ?string
    {
        return Htmx::currentUrlAbsPath();
    }

    /**
     * HTMX helper: emit fragment response and terminate request.
     *
     * @param   string  $html
     * @return  void
     */
    protected function htmxFragment(string $html, int $status = 200): void
    {
        Htmx::fragment($html, $status);
    }

    /**
     * HTMX helper: emit 204 No Content response.
     *
     * @return  void
     */
    protected function htmxNoContent(): void
    {
        Htmx::noContent();
    }

    /**
     * HTMX helper: emit 286 Stop Polling response.
     *
     * @return  void
     */
    protected function htmxStopPolling(): void
    {
        Htmx::stopPolling();
    }

    /**
     * HTMX helper: emit 422 validation response with fragment body.
     *
     * @param   string  $html
     * @param   array   $errors
     * @return  void
     */
    protected function htmxValidation(string $html, array $errors = array()): void
    {
        Htmx::validation($html, $errors);
    }

    /**
     * HTMX helper: share state for HTMX + Alpine usage.
     *
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    protected function htmxState($key, $value = null): void
    {
        Htmx::state($key, $value);
    }

    /**
     * HTMX helper: attach debug context metadata.
     *
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    protected function htmxDebugContext($key, $value = null): void
    {
        Htmx::debugContext($key, $value);
    }

    /**
     * HTMX helper: check whether debug mode is enabled.
     *
     * @return  bool
     */
    protected function isHtmxDebugEnabled(): bool
    {
        return Htmx::isDebugEnabled();
    }

    /**
     * HTMX helper: retrieve current debug snapshot.
     *
     * @return  array
     */
    protected function htmxDebugSnapshot(): array
    {
        return Htmx::debugSnapshot();
    }

    /**
     * HTMX helper: emit debug header immediately.
     *
     * @return  void
     */
    protected function htmxEmitDebugHeader(): void
    {
        Htmx::emitDebugHeader();
    }

    /**
     * HTMX helper: start a profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    protected function htmxProfileStart(string $label): void
    {
        Htmx::profileStart($label);
    }

    /**
     * HTMX helper: stop a profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    protected function htmxProfileStop(string $label): void
    {
        Htmx::profileStop($label);
    }

    /**
     * HTMX helper: retrieve profile timing snapshot.
     *
     * @return  array
     */
    protected function htmxProfileSnapshot(): array
    {
        return Htmx::profileSnapshot();
    }

    /**
     * HTMX helper: emit profile header.
     *
     * @return  void
     */
    protected function htmxEmitProfileHeader(): void
    {
        Htmx::emitProfileHeader();
    }

    /**
     * HTMX helper: preserve debug mode in outbound request params.
     *
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    protected function htmxDebugParams(array $params, string $name = 'htmx_debug', string $value = '1'): array
    {
        return Htmx::preserveDebugParam($params, $name, $value);
    }

    /**
     * HTMX helper: render hidden debug field for forms.
     *
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    protected function htmxDebugInput(string $name = 'htmx_debug', string $value = '1'): string
    {
        return Htmx::renderDebugHiddenInput($name, $value);
    }

    /**
     * HTMX helper: render reusable debug panel markup.
     *
     * @param   array  $snapshot
     * @param   array  $options
     * @return  string
     */
    protected function htmxDebugPanel(array $snapshot = array(), array $options = array()): string
    {
        return Htmx::renderDebugPanel($snapshot, $options);
    }

    /**
     * HTMX helper: render shared JSON state node.
     *
     * @param   string  $id
     * @return  string
     */
    protected function htmxStateNode(string $id = 'hx-state'): string
    {
        return Htmx::renderStateNode($id);
    }
}
