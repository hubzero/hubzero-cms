<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\AdminMenu;

/**
 * Extended class for rendering nested menus
 */
class Tree extends \Hubzero\Base\Obj
{
    /**
     * CSS string to add to document head
     *
     * @var  string
     */
    protected $css = null;

    /**
     * Root node
     *
     * @var  object
     */
    protected $root = null;

    /**
     * Current working node
     *
     * @var  object
     */
    protected $current = null;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->root = new Node('ROOT');
        $this->current =& $this->root;
    }

    /**
     * Method to add a child
     *
     * @param   array    &$node       The node to process
     * @param   boolean  $setCurrent  True to set as current working node
     * @return  mixed
     */
    public function addChild($node, $setCurrent = false)
    {
        $this->current->addChild($node);

        if ($setCurrent) {
            $this->current = &$node;
        }
    }

    /**
     * Method to get the parent
     *
     * @return  void
     */
    public function getParent()
    {
        $this->current = &$this->current->getParent();
    }

    /**
     * Method to get the parent
     *
     * @return  void
     */
    public function reset()
    {
        $this->current = &$this->root;
    }

    /**
     * Add a separator
     *
     * @return  void
     */
    public function addSeparator()
    {
        $this->addChild(new Node(null, null, 'separator', false));
    }

    /**
     * Render the menu
     *
     * @param   string  $id     Menu ID
     * @param   string  $class  Menu class
     * @return  void
     */
    public function renderMenu($id = 'menu', $class = '')
    {
        $depth = 1;

        if (!empty($id)) {
            $id = 'id="' . $id . '"';
        }

        if (!empty($class)) {
            $class = 'class="' . $class . '"';
        }

        // Recurse through children if they exist
        while ($this->current->hasChildren()) {
            echo '<ul ' . $id . ' ' . $class . ">\n";
            foreach ($this->current->getChildren() as $child) {
                $this->current =& $child;
                $this->renderLevel($depth++);
            }
            echo "</ul>\n";
        }

        if ($this->css) {
            // Add style to document head
            \Document::addStyleDeclaration($this->css);
        }
    }

    /**
     * Render a menu level
     *
     * @param   string  $id     Menu ID
     * @param   string  $class  Menu class
     * @return  void
     */
    public function renderLevel($depth)
    {
        // Build the CSS class suffix
        $class = '';
        $iconClass = '';
        $classes = array();

        if ($this->current->class) {
            $classes = explode(' ', trim($this->current->class));
            foreach ($classes as $i => $clas) {
                if (substr($clas, 0, strlen('class:')) == 'class:') {
                    $iconClass = $clas;
                    unset($classes[$i]);
                }
            }
        }

        if ($this->current->hasChildren()) {
            $classes[] = 'node';
        }

        if ($this->current->active) {
            $classes[] = 'active';
        }

        if (!empty($classes)) {
            $class = ' class="' . implode(' ', $classes) . '"';
        }

        // Print the item
        echo '<li' . $class . '>';

        // Print a link if it exists
        $linkClass = $this->getIconClass($iconClass);
        if (!empty($linkClass)) {
            $linkClass = ' class="' . $linkClass . '"';
        }

        if ($this->current->link != null && $this->current->target != null) {
            echo '<a' . $linkClass . ' href="' . $this->current->link . '" rel="noopener" target="'
                . $this->current->target . '">' . $this->current->title . '</a>';
            if ($this->current->hasChildren()) {
                echo '<span class="toggler" aria-hidden="true"></span>';
            }
        } elseif ($this->current->link != null && $this->current->target == null) {
            echo '<a' . $linkClass . ' href="' . $this->current->link . '">' . $this->current->title . '</a>';
            if ($this->current->hasChildren()) {
                echo '<span class="toggler" aria-hidden="true"></span>';
            }
        } elseif ($this->current->title != null) {
            echo '<a' . $linkClass . '>' . $this->current->title . '</a>';
            if ($this->current->hasChildren()) {
                echo '<span class="toggler" aria-hidden="true"></span>';
            }
        } else {
            echo '<span></span>';
        }

        // Recurse through children if they exist
        while ($this->current->hasChildren()) {
            if ($this->current->class) {
                $id = '';
                if (!empty($this->current->id)) {
                    $id = ' id="menu-' . strtolower($this->current->id) . '"';
                }
                echo '<ul' . $id . ' class="menu-component">' . "\n";
            } else {
                echo '<ul>' . "\n";
            }

            foreach ($this->current->getChildren() as $child) {
                $this->current =& $child;
                $this->renderLevel($depth++);
            }

            echo "</ul>\n";
        }
        echo "</li>\n";
    }

    /**
     * Method to get the CSS class name for an icon identifier or create one if
     * a custom image path is passed as the identifier
     *
     * @param   string  $identifier  Icon identification string
     * @return  string  CSS class name
     */
    public function getIconClass($identifier)
    {
        static $classes;

        // Initialise the known classes array if it does not exist
        if (!is_array($classes)) {
            $classes = array();
        }

        // If we don't already know about the class... build it and mark it
        // known so we don't have to build it again
        if (!isset($classes[$identifier])) {
            if (substr($identifier, 0, 6) == 'class:') {
                // We were passed a class name
                $class = substr($identifier, 6);
                $classes[$identifier] = "icon-$class";
            } else {
                if ($identifier == null) {
                    return null;
                }

                // Build the CSS class for the icon
                $class = preg_replace('#\.[^.]*$#', '', basename($identifier));
                $class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);

                $this->css  .= "\n.icon-$class {\n" .
                        "\tbackground: url($identifier) no-repeat;\n" .
                    "}\n";

                $classes[$identifier] = "icon-$class";
            }
        }

        return $classes[$identifier];
    }
}
