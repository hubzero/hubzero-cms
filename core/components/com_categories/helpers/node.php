<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Categories\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Config\Registry;
use User;

/**
 * Helper class to load Categorytree
 */
class Node extends Obj
{
	/**
	 * Primary key
	 *
	 * @var  integer
	 */
	public $id = null;

	/**
	 * The id of the category in the asset table
	 *
	 * @var  integer
	 */
	public $asset_id = null;

	/**
	 * The id of the parent of category in the asset table, 0 for category root
	 *
	 * @var  integer
	 */
	public $parent_id = null;

	/**
	 * The lft value for this category in the category tree
	 *
	 * @var  integer
	 */
	public $lft = null;

	/**
	 * The rgt value for this category in the category tree
	 *
	 * @var  integer
	 */
	public $rgt = null;

	/**
	 * The depth of this category's position in the category tree
	 *
	 * @var  integer
	 */
	public $level = null;

	/**
	 * The extension this category is associated with
	 *
	 * @var  integer
	 */
	public $extension = null;

	/**
	 * The menu title for the category (a short name)
	 *
	 * @var  string
	 */
	public $title = null;

	/**
	 * The the alias for the category
	 *
	 * @var  string
	 */
	public $alias = null;

	/**
	 * Description of the category.
	 *
	 * @var  string
	 */
	public $description = null;

	/**
	 * The publication status of the category
	 *
	 * @var  boolean
	 */
	public $published = null;

	/**
	 * Whether the category is or is not checked out
	 *
	 * @var  integer
	 */
	public $checked_out = 0;

	/**
	 * The time at which the category was checked out
	 *
	 * @var  string
	 */
	public $checked_out_time = 0;

	/**
	 * Access level for the category
	 *
	 * @var  integer
	 */
	public $access = null;

	/**
	 * JSON string of parameters
	 *
	 * @var  string
	 */
	public $params = null;

	/**
	 * Metadata description
	 *
	 * @var  string
	 */
	public $metadesc = null;

	/**
	 * Key words for meta data
	 *
	 * @var  string
	 */
	public $metakey = null;

	/**
	 * JSON string of other meta data
	 *
	 * @var  string
	 */
	public $metadata = null;

	/**
	 * Created user ID
	 *
	 * @var  integer
	 */
	public $created_user_id = null;

	/**
	 * The time at which the category was created
	 *
	 * @var  string
	 */
	public $created_time = null;

	/**
	 * Modified user ID
	 *
	 * @var  integer
	 */
	public $modified_user_id = null;

	/**
	 * The time at which the category was modified
	 *
	 * @var  string
	 */
	public $modified_time = null;

	/**
	 * Nmber of times the category has been viewed
	 *
	 * @var  integer
	 */
	public $hits = null;

	/**
	 * The language for the category in xx-XX format
	 *
	 * @var  string
	 */
	public $language = null;

	/**
	 * Number of items in this category or descendants of this category
	 *
	 * @var  integer
	 */
	public $numitems = null;

	/**
	 * Number of children items
	 *
	 * @var  integer
	 */
	public $childrennumitems = null;

	/**
	 * Slug fo the category (used in URL)
	 *
	 * @var  string
	 */
	public $slug = null;

	/**
	 * Array of  assets
	 *
	 * @var  array
	 */
	public $assets = null;

	/**
	 * Parent Category object
	 *
	 * @var  object
	 */
	protected $_parent = null;

	/**
	 * Array of Children
	 *
	 * @var  array
	 */
	protected $_children = array();

	/**
	 * Path from root to this category
	 *
	 * @var  array
	 */
	protected $_path = array();

	/**
	 * Category left of this one
	 *
	 * @var  integer
	 */
	protected $_leftSibling = null;

	/**
	 * Category right of this one
	 *
	 * @var  integer
	 */
	protected $_rightSibling = null;

	/**
	 * true if all children have been loaded
	 *
	 * @var  boolean
	 */
	protected $_allChildrenloaded = false;

	/**
	 * Constructor of this tree
	 *
	 * @var
	 */
	protected $_constructor = null;

	/**
	 * Class constructor
	 *
	 * @param   array    $category      The category data.
	 * @param   object   &$constructor  The tree constructor.
	 * @return  boolean
	 */
	public function __construct($category = null, &$constructor = null)
	{
		if ($category)
		{
			$this->setProperties($category);
			if ($constructor)
			{
				$this->_constructor = &$constructor;
			}

			return true;
		}

		return false;
	}

	/**
	 * Set the parent of this category
	 *
	 * If the category already has a parent, the link is unset
	 *
	 * @param   mixed  &$parent  Node for the parent to be set or null
	 * @return  void
	 */
	public function setParent(&$parent)
	{
		if ($parent instanceof self || is_null($parent))
		{
			if (!is_null($this->_parent))
			{
				$key = array_search($this, $this->_parent->_children);
				unset($this->_parent->_children[$key]);
			}

			if (!is_null($parent))
			{
				$parent->_children[] = & $this;
			}

			$this->_parent = & $parent;

			if ($this->id != 'root')
			{
				if ($this->parent_id != 1)
				{
					$this->_path = $parent->getPath();
				}
				$this->_path[] = $this->id . ':' . $this->alias;
			}

			if (count($parent->_children) > 1)
			{
				end($parent->_children);
				$this->_leftSibling = prev($parent->_children);
				$this->_leftSibling->_rightsibling = &$this;
			}
		}
	}

