<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\AdminMenu;

include_once __DIR__ . DS . 'node.php';

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
	protected $_css = null;

	/**
	 * Root node
	 *
	 * @var  object
	 */
	protected $_root = null;

	/**
	 * Current working node
	 *
	 * @var  object
	 */
	protected $_current = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_root = new Node('ROOT');
		$this->_current =& $this->_root;
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
		$this->_current->addChild($node);

		if ($setCurrent)
		{
			$this->_current = &$node;
		}
	}

	/**
	 * Method to get the parent
	 *
	 * @return  void
	 */
	public function getParent()
	{
		$this->_current = &$this->_current->getParent();
	}

	/**
	 * Method to get the parent
	 *
	 * @return  void
	 */
	public function reset()
	{
		$this->_current = &$this->_root;
	}

	/**
	 * Add a separator
	 *
	 * @return  object
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

		if (!empty($id))
		{
			$id = 'id="' . $id . '"';
		}

		if (!empty($class))
		{
			$class = 'class="' . $class . '"';
		}

		// Recurse through children if they exist
		while ($this->_current->hasChildren())
		{
			echo '<ul ' . $id . ' ' . $class . ">\n";
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current =& $child;
				$this->renderLevel($depth++);
			}
			echo "</ul>\n";
		}

		if ($this->_css)
		{
			// Add style to document head
			\Document::addStyleDeclaration($this->_css);
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

		if ($this->_current->class)
		{
			$classes = explode(' ', trim($this->_current->class));
			foreach ($classes as $i => $clas)
			{
				if (substr($clas, 0, strlen('class:')) == 'class:')
				{
					$iconClass = $clas;
					unset($classes[$i]);
				}
			}
		}

		if ($this->_current->hasChildren())
		{
			$classes[] = 'node';
		}

		if ($this->_current->active)
		{
			$classes[] = 'active';
		}

		if (!empty($classes))
		{
			$class = ' class="' . implode(' ', $classes) . '"';
		}

		// Print the item
		echo '<li' . $class . '>';

		// Print a link if it exists
		$linkClass = $this->getIconClass($iconClass);
		if (!empty($linkClass))
		{
			$linkClass = ' class="' . $linkClass . '"';
		}

		if ($this->_current->link != null && $this->_current->target != null)
		{
			echo '<a' . $linkClass . ' href="' . $this->_current->link . '" rel="noopener" target="' . $this->_current->target . '">' . $this->_current->title . '</a>';
			if ($this->_current->hasChildren())
			{
				echo '<span class="toggler" aria-hidden="true"></span>';
			}
		}
		elseif ($this->_current->link != null && $this->_current->target == null)
		{
			echo '<a' . $linkClass . ' href="' . $this->_current->link . '">' . $this->_current->title . '</a>';
			if ($this->_current->hasChildren())
			{
				echo '<span class="toggler" aria-hidden="true"></span>';
			}
		}
		elseif ($this->_current->title != null)
		{
			echo '<a' . $linkClass . '>' . $this->_current->title . '</a>';
			if ($this->_current->hasChildren())
			{
				echo '<span class="toggler" aria-hidden="true"></span>';
			}
		}
		else
		{
			echo '<span></span>';
		}

		// Recurse through children if they exist
		while ($this->_current->hasChildren())
		{
			if ($this->_current->class)
			{
				$id = '';
				if (!empty($this->_current->id))
				{
					$id = ' id="menu-' . strtolower($this->_current->id) . '"';
				}
				echo '<ul' . $id . ' class="menu-component">' . "\n";
			}
			else
			{
				echo '<ul>' . "\n";
			}

			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current =& $child;
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
		if (!is_array($classes))
		{
			$classes = array();
		}

		// If we don't already know about the class... build it and mark it
		// known so we don't have to build it again
		if (!isset($classes[$identifier]))
		{
			if (substr($identifier, 0, 6) == 'class:')
			{
				// We were passed a class name
				$class = substr($identifier, 6);
				$classes[$identifier] = "icon-$class";
			}
			else
			{
				if ($identifier == null)
				{
					return null;
				}

				// Build the CSS class for the icon
				$class = preg_replace('#\.[^.]*$#', '', basename($identifier));
				$class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);

				$this->_css  .= "\n.icon-$class {\n" .
						"\tbackground: url($identifier) no-repeat;\n" .
					"}\n";

				$classes[$identifier] = "icon-$class";
			}
		}

		return $classes[$identifier];
	}
}
