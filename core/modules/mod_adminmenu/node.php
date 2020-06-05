<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\AdminMenu;

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
	protected $_parent = null;

	/**
	 * Array of Children
	 *
	 * @var    array
	 * @since  2.1.13
	 */
	protected $_children = array();

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
	 * @return  void
	 */
	public function __construct($title, $link = null, $class = null, $active = false, $target = null, $titleicon = null)
	{
		$this->title  = $titleicon ? $title . $titleicon : $title;
		if ($link && substr($link, 0, strlen('index.php')) == 'index.php')
		{
			$link = \Route::url($link);
		}
		$this->link   = \Hubzero\Utility\Str::ampReplace($link);
		$this->class  = $class;
		$this->active = $active;

		$this->id = null;
		if (!empty($link) && $link !== '#')
		{
			$params = with(new \Hubzero\Utility\Uri($link))->getQuery(true);

			$parts = array();
			foreach ($params as $name => $value)
			{
				$parts[] = str_replace(array('.', '_'), '-', $value);
			}

			$this->id = implode('-', $parts);
		}

		$this->target = $target;
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
		if ($child instanceof Node)
		{
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
		if ($parent instanceof Node || is_null($parent))
		{
			$hash = spl_object_hash($this);
			if (!is_null($this->_parent))
			{
				unset($this->_parent->children[$hash]);
			}
			if (!is_null($parent))
			{
				$parent->_children[$hash] = & $this;
			}
			$this->_parent = & $parent;
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
		return $this->_children;
	}

	/**
	 * Get the parent of this node
	 *
	 * @return  mixed   Node object with the parent or null for no parent
	 * @since   2.1.13
	 */
	public function &getParent()
	{
		return $this->_parent;
	}

	/**
	 * Test if this node has children
	 *
	 * @return   boolean  True if there are children
	 * @since    2.1.13
	 */
	public function hasChildren()
	{
		return (bool) count($this->_children);
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
