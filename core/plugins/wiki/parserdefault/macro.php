<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */


/**
 * Base class for wiki macros
 * Should be extended
 */
class WikiMacro
{
    /**
     * Name of the macro
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = null;

    /**
     * Container for internal data
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_data = array();

    /**
     * Database
     *
     * @var  object
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_db = null;

    /**
     * Container for errors
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_error = null;

    /**
     * Container for errors
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_arguments = null;

    /**
     * Allow macro in partial parsing?
     *
     * @var  string
     */
    public $allowPartial = false;

    /**
     * Allow macro in partial parsing?
     *
     * @var  array
     */
    public $linkLog = array();

    /**
     * Instance of a macro
     *
     * @var  object
     */
    protected static $thisInstance = null;

    /**
     * Constructor
     *
     * @param   array  $config  Configuration options
     * @return  void
     */
    public function __construct($config = array())
    {
        $this->_db = App::get('db');

        $this->args = '';

        // Set the controller name
        if (empty($this->_name)) {
            if (isset($config['name'])) {
                $this->_name = $config['name'];
            } else {
                // Get the reflection info
                $r = new ReflectionClass($this);

                // Is it namespaced?
                if ($r->inNamespace()) {
                    // It is! This makes things easy.
                    $this->_name = strtolower($r->getShortName());
                } elseif (preg_match('/(.*)Macro/i', get_class($this), $r)) {
                    $this->_name = strtolower($r[1]);
                } else {
                    throw new RuntimeException(__CLASS__ . '::__construct(); Can\'t get or parse class name.');
                }
            }
        }
    }

    /**
     * Set a property
     *
     * @param   string  $property  Name of property to set
     * @param   mixed   $value     Value to set property to
     * @return  void
     */
    public function __set($property, $value)
    {
        if ($property == 'args') {
            $this->_arguments = null;
        }
        $this->_data[$property] = $value;
    }

    /**
     * Get a property
     *
     * @param   string  $property  Name of property to retrieve
     * @return  mixed
     */
    public function __get($property)
    {
        if (isset($this->_data[$property])) {
            return $this->_data[$property];
        }
    }

    /**
     * Get an instance of this macro, creating it if not found
     *
     * @param   array   $config  Configuration parameters
     * @return  object
     */
    public static function getInstance($config = array())
    {
        if (self::$thisInstance == null) {
            if (isset($config['name'])) {
                $name = $config['name'];
            } else {
                $name = get_class();
            }
            self::$thisInstance = new $name();
        }
        return self::$thisInstance;
    }

    /**
     * Render macro output
     * this should be overriden by extended classes
     *
     * @return  void
     */
    public function render()
    {
        // Abstract function for overloading
    }

    /**
     * Return the macro's name
     *
     * @return  string
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * Returns description of macro, use, and accepted arguments
     * this should be overriden by extended classes
     *
     * @return     string
     */
    public function description()
    {
        return Lang::txt('Not implemented.');
    }

    /**
     * Get macro argumentss
     *
     * @return  array  List of arguments
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getArguments()
    {
        if (!is_array($this->_arguments)) {
            // get the args passed in
            $arguments = explode(',', (string)$this->args);
            $arguments = array_map('trim', (array)$arguments);

            $this->_arguments = $arguments;
        }

        return $this->_arguments;
    }

    /**
     * Get a macro argument
     *
     * @param   mixed   $key
     * @param   mixed   $default
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getArgument($key, $default = null)
    {
        $arguments = $this->_getArguments();

        if (!$this->_hasArgument($key)) {
            return $default;
        }

        $value = $arguments[$key];

        return $value ? $value : $default;
    }

    /**
     * Set all macro argument values
     *
     * @param   array   $data
     * @return  object
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setArguments($data)
    {
        $this->_arguments = (array)$data;

        return $this;
    }

    /**
     * Set a macro argument value
     *
     * @param   mixed   $key
     * @param   mixed   $val
     * @return  object
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setArgument($key, $val)
    {
        $this->_arguments[$key] = $val;

        return $this;
    }

    /**
     * Check if macro has any arguments
     *
     * @return  boolean
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _hasArguments()
    {
        $arguments = $this->_getArguments();

        return !empty($arguments);
    }

    /**
     * Check if macro has specified argument
     *
     * @param   string   $key
     * @return  boolean
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _hasArgument($key)
    {
        $arguments = $this->_getArguments();

        return isset($arguments[$key]);
    }
}
