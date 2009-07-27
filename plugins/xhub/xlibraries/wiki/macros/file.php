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
		
		ximport('wiki.config');
		$configs = array();
		$configs['option'] = $this->option;
		if ($this->filepath != '') {
			$configs['filepath'] = $this->filepath;
		}
		$config = new WikiConfig( $configs );
		
		// Is it numeric?
		if (is_numeric($et)) {
			ximport('wiki.attachment');
			
			// Yes, then get resource by ID
			$id = intval($et);
			$attach = new WikiPageAttachment( $this->_db );
			$attach->load( $id );
			
			// Did we get a result from the database?
			if ($attach->filename && is_file(JPATH_ROOT.$config->filepath.DS.$attach->pageid.DS.$attach->filename)) {
				$xhub =& XFactory::getHub();
				$link = $xhub->getCfg('hubLongURL').$config->filepath.DS.$attach->pageid.DS.$attach->filename;
				$desc = ($attach->description) ? stripslashes($attach->description) : $attach->filename;

				$bits = explode('.',$attach->filename);
				$ext = end($bits);

				// Build and return the link
				if (in_array($ext, $config->image_ext)) {
					return '<img src="'.$link.'" alt="'.$desc.'" />';
				} else {
					// Link
					//return '['.JRoute::_($link).' '.$desc.']';
					return '<a href="'.JRoute::_($link).'">'.$desc.'</a>';
				}
			} else {
				// Return error message
				return '(file:'.$et.' not found)';
			}
		} else {
			// Did we get a result from the database?
			if (is_file(JPATH_ROOT.$config->filepath.DS.$this->pageid.DS.$et)) {
				$xhub =& XFactory::getHub();
				$link = $xhub->getCfg('hubLongURL').$config->filepath.DS.$this->pageid.DS.$et;
				$desc = $et;

				$bits = explode('.',$et);
				$ext = end($bits);

				// Build and return the link
				if (in_array($ext, $config->image_ext)) {
					return '<img src="'.$link.'" alt="'.$desc.'" />';
				} else {
					// Link
					//return '['.JRoute::_($link).' '.$desc.']';
					return '<a href="'.JRoute::_($link).'">'.$desc.'</a>';
				}
			} else {
				// Return error message
				return '(file:'.$et.' not found)';
			}
		}
	}
}
?>