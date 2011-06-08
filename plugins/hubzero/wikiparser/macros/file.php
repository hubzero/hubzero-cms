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


class FileMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Works similar to the Image macro but, instead, generates a link to a file. The first argument is the filename.';
		$txt['html'] = '<p>Works similar to the Image macro but, instead, generates a link to a file. The first argument is the filename.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;
		
		if (!$et) {
			return '';
		}
		
		$config = JComponentHelper::getParams( 'com_wiki' );
		if ($this->filepath != '') {
			$config->set('filepath', $this->filepath);
		}

		// Is it numeric?
		if (is_numeric($et)) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'attachment.php');
			
			// Yes, then get resource by ID
			$id = intval($et);
			$attach = new WikiPageAttachment( $this->_db );
			$attach->load( $id );
			
			// Did we get a result from the database?
			$fp  = JPATH_ROOT.$config->get('filepath');
			$fp .= ($attach->pageid) ? DS.$attach->pageid : ''; 
			$fp .= DS.$attach->filename;
			if ($attach->filename && is_file($fp)) {
				/*
				$xhub =& Hubzero_Factory::getHub();
				$link  = $xhub->getCfg('hubLongURL').$config->get('filepath');
				$link .= ($attach->pageid) ? DS.$attach->pageid : ''; 
				$link .= DS.$attach->filename;
				*/
				$link  = substr($this->option,4,strlen($this->option)).DS;
				$link .= ($this->scope) ? $this->scope.DS : '';
				$link .= $this->pagename.DS.'File:'.$attach->filename;
				$desc = ($attach->description) ? stripslashes($attach->description) : $attach->filename;

				$bits = explode('.',$attach->filename);
				$ext = end($bits);

				// Build and return the link
				if (in_array($ext, explode(',',$config->get('img_ext')))) {
					return '<img src="'.$link.'" alt="'.$desc.'" />';
				} else {
					// Link
					//return '['.JRoute::_($link).' '.$desc.']';
					//return '<a href="'.JRoute::_($link).'">'.$desc.'</a>';
					return '<a class="attachment" href="'.JRoute::_($link).'">'.$desc.'</a>';
				}
			} else {
				// Return error message
				return '(file:'.$et.' not found)';
			}
		} else {
			// Did we get a result from the database?
			$fp  = JPATH_ROOT.$config->get('filepath');
			$fp .= ($this->pageid) ? DS.$this->pageid : '';
			$fp .= DS.$et;
			if (is_file($fp)) {
				/*
				$xhub =& Hubzero_Factory::getHub();
				$link  = $xhub->getCfg('hubLongURL').$config->get('filepath');
				$link .= ($this->pageid) ? DS.$this->pageid : ''; 
				$link .= DS.$et;
				*/
				$link  = substr($this->option,4,strlen($this->option)).DS;
				$link .= ($this->scope) ? $this->scope.DS : '';
				$link .= $this->pagename.DS.'File:'.$et;
				$desc = $et;

				$bits = explode('.',$et);
				$ext = end($bits);

				// Build and return the link
				if (in_array($ext, explode(',',$config->get('img_ext')))) {
					return '<img src="'.$link.'" alt="'.$desc.'" />';
				} else {
					// Link
					//return '['.JRoute::_($link).' '.$desc.']';
					//return '<a href="'.JRoute::_($link).'">'.$desc.'</a>';
					return '<a class="attachment" href="'.JRoute::_($link).'">'.$desc.'</a>';
				}
			} else {
				// Return error message
				return '(file:'.$et.' not found)';
			}
		}
	}
}

