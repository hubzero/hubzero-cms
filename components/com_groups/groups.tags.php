<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

require_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.class.php');

//----------------------------------------------------------
// Answers Tagging class
//----------------------------------------------------------

class GroupsTags extends Tags
{
	public function __construct( $db, $config=array() )
	{
		$this->_db  = $db;
		$this->_tbl = 'groups';
		
		if (isset($config['normalized_valid_chars'])) {
			$this->_normalized_valid_chars = $config['normalized_valid_chars'];
		}
		if (isset($config['normalize_tags'])) {
			$this->_normalize_tags = $config['normalize_tags'];
		}
		if (isset($config['max_tag_length'])) {
			$this->_max_tag_length = $config['max_tag_length'];
		}
		if (isset($config['block_multiuser_tag_on_object'])) {
			$this->_block_multiuser_tag_on_object = $config['block_multiuser_tag_on_object'];
		}
	}
}
?>