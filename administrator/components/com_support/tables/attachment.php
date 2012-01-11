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

//----------------------------------------------------------
// Support Attachments class
//----------------------------------------------------------

/**
 * Short description for 'SupportAttachment'
 * 
 * Long description (if any) ...
 */
class SupportAttachment extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'ticket'
	 * 
	 * @var string
	 */
	var $ticket      = NULL;  // @var int(11)

	/**
	 * Description for 'filename'
	 * 
	 * @var string
	 */
	var $filename    = NULL;  // @var varchar(255)

	/**
	 * Description for 'description'
	 * 
	 * @var string
	 */
	var $description = NULL;  // @var varchar(255)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_attachments', 'id', $db);
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getID'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function getID()
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename='".$this->filename."' AND description='".$this->description."' AND ticket=".$this->ticket);
		$id = $this->_db->loadResult();
		$this->id = $id;
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
		$f = '/\{attachment#[0-9]*\}/sU';
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
		$match = $matches[0];
		$tokens = explode('#',$match);
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

				if (preg_match("/bmp|gif|jpg|jpe|jpeg|tif|tiff|png/i", $a[0])) {
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

	/**
	 * Short description for 'deleteAttachment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $ticket Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function deleteAttachment( $filename, $ticket )
	{
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND ticket=".$ticket );
		if (!$this->_db->query()) {
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Short description for 'loadAttachment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $filename Parameter description (if any) ...
	 * @param      unknown $ticket Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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
