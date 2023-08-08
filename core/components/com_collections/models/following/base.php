<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Following;

use Hubzero\Base\Obj;

/**
 * Abstract model class for following
 */

/** @phpstan-consistent-constructor */
abstract class Base extends Obj
{
	/**
	 * Varies
	 *
	 * @var object
	 */
	private $_obj = null;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $_image = null;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = 'index.php';

	/**
	 * Constructor
	 *
	 * @param   integer  $id  ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
	}

	/**
	 * Returns a reference to this object
	 *
	 * @param   integer  $oid  User ID
	 * @return  object
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new static($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return  mixed
	 */
	public function creator()
	{
		return null;
	}

	/**
	 * Get this item's image
	 *
	 * @return  string
	 */
	public function image()
	{
		return $this->_image;
	}

	/**
	 * Get this item's alias
	 *
	 * @return  string
	 */
	public function alias()
	{
		return '';
	}

	/**
	 * Get this item's title
	 *
	 * @return  string
	 */
	public function title()
	{
		return '';
	}

	/**
	 * Get the URL for this item
	 *
	 * @param   string  $what
	 * @return  string
	 */
	public function link($what='base')
	{
		return $this->_baselink;
	}
}
