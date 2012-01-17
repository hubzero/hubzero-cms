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
 * Short description for 'WikiPageAttachment'
 * 
 * Long description (if any) ...
 */
class WikiPageAttachment extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'pageid'
	 * 
	 * @var string
	 */
	var $pageid      = NULL;  // @var int(11)

	/**
	 * Description for 'filename'
	 * 
	 * @var unknown
	 */
	var $filename    = NULL;  // @var varchar(255)

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description = NULL;  // @var text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created     = NULL;  // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by  = NULL;  // @var int(11)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__wiki_attachments', 'id', $db );
	}

	/**
	 * Short description for 'getID'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $listdir Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getID($name, $listdir)
	{
		$this->_db->setQuery( "SELECT id, description FROM $this->_tbl WHERE filename='".$name."' AND pageid=".$listdir );
		return $this->_db->loadRow();
	}

	/**
	 * Short description for 'deleteFile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $pageid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteFile($filename, $pageid)
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

	/**
	 * Short description for 'parse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function parse($text)
	{
		//$f = '/\{file#[0-9]*\}/sU';
		$f = '/\[\[file#[0-9]*\]\]/sU';
		return preg_replace_callback($f, array(&$this,'getAttachment'), $text);
	}

	/**
	 * Short description for 'getAttachment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getAttachment($matches)
	{
		$match  = $matches[0];
		$tokens = preg_split('/#/',$match);
		$id = intval(end($tokens));

		$this->_db->setQuery( "SELECT filename, description FROM $this->_tbl WHERE id=".$id );
		$a = $this->_db->loadRow();

		if (is_file(JPATH_ROOT.$this->path.DS.$this->pageid.DS.$a[0])) {
			if (preg_match( "#bmp|gif|jpg|jpe|jpeg|tif|tiff|png#i", $a[0] )) {
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

	/**
	 * Short description for 'setPageID'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oldid Parameter description (if any) ...
	 * @param      unknown $newid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function setPageID( $oldid=null, $newid=null )
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

