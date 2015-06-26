<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

