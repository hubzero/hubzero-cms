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

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Base\Model;
use Hubzero\User\Profile;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use Request;

/**
 * Model class for a forum post attachment
 */
class Attachment extends Model
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Hubzero\\Item\\Comment\\File';

	/**
	 * File size
	 *
	 * @var string
	 */
	private $_size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var array
	 */
	private $_dimensions = null;

	/**
	 * Constructor
	 *
	 * @param      mixed   $oid ID (integer), alias (string), array or object
	 * @param      integer $pid Post ID
	 * @return     void
	 */
	public function __construct($oid=null, $pid=null)
	{
		$this->_db = \App::get('db');

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . \Lang::txt('Table class must be an instance of JTable.')
			);
			throw new \LogicException(\Lang::txt('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
		else if ($pid)
		{
			$this->_tbl->loadByComment($pid);
		}
	}

	/**
	 * Returns a reference to an attachment model
	 *
	 * @param      mixed   $oid ID (int), alias (string), array, or object
	 * @param      integer $pid Post ID
	 * @return     object
	 */
	static function &getInstance($oid=0, $pid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $pid . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $pid . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $pid . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $pid);
		}

		return $instances[$key];
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function size()
	{
		if ($this->_size === null)
		{
			$this->_size = 0;
			if (file_exists($this->link('filepath')))
			{
				$this->_size = filesize($this->link('filepath'));
			}
		}

		return $this->_size;
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function width()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[0];
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function height()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[1];
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		static $path;

		if (!$path)
		{
			$path = PATH_APP . '/site/comments';
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				$link = $path . DS . $this->get('comment_id');
			break;

			case 'path':
			case 'filepath':
				$link = $path . DS . $this->get('comment_id') . DS . $this->get('filename');
			break;

			case 'permalink':
			default:
				$link = rtrim(Request::base(), '/') . substr(PATH_APP, strlen(PATH_ROOT)) . '/site/comments/' . $this->get('comment_id') . '/' . $this->get('filename');
			break;
		}

		return $link;
	}
}

