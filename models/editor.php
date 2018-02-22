<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
