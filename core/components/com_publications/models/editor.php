<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Model for a handler editor
 */
class Editor extends Obj
{
	/**
	 * Handler object
	 *
	 * @var  object
	 */
	public $handler = null;

	/**
	 * Database
	 *
	 * @var  object
	 */
	private $_db = null;

	/**
	 * Configs
	 *
	 * @var  object
	 */
	public $_configs = null;

	/**
	 * Constructor
	 *
	 * @param   object  $handler
	 * @param   object  $configs
	 * @return  void
	 */
	public function __construct($handler, $configs)
	{
		$this->_db = \App::get('db');

		$this->handler 	= $handler;
		$this->configs 	= $configs;
	}

	/**
	 * Draw status
	 *
	 * @return  string
	 */
	public function drawStatus()
	{
		return $this->handler->drawStatus($this);
	}

	/**
	 * Draw editor content
	 *
	 * @return  string
	 */
	public function drawEditor()
	{
		return $this->handler->drawEditor($this);
	}
}
