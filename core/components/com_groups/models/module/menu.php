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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Module;

use Components\Groups\Tables;
use Hubzero\Base\Model;
use Lang;

/**
 * Group module menu model class
 */
class Menu extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\ModuleMenu';

	/**
	 * Constructor
	 *
	 * @param   mixed $oid
	 * @return  void
	 */
	public function __construct($oid)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ModuleMenu($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get page title
	 * 
	 * @return  string
	 */
	public function getPageTitle()
	{
		if ($this->get('pageid') == 0)
		{
			return  Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_ALL_PAGES');
		}

		if ($this->get('pageid') == -1)
		{
			return  Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_NO_PAGES');
		}

		// new group page
		$tbl = new Tables\Page($this->_db);

		// load page
		$tbl->load($this->get('pageid'));

		// return page title
		return $tbl->get('title');
	}

}