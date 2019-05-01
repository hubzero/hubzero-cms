<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


/**
 * Custom handler. Parent class for all custom handlers.
 */
class Custom_Handler
{
	// Database instance
	var $db = null;

	// Item info
	var $item;

	// Cart ID
	var $crtId;

	/**
	 * Constructor
	 *
	 */
	public function __construct($item, $crtId)
	{
		$this->item = $item;
		$this->crtId = $crtId;
	}
}
