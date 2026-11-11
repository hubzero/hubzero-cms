<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault;

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;

/**
 * Base class for wiki macros
 * Should be extended
 *
 * The parser hands each macro its context through __set(), which keeps it
 * in $data (setting $args also clears the parsed arguments):
 *
 * @property  string|null  $args        The text between the macro's parentheses
 * @property  string       $option      The component the content belongs to
 * @property  string       $scope       The path of the page the content belongs to
 * @property  string       $pagename    The name of that page
 * @property  int          $pageid      The id of that page, or the upload folder id of an unsaved one
 * @property  string       $domain      The kind of wiki the page is in (site, group, project)
 * @property  int          $domain_id   The id of the group or project whose wiki it is (0 for the site)
 * @property  string       $uniqPrefix  The parser's token, for ids unique to this rendering
 * @property  string       $filepath    The upload path to use instead of the wiki's, if any
 */
class WikiMacro
{
    /**
     * Name of the macro
     *
     * @var  string
     */
    protected $name = null;

    /**
     * Container for internal data
     *
     * @var  array
     */
    protected $data = [];

    /**
     * Database
     *
     * @var  object
     */
    protected $db = null;

    /**
     * Container for errors
     *
     * @var  array
     */
    protected $error = null;

    /**
     * Container for errors
     *
     * @var  array
     */
    protected $arguments = null;

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
    public $linkLog = [];

    /**
     * Macro instances, keyed by class name
     *
     * @var  array
     */
    protected static $instances = [];

    /**
     * Constructor
     *
     * @param   array  $config  Configuration options
     * @return  void
     */
    public function __construct($config = [])
    {
        $this->db = App::get('db');

        $this->args = '';

        // Set the controller name
        if (empty($this->name)) {
            if (isset($config['name'])) {
                $this->name = $config['name'];
            } else {
                // Get the reflection info
                $r = new \ReflectionClass($this);

                // Is it namespaced?
                if ($r->inNamespace()) {
                    // It is! This makes things easy.
                    $this->name = strtolower($r->getShortName());
                } elseif (preg_match('/(.*)Macro/i', get_class($this), $r)) {
                    $this->name = strtolower($r[1]);
                } else {
                    throw new \RuntimeException(__CLASS__ . '::__construct(); Can\'t get or parse class name.');
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
            $this->arguments = null;
        }
        $this->data[$property] = $value;
    }

    /**
     * Get a property
     *
     * @param   string  $property  Name of property to retrieve
     * @return  mixed
     */
    public function __get($property)
    {
        if (isset($this->data[$property])) {
            return $this->data[$property];
        }
    }

    /**
     * Get an instance of this macro, creating it if not found
     *
     * @param   array   $config  Configuration parameters
     * @return  object
     */
    public static function getInstance($config = [])
    {
        // Keyed by class. A single shared slot combined with static::class
        // meant whichever macro asked first owned the instance for the whole
        // request, and every later macro was handed that same wrong object.
        $name = isset($config['name']) ? $config['name'] : static::class;

        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new $name();
        }

        return self::$instances[$name];
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
        return $this->name;
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
    protected function getArguments()
    {
        if (!is_array($this->arguments)) {
            // get the args passed in
            $arguments = explode(',', (string) $this->args);
            $arguments = array_map('trim', (array) $arguments);

            $this->arguments = $arguments;
        }

        return $this->arguments;
    }

    /**
     * Get a macro argument
     *
     * @param   mixed   $key
     * @param   mixed   $default
     * @return  string
     */
    protected function getArgument($key, $default = null)
    {
        $arguments = $this->getArguments();

        if (!$this->hasArgument($key)) {
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
    protected function setArguments($data)
    {
        $this->arguments = (array) $data;

        return $this;
    }

    /**
     * Set a macro argument value
     *
     * @param   mixed   $key
     * @param   mixed   $val
     * @return  object
     */
    protected function setArgument($key, $val)
    {
        $this->arguments[$key] = $val;

        return $this;
    }

    /**
     * Check if macro has any arguments
     *
     * @return  boolean
     */
    protected function hasArguments()
    {
        $arguments = $this->getArguments();

        return !empty($arguments);
    }

    /**
     * Check if macro has specified argument
     *
     * @param   string   $key
     * @return  boolean
     */
    protected function hasArgument($key)
    {
        $arguments = $this->getArguments();

        return isset($arguments[$key]);
    }
}
