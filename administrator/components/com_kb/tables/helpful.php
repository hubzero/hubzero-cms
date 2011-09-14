<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

class KbHelpful extends JTable
{
	var $id      = NULL;  // @var int(11) Primary key
	var $fid     = NULL;  // @var int(11)
	var $ip      = NULL;  // @var varchar(15)
	var $helpful = NULL;  // @var varchar(10)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__faq_helpful_log', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->fid ) == '') {
			$this->_error = JText::_('KB_ERROR_MISSING_ARTICLE_ID');
			return false;
		}
		return true;
	}

	public function getHelpful( $fid=NULL, $ip=NULL )
	{
		if ($fid == NULL) {
			$fid = $this->fid;
		}
		if ($ip == NULL) {
			$ip = $this->ip;
		}
		$this->_db->setQuery( "SELECT helpful FROM $this->_tbl WHERE fid =".$fid." AND ip='".$ip."'" );
		return $this->_db->loadResult();
	}

	public function deleteHelpful( $fid=NULL )
	{
		if ($fid == NULL) {
			$fid = $this->fid;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE fid=".$fid );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}

