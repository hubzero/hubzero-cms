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
// Support Attachments class
//----------------------------------------------------------

class SupportAttachment extends JTable 
{
	var $id          = NULL;  // @var int(11) Primary key
	var $ticket      = NULL;  // @var int(11)
	var $filename    = NULL;  // @var varchar(255)
	var $description = NULL;  // @var varchar(255)

	//-----------

	public function __construct(&$db) 
	{
		parent::__construct('#__support_attachments', 'id', $db);
	}
	
	//-----------
	
	public function check() 
	{
		if ($this->ticket == NULL) {
			$this->setError(JText::_('SUPPORT_ERROR_NO_TICKET_ID'));
			return false;
		}
		if (trim($this->filename) == '') {
			$this->setError(JText::_('SUPPORT_ERROR_NO_FILENAME'));
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function getID() 
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename='".$this->filename."' AND description='".$this->description."' AND ticket=".$this->ticket);
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
			if (is_file($this->uppath . DS . $a[0])) {
				$juri =& JURI::getInstance();
				$sef = JRoute::_('index.php?option=com_support&task=download&id='. $id . '&file=' . $a[0]);
				if (substr($sef,0,1) == '/') {
					$sef = substr($sef,1,strlen($sef));
				}
				$url = $juri->base() . $sef;
				
				if (eregi("bmp|gif|jpg|jpe|jpeg|tif|tiff|png", $a[0])) {
					$size = getimagesize($this->uppath . DS . $a[0]);
					if ($size[0] > 400) {
						$img = '<a href="' . $url . '" title="Click for larger version"><img src="' . $url . '" alt="' . $a[1] . '" width="400" /></a>';
					} else {
						$img = '<img src="' . $url . '" alt="' . $a[1] . '" />';
					}
					return $img;
				} else {
					$html  = '<a href="' . $url . '" title="' . $a[1] . '">';
					$html .= ($a[1]) ? $a[1] : $a[0];
					$html .= '</a>';
					return $html;
				}
			} else {
				return '[attachment #'.$id.' not found]';
			}
		} else {
			return $this->webpath . '/' . $a[0];
		}
	}
	
	//-----------
	
	public function deleteAttachment( $filename, $ticket ) 
	{
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND ticket=".$ticket );
		if (!$this->_db->query()) {
			return $this->_db->getErrorMsg();
		}
		return true;
	}
	
	//-----------
	
	public function loadAttachment($filename=NULL, $ticket=NULL)
	{
		if ($filename === NULL) {
			return false;
		}
		if ($ticket === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE filename='$filename' AND ticket='$ticket'" );
		return $this->_db->loadObject( $this );
	}
}
