<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Adminmenu;

use Hubzero\Facades\Document;

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
    protected $css;

    /**
     * Root node
     *
     * @var  \Modules\Adminmenu\Node
     */
    protected $root;

    /**
     * Current working node
     *
     * @var  \Modules\Adminmenu\Node
     */
    protected $current;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->root = new Node('ROOT');
        $this->current = & $this->root;
    }

    /**
     * Method to add a child
     *
     * @param   \Modules\Adminmenu\Node    $node       The node to process
     * @param   boolean  $setCurrent  True to set as current working node
     * @return  mixed
     */
    public function addChild($node, $setCurrent = false)
    {
        // addChild() takes its argument by reference (it never replaces it)
        $child = $node;
        $this->current->addChild($child);

        if ($setCurrent) {
            $this->current = $node;
        }

        return null;
    }

    /**
     * Method to get the parent
     *
     * @return  void
     */
    public function getParent()
    {
        // The root has no parent; stay on it
        $parent = $this->current->getParent();
        if ($parent !== null) {
            $this->current = $parent;
        }
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
                $this->current = & $child;
                $this->renderLevel($depth++);
            }
            echo "</ul>\n";
        }

        if ($this->css) {
            // Add style to document head
            Document::addStyleDeclaration($this->css);
        }
    }

    /**
     * Render a menu level
     *
     * @param   mixed  $depth
     * @return  void
     */
    public function renderLevel($depth)
    {
        // Build the CSS class suffix
        $class = '';
        $iconClass = '';
        $classes = [];

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
        } elseif ($this->current->link != null) {
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
                $this->current = & $child;
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
     * @return  string|null  CSS class name
     */
    public function getIconClass($identifier)
    {
        static $classes;

        // Initialise the known classes array if it does not exist
        if (!is_array($classes)) {
            $classes = [];
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
                $class = (string) preg_replace('#\.[^.]*$#', '', basename($identifier));
                $class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);

                $this->css  .= "\n.icon-$class {\n"
                        . "\tbackground: url($identifier) no-repeat;\n"
                    . "}\n";

                $classes[$identifier] = "icon-$class";
            }
        }

        return $classes[$identifier];
    }
}
