<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication status model class
 */
class Status extends Obj
{
	/**
	 * Int(1)
	 * 1 = requirement satisfied
	 * 0 = requirement not satisfied
	 * 2 = requirement partially satisfied (incomplete)
	 * 3 = not available
	 */
	public $status = null;

	/**
	 * Status message
	 */
	public $message = null;

	/**
	 * For nested blocks
	 */
	public $elements = null;

	/**
	 * Time of last status update
	 */
	public $lastupdate = null;
}
