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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Route;
use Lang;

require_once(__DIR__ . DS . 'base.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'wish' . DS . 'attachment.php');

/**
 * Model class for a wish attachment
 */
class Attachment extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Wishlist\\Tables\\Wish\\Attachment';

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid     ID (int), alias (string), array, or object
	 * @param   integer  $wishid  Wish ID
	 * @return  void
	 */
	public function __construct($oid=null, $wishid=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of JTable.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					if ($wishid)
					{
						$this->_tbl->loadAttachment($oid, $wishid);
					}
					else
					{
						$this->_tbl->load($oid);
					}
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to this model
	 *
	 * @param   mixed    $oid     ID (int), alias (string), array, or object
	 * @param   integer  $wishid  Wish ID
	 * @return  object
	 */
	static function &getInstance($oid=0, $wishid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $wishid . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $wishid . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $wishid . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $wishid);
		}

		return $instances[$key];
	}

	/**
	 * Returns a link or path to the file
	 *
	 * @param   string  $type
	 * @return  string
	 */
	public function link($type)
	{
		$type = strtolower($type);

		switch ($type)
		{
			case 'download':
				return Route::url('index.php?option=com_wishlist&task=wish&category=' . $this->get('category') . '&rid=' . $this->get('referenceid') . '&wishid=' . $this->get('wish'));
			break;

			case 'dir':
				return PATH_APP . DS . trim($this->config('webpath'), '/') . DS . $this->get('wish');
			break;

			case 'file':
			case 'server':
				return PATH_APP . DS . trim($this->config('webpath'), '/') . DS . $this->get('wish') . DS . ltrim($this->get('filename'), '/');
			break;
		}
	}

	/**
	 * Checks the file type and determines if it's in the
	 * whitelist of allowed extensions
	 *
	 * @return  boolean  True if allowed file type
	 */
	public function isAllowedType()
	{
		$ext = strtolower(\Filesystem::extension($this->get('filename')));

		if (!in_array($ext, explode(',', $this->config('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,wav,mp3,eps,ppt,pps,swf,tar,tex,gz'))))
		{
			return false;
		}

		return true;
	}
}

