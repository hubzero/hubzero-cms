<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publications block base class
 */
class Block extends Obj
{
	/**
	 * Element name
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Block manifest
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @var  string
	 */
	protected $_manifest = null;

	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var  object
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   object  $parent
	 * @return  void
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get the block name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Get property
	 *
	 * @param   string  $name
	 * @return  mixed
	 */
	public function getProperty($name)
	{
		if (isset($this->$name))
		{
			return $this->$name;
		}

		return false;
	}
}
