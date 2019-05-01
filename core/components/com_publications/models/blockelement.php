<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication block element class
 */
class BlockElement extends Obj
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
	 * Get the element name
	 *
	 * @return  string  type of the parameter
	 */
	public function getName()
	{
		return $this->_name;
	}
}
