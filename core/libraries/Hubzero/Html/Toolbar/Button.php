<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Toolbar;

use Hubzero\Base\Obj;

/**
 * Button base class
 * The Button is the base class for all Button types
 *
 * Inspired by Joomla's JButton class
 */
abstract class Button extends Obj
{
    /**
     * Element name
     *
     * @var  string|null
     */
    protected $name = null;

    /**
     * Reference to the object that instantiated the element
     *
     * @var  \Hubzero\Html\Toolbar|null  The toolbar the button is on
     */
    protected $parent = null;

    /**
     * Constructor
     *
     * @param   \Hubzero\Html\Toolbar|null  $parent  The toolbar the button is on
     * @return  void
     */
    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get the element name
     *
     * @return  string  type of the parameter
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the HTML to render the button
     *
     * @param   array<int, mixed>  &$definition  The button type and its arguments
     * @return  string
     */
    public function render(&$definition)
    {
        // Initialise some variables
        $html   = null;
        $cls    = [];
        if (isset($definition[9])) {
            $cls = array_pop($definition);
        }
        $id     = call_user_func_array([&$this, 'fetchId'], $definition);
        $action = call_user_func_array([&$this, 'fetchButton'], $definition);

        // Build id attribute
        if ($id) {
            $id = 'id="' . $id . '"';
        }

        // Build the HTML Button
        $html .= '<li class="button ' . implode(' ', $cls) . '" ' . $id . ">\n";
        $html .= $action;
        $html .= "</li>\n";

        return $html;
    }

    /**
     * Method to get the CSS class name for an icon identifier
     *
     * @param   string  $identifier  Icon identification string
     * @return  string  CSS class name
     */
    public function fetchIconClass($identifier)
    {
        return "icon-$identifier icon-32-$identifier";
    }

    /**
     * Get the button
     *
     * @return  string
     */
    abstract public function fetchButton();
}
