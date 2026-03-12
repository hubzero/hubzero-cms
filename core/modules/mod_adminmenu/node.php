<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Adminmenu;

use Hubzero\Base\Obj;

/**
 * Menu node class
 */
class Node extends Obj
{
    /**
     * Parent node
     * @var    object
     *
     * @since  2.1.13
     */
    protected $parent = null;

    /**
     * Array of Children
     *
     * @var    array
     * @since  2.1.13
     */
    protected $children = array();

    /**
     * Node Title
     *
     * @var  string
     */
    public $title = null;

    /**
     * Node Id
     *
     * @var  string
     */
    public $id = null;

    /**
     * Node Link
     *
     * @var  string
     */
    public $link = null;

    /**
     * Link Target
     *
     * @var  string
     */
    public $target = null;

    /**
     * CSS Class for node
     *
     * @var  string
     */
    public $class = null;

    /**
     * Active Node?
     *
     * @var  boolean
     */
    public $active = false;

    /**
     * Constructor
     *
     * @param   string   $title      Node title
     * @param   string   $link       URL for the node
     * @param   string   $class      CSS class
     * @param   mixed    $active     True/false to force active state, null to auto-detect from link URL
     * @param   string   $target     Link target attribute
     * @param   string   $titleicon  Optional icon HTML appended to title
     * @return  void
     */
    public function __construct($title, $link = null, $class = null, $active = null, $target = null, $titleicon = null)
    {
        $this->title  = $titleicon ? $title . $titleicon : $title;
        if ($link && substr($link, 0, strlen('index.php')) == 'index.php') {
            $link = \Hubzero\Facades\Route::url($link);
        }
        $this->link   = $link ? \Hubzero\Utility\Str::ampReplace($link) : '';
        $this->class  = $class;

        if ($active === null) {
            $this->active = $this->detectActive($link);
        } else {
            $this->active = (bool) $active;
        }

        $this->id = null;
        if (!empty($link) && $link !== '#') {
            $params = with(new \Hubzero\Utility\Uri($link))->getQuery(true);

            $parts = array();
            foreach ($params as $name => $value) {
                $parts[] = str_replace(array('.', '_'), '-', $value);
            }

            $this->id = implode('-', $parts);
        }

        $this->target = $target;
    }

    /**
     * Auto-detect active state by comparing the link's query params against
     * the current request. The node is active when:
     *   1. All params in the link match the current request, AND
     *   2. The current request has no extra navigation params (option, view,
     *      controller) that the link does not explicitly specify.
     *
     * This ensures that a link to ?option=com_cache is only active on the
     * default cache view, not on ?option=com_cache&view=purge.
     *
     * @param   string  $link  The raw link before Route::url() processing
     * @return  bool
     */
    protected function detectActive($link)
    {
        if (!$link || $link === '#') {
            return false;
        }

        $linkParams = with(new \Hubzero\Utility\Uri($link))->getQuery(true);
        if (empty($linkParams)) {
            return false;
        }

        // All link params must match current request
        foreach ($linkParams as $key => $value) {
            if (\Hubzero\Facades\Request::getCmd($key) !== $value) {
                return false;
            }
        }

        // Current request must not have extra navigation params the link omits
        foreach (['option', 'view', 'controller'] as $navKey) {
            if (!isset($linkParams[$navKey]) && \Hubzero\Facades\Request::getCmd($navKey, '') !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Add child to this node
     *
     * If the child already has a parent, the link is unset
     *
     * @param   object  &$child  The child to be added
     * @return  void
     * @since   2.1.13
     */
    public function addChild(&$child)
    {
        if ($child instanceof Node) {
            $child->setParent($this);
        }
    }

    /**
     * Set the parent of a this node
     *
     * If the node already has a parent, the link is unset
     *
     * @param   mixed  &$parent  The Node for parent to be set or null
     * @return  void
     * @since   2.1.13
     */
    public function setParent(&$parent)
    {
        if ($parent instanceof Node || is_null($parent)) {
            $hash = spl_object_hash($this);
            if (!is_null($this->parent)) {
                unset($this->parent->children[$hash]);
            }
            if (!is_null($parent)) {
                $parent->children[$hash] = & $this;
            }
            $this->parent = & $parent;
        }
    }

    /**
     * Get the children of this node
     *
     * @return  array    The children
     * @since   2.1.13
     */
    public function &getChildren()
    {
        return $this->children;
    }

    /**
     * Get the parent of this node
     *
     * @return  mixed   Node object with the parent or null for no parent
     * @since   2.1.13
     */
    public function &getParent()
    {
        return $this->parent;
    }

    /**
     * Test if this node has children
     *
     * @return   boolean  True if there are children
     * @since    2.1.13
     */
    public function hasChildren()
    {
        return (bool) count($this->children);
    }

    /**
     * Test if this node has a parent
     *
     * @return  boolean  True if there is a parent
     * @since   2.1.13
     */
    public function hasParent()
    {
        return $this->getParent() != null;
    }
}