	/**
	 * Add child to this node
	 *
	 * If the child already has a parent, the link is unset
	 *
	 * @param   object  &$child  The child to be added.
	 * @return  void
	 */
	public function addChild(&$child)
	{
		if ($child instanceof self)
		{
			$child->setParent($this);
		}
	}

	/**
	 * Remove a specific child
	 *
	 * @param   integer  $id  ID of a category
	 * @return  void
	 */
	public function removeChild($id)
	{
		$key = array_search($this, $this->_parent->_children);
		unset($this->_parent->_children[$key]);
	}

	/**
	 * Get the children of this node
	 *
	 * @param   boolean  $recursive  False by default
	 * @return  array    The children
	 */
	public function &getChildren($recursive = false)
	{
		if (!$this->_allChildrenloaded)
		{
			$temp = $this->_constructor->get($this->id, true);
			if ($temp)
			{
				$this->_children = $temp->getChildren();
				$this->_leftSibling = $temp->getSibling(false);
				$this->_rightSibling = $temp->getSibling(true);
				$this->setAllLoaded();
			}
		}

		if ($recursive)
		{
			$items = array();
			foreach ($this->_children as $child)
			{
				$items[] = $child;
				$items = array_merge($items, $child->getChildren(true));
			}
			return $items;
		}

		return $this->_children;
	}

	/**
	 * Get the parent of this node
	 *
	 * @return  mixed  Node or null
	 */
	public function &getParent()
	{
		return $this->_parent;
	}

	/**
	 * Test if this node has children
	 *
	 * @return  integer  True if there is a child
	 */
	public function hasChildren()
	{
		return count($this->_children);
	}

	/**
	 * Test if this node has a parent
	 *
	 * @return  boolean  True if there is a parent
	 */
	public function hasParent()
	{
		return $this->getParent() != null;
	}

	/**
	 * Function to set the left or right sibling of a category
	 *
	 * @param   object   $sibling  Node object for the sibling
	 * @param   boolean  $right    If set to false, the sibling is the left one
	 * @return  void
	 */
	public function setSibling($sibling, $right = true)
	{
		if ($right)
		{
			$this->_rightSibling = $sibling;
		}
		else
		{
			$this->_leftSibling = $sibling;
		}
	}

	/**
	 * Returns the right or left sibling of a category
	 *
	 * @param   boolean  $right  If set to false, returns the left sibling
	 * @return  mixed    Node object with the sibling information or
	 *                   NULL if there is no sibling on that side.
	 */
	public function getSibling($right = true)
	{
		if (!$this->_allChildrenloaded)
		{
			$temp = $this->_constructor->get($this->id, true);
			$this->_children = $temp->getChildren();
			$this->_leftSibling = $temp->getSibling(false);
			$this->_rightSibling = $temp->getSibling(true);
			$this->setAllLoaded();
		}

		if ($right)
		{
			return $this->_rightSibling;
		}
		else
		{
			return $this->_leftSibling;
		}
	}

	/**
	 * Returns the category parameters
	 *
	 * @return  object
	 */
	public function getParams()
	{
		if (!($this->params instanceof Registry))
		{
			$temp = new Registry($this->params);

			$this->params = $temp;
		}

		return $this->params;
	}

	/**
	 * Returns the category metadata
	 *
	 * @return  object  A Registry object containing the metadata
	 */
	public function getMetadata()
	{
		if (!($this->metadata instanceof Registry))
		{
			$temp = new Registry($this->metadata);

			$this->metadata = $temp;
		}

		return $this->metadata;
	}

	/**
	 * Returns the category path to the root category
	 *
	 * @return  array
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * Returns the user that created the category
	 *
	 * @param   boolean  $modified_user  Returns the modified_user when set to true
	 * @return  object   A User object containing a userid
	 */
	public function getAuthor($modified_user = false)
	{
		if ($modified_user)
		{
			return User::getInstance($this->modified_user_id);
		}

		return User::getInstance($this->created_user_id);
	}

	/**
	 * Set to load all children
	 *
	 * @return  void
	 */
	public function setAllLoaded()
	{
		$this->_allChildrenloaded = true;
		foreach ($this->_children as $child)
		{
			$child->setAllLoaded();
		}
	}

	/**
	 * Returns the number of items.
	 *
	 * @param   boolean  $recursive  If false number of children, if true number of descendants
	 * @return  integer  Number of children or descendants
	 */
	public function getNumItems($recursive = false)
	{
		if ($recursive)
		{
			$count = $this->numitems;

			foreach ($this->getChildren() as $child)
			{
				$count = $count + $child->getNumItems(true);
			}

			return $count;
		}

		return $this->numitems;
	}
}
