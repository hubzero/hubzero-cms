<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2009-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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


class WishAttachment extends JTable 
{
	var $id = NULL;  // @var int(11) Primary key
	var $wish = NULL;  // @var int(11)
	var $filename = NULL;  // @var varchar(255)
	var $description = NULL;  // @var varchar(255)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__wish_attachments', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if ($this->wish == NULL) {
			$this->setError( JText::_('Error: wish not found.') );
			return false;
		}
		if (trim( $this->filename ) == '') {
			$this->setError( JText::_('Error: attachment not found.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function getID() 
	{
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE filename='".$this->filename."' AND description='".$this->description."' AND wish=".$this->wish );
		$id = $this->_db->loadResult();
		$this->id = $id;
	}
	
	//-----------

	public function parse($text)
	{
		$f = '/\{attachment#[0-9]*\}/sU';
		return preg_replace_callback($f, array(&$this,'getAttachment'), $text);
	}
	
	//-----------
	
	public function getAttachment($matches)
	{
		$match = $matches[0];
		$tokens = split('#',$match);
		$id = intval(end($tokens));
		
		$this->_db->setQuery( "SELECT filename, description FROM $this->_tbl WHERE id=".$id );
		$a = $this->_db->loadRow();
		
		if ($this->output == 'web') {
			if (is_file($this->uppath.DS.$a[0])) {
				if (eregi( "bmp|gif|jpg|jpe|jpeg|tif|tiff|png", $a[0] )) {
					$size = getimagesize($this->uppath.DS.$a[0]);
					if ($size[0] > 300) {
						$img = '<a href="'.$this->webpath.'/'.$a[0].'" rel="lightbox" title="'.$a[1].'"><img src="'.$this->webpath.'/'.$a[0].'" alt="'.$a[1].'" width="300" /></a>';
					} else {
						$img = '<img src="'.$this->webpath.'/'.$a[0].'" alt="'.$a[1].'" />';
					}
					return $img;
				} else {
					$html  = '<a href="'.$this->webpath.'/'.$a[0].'" title="'.$a[1].'">';
					$html .= ($a[1]) ? $a[1] : $a[0];
					$html .= '</a>';
					return $html;
				}
			} else {
				return '[attachment #'.$id.' not found]';
			}
		} else {
			return $this->webpath.'/'.$a[0];
		}
	}
	
	//-----------
	
	public function deleteAttachment( $filename, $wish ) 
	{
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND wish=".$wish );
		if (!$this->_db->query()) {
			return $this->_db->getErrorMsg();
		}
		return true;
	}
	
	//-----------
	
	public function loadAttachment($filename=NULL, $wish=NULL)
	{
		if ($filename === NULL) {
			return false;
		}
		if ($wish === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE filename='$filename' AND wish='$wish'" );
		return $this->_db->loadObject( $this );
	}
}

