<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'CitationsDownloadAbstract'
 * 
 * Long description (if any) ...
 */
abstract class CitationsDownloadAbstract
{

	/**
	 * Description for '_mime'
	 * 
	 * @var string
	 */
	protected $_mime = '';

	/**
	 * Description for '_extension'
	 * 
	 * @var string
	 */
	protected $_extension = '';

	/**
	 * Short description for 'setMimeType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $mime Parameter description (if any) ...
	 * @return     void
	 */
	public function setMimeType($mime)
	{
		$this->_mime = trim($mime);
	}

	/**
	 * Short description for 'getMimeType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function getMimeType()
	{
		return $this->_mime;
	}

	/**
	 * Short description for 'setExtension'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $ext Parameter description (if any) ...
	 * @return     void
	 */
	public function setExtension($ext)
	{
		$this->_extension = trim($ext);
	}

	/**
	 * Short description for 'getExtension'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function getExtension()
	{
		return $this->_extension;
	}

	/**
	 * Short description for 'format'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function format()
	{
		return '';
	}
}

