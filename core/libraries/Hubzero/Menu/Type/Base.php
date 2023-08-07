<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Menu\Type;

use Hubzero\Config\Registry;
use Hubzero\Base\Obj;

/**
 * Base Menu class
 *
 * Inspired by Joomla's JMenu class
 */
class Base extends Obj
{
	/**
	 * Array to hold the menu items
	 *
	 * @var  array
	 */
	protected $_items = array();

	/**
	 * Identifier of the default menu item
	 *
	 * @var  integer
	 */
	protected $_default = array();

	/**
	 * Identifier of the active menu item
	 *
	 * @var  integer
	 */
	protected $_active = 0;

	/**
	 * Filter by set language
	 *
	 * @var string
	 */
	protected $language_filter;

	/**
	 * Default language
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * Access level
	 *
	 * @var int
	 */
	protected $access = 1;

	/**
	 * Database connection
	 *
	 * @var object
	 */
	protected $db = null;

	/**
	 * Menu instances container.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  An array of configuration options.
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Load the menu items
		$this->load();

		foreach ($this->_items as $item)
		{
			if ($item->home)
			{
				$this->_default[trim($item->language)] = $item->id;
			}

			// Decode the item params
			$item->params = new Registry($item->params);
		}
	}

	/**
	 * Get menu item by id
	 *
	 * @param   integer  $id  The item id
	 * @return  mixed    The item object, or null if not found
	 */
	public function getItem($id)
	{
		$result = null;

		if (isset($this->_items[$id]))
		{
			$result =& $this->_items[$id];
		}

		return $result;
	}

	/**
	 * Set the default item by id and language code.
	 *
	 * @param   integer  $id        The menu item id.
	 * @param   string   $language  The language cod (since 1.6).
	 * @return  boolean  True, if successful
	 */
	public function setDefault($id, $language = '')
	{
		if (isset($this->_items[$id]))
		{
			$this->_default[$language] = $id;
			return true;
		}

		return false;
	}

	/**
	 * Get the default item by language code.
	 *
	 * @param   string  $language  The language code, default value of * means all.
	 * @return  mixed   The item object
	 */
	public function getDefault($language = '*')
	{
		if (array_key_exists($language, $this->_default))
		{
			return $this->_items[$this->_default[$language]];
		}

		if (array_key_exists('*', $this->_default))
		{
			return $this->_items[$this->_default['*']];
		}

		return 0;
	}

	/**
	 * Set the default item by id
	 *
	 * @param   integer  $id  The item id
	 * @return  mixed    If successful the active item, otherwise null
	 */
	public function setActive($id)
	{
		if (isset($this->_items[$id]))
		{
			$this->_active = $id;

			$result =& $this->_items[$id];

			return $result;
		}

		return null;
	}

	/**
	 * Get menu item by id.
	 *
	 * @return  object  The item object.
	 */
	public function getActive()
	{
		if ($this->_active)
		{
			$item =& $this->_items[$this->_active];

			return $item;
		}

		return null;
	}

	/**
	 * Gets menu items by attribute
	 *
	 * @param   mixed    $attributes  The field name(s).
	 * @param   mixed    $values      The value(s) of the field. If an array, need to match field names
	 *                                each attribute may have multiple values to lookup for.
	 * @param   boolean  $firstonly   If true, only returns the first item found
	 * @return  array
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$items      = array();
		$attributes = (array) $attributes;
		$values     = (array) $values;

		foreach ($this->_items as $item)
		{
			if (!is_object($item))
			{
				continue;
			}

			$test = true;
			for ($i = 0, $count = count($attributes); $i < $count; $i++)
			{
				$c = $attributes[$i];
				if (is_array($values[$i]))
				{
					if (!in_array($item->$c, $values[$i]))
					{
						$test = false;
						break;
					}
				}
				else
				{
					if ($item->$c != $values[$i])
					{
						$test = false;
						break;
					}
				}
			}

			if ($test)
			{
				if ($firstonly)
				{
					return $item;
				}

				$items[] = $item;
			}
		}

		return $items;
	}

	/**
	 * Gets the parameter object for a certain menu item
	 *
	 * @param   integer  $id  The item id
	 * @return  object   A Registry object
	 */
	public function getParams($id)
	{
		if ($menu = $this->getItem($id))
		{
			return $menu->params;
		}

		return new Registry;
	}

	/**
	 * Getter for the menu array
	 *
	 * @return  array
	 */
	public function getMenu()
	{
		return $this->_items;
	}

	/**
	 * Method to check object authorization against an access control
	 * object and optionally an access extension object
	 *
	 * @param   integer  $id  The menu id
	 * @return  boolean  True if authorised
	 */
	public function authorise($id)
	{
		$menu = $this->getItem($id);

		if ($menu)
		{
			return in_array((int) $menu->access, $this->get('access', array(0)));
		}

		return true;
	}

	/**
	 * Loads the menu items
	 *
	 * @return  void
	 */
	public function load()
	{
	}
}
