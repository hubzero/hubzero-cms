<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

$html = '';
$firstattach = $this->firstattach;
if(!$firstattach)
{
	echo '<p class="error">'.JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND').'</p>'."\n";
	return;
}

// Get some attributes
$attribs = new JParameter( $firstattach->attribs );
$width  = $attribs->get( 'width', '' );
$height = $attribs->get( 'height', '' );
$attributes = $attribs->get('attributes', '');
$width = (intval($width) > 0) ? $width : 0;
$height = (intval($height) > 0) ? $height : '400px';

if ($attributes) {
	$a = explode(',', $attributes);
	$bits = array();
	if ($a && is_array($a)) {
		foreach ($a as $b) 
		{
			if (strstr($b, ':')) {
				$b = explode(':', $b);
				$bits[] = trim($b[0]) . '="' . trim($b[1]) . '"';
			}
		}
	}
	$attributes = implode(' ', $bits);
}

$images = array('png', 'jpeg', 'jpe', 'jpg', 'gif', 'bmp');
$files = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages', 'ai', 'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'zip', 'rar', 'svg');

if (is_file(JPATH_ROOT.$firstattach->url)) 
{
	if($firstattach->type == 'video' || $firstattach->ext == 'swf') 
	{
		// Serve video
		$view = new JView( array('name'=>'view','layout'=>'video') );
		$view->option = $this->option;
		$view->config = $this->config;
		$view->database = $this->database;
		$view->publication = $this->publication;
		$view->helper = $this->helper;
		$view->attachments = $this->attachments;
		$view->firstattach = $firstattach;
		$view->path = $this->path;
		$view->version = $this->version;
		$view->height = $height;
		$view->width = $width;
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	else if (in_array(strtolower($firstattach->ext), $images)) {
		$html .= '<img ' . $attributes . ' src="' . $firstattach->url . '" alt="Image" />'."\n";
	}
	else if (in_array(strtolower($firstattach->ext), $files)) {
		$token = '';
		$juser = JFactory::getUser();
		if (!$juser->get('guest'))
		{
			$session = JFactory::getSession();

			$session_id = $session->getId();
			
			jimport('joomla.utilities.simplecrypt');
			$crypter = new JSimpleCrypt();
			$token = base64_encode($crypter->encrypt($session_id));
		}
		$juri = JURI::getInstance();
		$sef = JRoute::_('index.php?option=com_publications&id='.$this->publication->id.'&task=serve&aid='.basename($firstattach->id).'&render=download&token='.$token);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$html .= '<iframe src="https://docs.google.com/viewer?url='.urlencode($juri->base().$sef).'&amp;embedded=true#:0.page.0" width="100%" height="500" name="file_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
	} else {
		$html .= '<applet ' . $attributes . ' archive="'. $firstattach->url .'" width="';
		$html .= ($width > 0) ? $width : '';
		$html .= '" height="';
		$html .= ($height > 0) ? $height : '';
		$html .= '">'."\n";
		if ($width > 0) {
			$html .= ' <param name="width" value="'. $width .'" />'."\n";
		}
		if ($height > 0) {
			$html .= ' <param name="height" value="'. $height .'" />'."\n";
		}
		$html .= '</applet>'."\n";
	}
} else {
	$html .= '<p class="error">'.JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND').'</p>'."\n";
}

echo $html;
?>
