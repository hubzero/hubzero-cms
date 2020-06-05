<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Delete operation
 */
class _DiffOp_Delete extends _DiffOp
{
	/**
	 * Description for 'type'
	 *
	 * @var string
	 */
	public $type = 'delete';

	/**
	 * Short description for '_DiffOp_Delete'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($lines)
	{
		$this->orig = $lines;
		$this->closing = false;
	}

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	public function reverse()
	{
		return new _DiffOp_Add($this->orig);
	}
}
