<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models\Adapters;

use Hubzero\Base\Obj;
use Pathway;
use Lang;

/**
 * Abstract adapter class for a wishlist
 */
abstract class Base extends Obj
{
	/**
	 * The object the referenceid references
	 *
	 * @var  object
	 */
	protected $_item = null;

	/**
	 * Script name
	 *
	 * @var  string
	 */
	protected $_base = 'index.php';

	/**
	 * URL segments
	 *
	 * @var  array
	 */
	protected $_segments = array();

	/**
	 * Scope name
	 *
	 * @var  string
	 */
	protected $_scope = '';

	/**
	 * Constructor
	 *
	 * @param   integer  $referenceid  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid);
	}

	/**
	 * Can this adapter handle the provided scope?
	 *
	 * @return  bool
	 */
	public function handles($scope)
	{
		return ($this->_scope && $this->_scope == $scope);
	}

	/**
	 * Get owners
	 *
	 * @return  array
	 */
	public function owners()
	{
		return array();
	}

	/**
	 * Get groups
	 *
	 * @return  array
	 */
	public function groups()
	{
		return array();
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return  string
	 */
	public function title()
	{
		return Lang::txt('COM_WISHLIST');
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param   string  $key  Property to retrieve
	 * @return  string
	 */
	public function item($key='')
	{
		if ($key && is_object($this->_item))
		{
			return $this->_item->$key;
		}
		return $this->_item;
	}

	/**
	 * Does the item exists?
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		if ($this->item() && $this->item('id'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->_base;
	}

	/**
	 * Flatten array of segments into querystring
	 *
	 * @param   array   $segments  An associative array of querystring bits
	 * @return  string
	 */
	protected function _build(array $segments)
	{
		$bits = array();
		foreach ($segments as $key => $param)
		{
			if ($param)
			{
				$bits[] = $key . '=' . $param;
			}
		}
		return implode('&', $bits);
	}

	/**
	 * Append an item to the breadcrumb trail.
	 * If no item is provided, it will build the trail up to the list
	 *
	 * @param   string  $title  Breadcrumb title
	 * @param   string  $url    Breadcrumb URL
	 * @return  string
	 */
	public function pathway($title=null, $url=null)
	{
		if (!$title)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->get('option'))),
				'index.php?option=' . $this->get('option')
			);
		}
		else
		{
			Pathway::append(
				$title,
				$url
			);
		}

		return $this;
	}
}
