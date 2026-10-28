<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;

/**
 * Class for a component View
 *
 * @TODO: Change all public attributes of View class to protected
 * when all references have been fixed to use get()/set()
 */
class View extends AbstractView
{
    /**
     * Layout name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_layout = 'display';

    /**
     * Base URL
     *
     * @var string
     */
    public $baseurl;

    /**
     * Current component
     *
     * @var string
     */
    public $option;

    /**
     * Current controller
     *
     * @var string
     */
    public $controller;

    /**
     * Current task
     *
     * @var string
     */
    public $task;

    /**
     * Constructor
     *
     * @param   array  $config  A named configuration array for object construction.<br/>
     * @return  void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __construct($config = array())
    {
        // Look for overrides under the component that owns the view, not
        // the one the request is for: a group's wiki is com_wiki's views
        // rendered inside com_groups, and belongs in html/com_wiki/
        if (!array_key_exists('override_path', $config) && !empty($config['base_path'])) {
            if ($component = self::componentFromPath($config['base_path'])) {
                $config['override_path'] = $component;
            }
        }

        parent::__construct($config);

        // Set a base path for use by the view
        if (!array_key_exists('base_path', $config)) {
            $config['base_path'] = '';

            if (defined('PATH_COMPONENT')) {
                $config['base_path'] = PATH_COMPONENT;
            }
        }
        $this->_basePath = $config['base_path'];
    }

    /**
     * The component (com_*) a path lies inside, if any
     *
     * @param   string  $path  A component path, such as core/components/com_wiki/site
     * @return  string|null
     */
    protected static function componentFromPath($path)
    {
        $segments = array_values(array_filter(preg_split('#[/\\\\]+#', (string) $path), 'strlen'));

        // The directory after "components" names the component, with or
        // without the com_ prefix: Component::path() accepts both
        for ($i = count($segments) - 1; $i > 0; $i--) {
            if ($segments[$i - 1] === 'components' && preg_match('/^[A-Z0-9_\.-]+$/i', $segments[$i])) {
                $name = strtolower($segments[$i]);

                return substr($name, 0, 4) === 'com_' ? $name : 'com_' . $name;
            }
        }

        // Not under a components directory: take the nearest com_* segment
        foreach (array_reverse($segments) as $segment) {
            if (preg_match('/^com_[A-Z0-9_\.-]+$/i', $segment)) {
                return strtolower($segment);
            }
        }

        return null;
    }

    /**
     * Create a component view and return it
     *
     * @param   string  $layout  View layout
     * @param   string  $name    View name
     * @return  View
     */
    public function view($layout, $name = null)
    {
        // If we were passed only a view model, just render it.
        if ($layout instanceof AbstractView) {
            return $layout;
        }

        $view = new self(array(
            'base_path' => $this->_basePath,
            'name'      => ($name ? $name : $this->_name),
            'layout'    => $layout
        ));
        $view->set('option', $this->option)
             ->set('controller', $this->controller)
             ->set('task', $this->task);

        return $view;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param   string  $method
     * @param   array   $parameters
     * @return  mixed
     * @throws  \BadMethodCallException
     * @since   1.3.1
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __call($method, $parameters)
    {
        if (!static::hasHelper($method)) {
            foreach ($this->_path['helper'] as $path) {
                $file = $path . DIRECTORY_SEPARATOR . $method . '.php';
                if (file_exists($file)) {
                    include_once $file;
                    break;
                }
            }

            $option = ($this->option ? $this->option : \Request::getCmd('option'));
            $option = ucfirst(substr($option, 4));

            // Namespaced
            $invokable1 = '\\Components\\' . $option . '\\Helpers\\' . ucfirst($method);

            // Old naming scheme "OptionHelperMethod"
            $invokable2 = $option . 'Helper' . ucfirst($method);

            $callback = null;
            if (class_exists($invokable1)) {
                $callback = new $invokable1();
            } elseif (class_exists($invokable2)) {
                $callback = new $invokable2();
            }

            if (is_callable($callback)) {
                $callback->setView($this);

                $this->helper($method, $callback);
            }
        }

        return parent::__call($method, $parameters);
    }
}
