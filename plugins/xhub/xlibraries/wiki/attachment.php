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

//----------------------------------------------------------

class WikiPageAttachment extends JTable 
{
	var $id          = NULL;  // @var int(11) Primary key
	var $pageid      = NULL;  // @var int(11)
	var $filename    = NULL;  // @var varchar(255)
	var $description = NULL;  // @var text
	var $created     = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $created_by  = NULL;  // @var int(11)
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_attachments', 'id', $db );
	}
	
	//-----------
	
	function getID($name, $listdir)
	{
		$this->_db->setQuery( "SELECT id, description FROM $this->_tbl WHERE filename='".$name."' AND pageid=".$listdir );
		return $this->_db->loadRow();
	}
	
	//-----------
	
	function deleteFile($filename, $pageid)
	{
		if (!$filename) {
			return false;
		}
		if (!$pageid) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND pageid=".$pageid );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
	}
	
	//-----------
	
	function parse($text)
	{
		//$f = '/\{file#[0-9]*\}/sU';
		$f = '/\[\[file#[0-9]*\]\]/sU';
		return preg_replace_callback($f, array(&$this,'getAttachment'), $text);
	}
	
	//-----------
	
	function getAttachment($matches)
	{
		$match  = $matches[0];
		$tokens = split('#',$match);
		$id = intval(end($tokens));
		
		$this->_db->setQuery( "SELECT filename, description FROM $this->_tbl WHERE id=".$id );
		$a = $this->_db->loadRow();
		
		if (is_file(JPATH_ROOT.$this->path.DS.$this->pageid.DS.$a[0])) {
			if (eregi( "bmp|gif|jpg|jpe|jpeg|tif|tiff|png", $a[0] )) {
				return '<img src="'.$this->path.DS.$this->pageid.DS.$a[0].'" alt="'.$a[1].'" />';
			} else {
				$html  = '<a href="'.$this->path.DS.$this->pageid.DS.$a[0].'" title="'.$a[1].'">';
				$html .= ($a[1]) ? $a[1] : $a[0];
				$html .= '</a>';
				return $html;
			}
		} else {
			return '[file #'.$id.' not found]';
		}
	}
	
	//-----------
	
	function setPageID( $oldid=null, $newid=null ) 
	{
		if (!$oldid) {
			return false;
		}
		if (!$newid) {
			return false;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET pageid='$newid' WHERE pageid='$oldid'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
	}
}
?>
