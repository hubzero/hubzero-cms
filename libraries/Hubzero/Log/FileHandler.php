<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class Hubzero_Log_FileHandler
{
	var $_filename = null;
	var $_fp = null;

	//-----------

	public function __construct($filename) 
	{
		$this->_filename = $filename;
		$this->_fp = null;
	}
	
	//-----------

	public function log($priority, $message, $trace = false)
	{
		if (empty($this->_fp)) {
			if (is_null($this->_fp))
				$this->_fp = fopen($this->_filename, "ab");
		
			if (empty($this->_fp)) {
				return;
			}
		}

		flock($this->_fp, LOCK_EX);
		fwrite($this->_fp, date('Y-m-d H:i:s ') );
		fwrite($this->_fp, $message);

		if ($trace) {
			fwrite($this->_fp, Hubzero_Log::getSimpleTrace() );
		}
		
		fwrite($this->_fp, "\n");
		fflush($this->_fp);
		flock($this->_fp, LOCK_UN);

		return;
	}

	//-----------

	public function close()
	{
		if (!empty($this->_fp)) {
			fclose($this->_fp);
			$this->_fp = null;
		}
	}
}

