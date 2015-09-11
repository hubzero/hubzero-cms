<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\AdminMenu;

include_once __DIR__ . DS . 'node.php';

jimport('joomla.base.tree');

/**
 * Extended class for rendering nested menus
 */
class Tree extends \JTree
{
	/**
	 * CSS string to add to document head
	 *
	 * @var  string
	 */
	protected $_css = null;

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
		if ($this->_current->hasChildren())
		{
			$class = ' class="node"';
		}

		if ($this->_current->class == 'separator')
		{
			$class = ' class="separator"';
		}

		if ($this->_current->class == 'disabled')
		{
			$class = ' class="disabled"';
		}

		// Print the item
		echo '<li' . $class . '>';

		// Print a link if it exists
		$linkClass = '';

		if ($this->_current->link != null)
		{
			$linkClass = $this->getIconClass($this->_current->class);
			if (!empty($linkClass))
			{
				$linkClass = ' class="' . $linkClass . '"';
			}
		}

		if ($this->_current->link != null && $this->_current->target != null)
		{
			echo '<a' . $linkClass . ' href="' . $this->_current->link . '" target="' . $this->_current->target . '">' . $this->_current->title . '</a>';
		}
		elseif ($this->_current->link != null && $this->_current->target == null)
		{
			echo '<a' . $linkClass . ' href="' . $this->_current->link . '">' . $this->_current->title . '</a>';
		}
		elseif ($this->_current->title != null)
		{
			echo '<a>' . $this->_current->title . '</a>';
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
				$classes[$identifier] = "icon-16-$class";
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

				$this->_css  .= "\n.icon-16-$class {\n" .
						"\tbackground: url($identifier) no-repeat;\n" .
					"}\n";

				$classes[$identifier] = "icon-16-$class";
			}
		}
		return $classes[$identifier];
	}
}
