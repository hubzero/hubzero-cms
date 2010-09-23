<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
