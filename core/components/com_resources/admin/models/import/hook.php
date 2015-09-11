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

namespace Components\Resources\Models\Import;

use Hubzero\Base\Model;

// included needed tables
require_once dirname(dirname(dirname(__DIR__))) . DS . 'tables' . DS . 'import' . DS . 'hook.php';

/**
 * Import Runs Model
 */
class Hook extends Model
{
	/**
	 * JTable
	 *
	 * @var  string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Resources\\Tables\\Import\\Hook';

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Object Id
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		// create needed objects
		$this->_db = \App::get('db');

		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
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
	 * Return imports filespace path
	 *
	 * @return  string
	 */
	public function fileSpacePath()
	{
		// get com resources params
		$params = \Component::params('com_resources');

		// build upload path
		$uploadPath = $params->get('import_hooks_uploadpath', '/site/resources/import/hooks');
		$uploadPath = PATH_APP . DS . trim($uploadPath, DS) . DS . $this->get('id');

		// return path
		return $uploadPath;
	}
}