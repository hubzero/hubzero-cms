<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$html = '';
$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}

if ($this->resource->type == 4) {
	$parameters = new $paramsClass( $this->resource->params );

	$this->helper->getChildren();

	$children = $this->helper->children;

	// We're going through a learning module
	$html .= '<div class="aside">'."\n";
	$n = count($children);
	$i = 0;
	$blorp = 0;

	$html .= '<ul class="sub-nav">'."\n";
	foreach ($children as $child)
	{
		$attribs = new $paramsClass( $child->attribs );

		if ($attribs->get( 'exclude', '' ) != 1) {
			$params = new $paramsClass( $child->params );
			$link_action = $params->get( 'link_action', '' );
			switch ($child->logicaltype)
			{
				case 19: $class = ' class="withoutaudio'; break;
				case 20: $class = ' class="withaudio';    break;
				default:
					if ($child->type == 33) {
						$class = ' class="pdf';
					} else {
						$class = ' class="';
					}
					break;
			}
			$class .= ($this->resid == $child->id || ($this->resid == '' && $i == 0)) ? ' active"': '"';

			$i++;
			if ((!$child->grouping && $blorp) || ($child->grouping && $blorp && $child->grouping != $blorp)) {
				$blorp = '';
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
			if ($child->grouping && !$blorp) {
				$blorp = $child->grouping;

				$type = new ResourcesType( $this->database );
				$type->load( $child->grouping );

				$html .= ' <li class="grouping"><span>'.$type->type.'</span>'."\n";
				$html .= "\t".'<ul id="'.strtolower($type->type).$i.'">'."\n";
			}
			$html .= ($blorp) ? "\t" : '';
			$html .= ' <li'.$class.'>';

			$url  = ($link_action == 1)
				  ? checkPath($child->path, $child->type, $child->logicaltype)
				  : JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&resid='. $child->id);
			$html .= '<a href="'.$url.'" ';
			if ($link_action == 1) {
				$html .= 'target="_blank" ';
			} elseif($link_action == 2) {
				$html .= 'onclick="popupWindow(\''.$child->path.'\', \''.$child->title.'\', 400, 400, \'auto\');" ';
			}
			$html .= '>'. $child->title .'</a>';
			$html .= ($child->type == 33)
				   ? ' '.ResourcesHtml::getFileAttribs( $child->path, '', $this->fsize )
				   : '';
			$html .= '</li>'."\n";
			if ($i == $n && $blorp) {
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
		}
	}
	$html .= '</ul>'."\n";
	$html .= ResourcesHtml::license( $parameters->get( 'license', '' ) );
	$html .= '</div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";

	// Playing a learning module
	if (is_object($this->activechild)) {
		if (!$this->activechild->path) {
			// Output just text
			$html .= '<h3>'.stripslashes($this->activechild->title).'</h3>';
			$html .= stripslashes($this->activechild->fulltxt);
		} else {
			// Output content in iFrame
			$html .= '<iframe src="'.$this->activechild->path.'" width="97%" height="500" name="lm_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
		}
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= '<div class="clear"></div>'."\n";
} else {
	$url = $this->activechild->path;

	// Get some attributes
	$attribs = new $paramsClass( $this->activechild->attribs );
	$width  = $attribs->get( 'width', '' );
	$height = $attribs->get( 'height', '' );
	$attributes = $attribs->get('attributes', '');
	if ($attributes) {
		$a = explode(',', $attributes);
		$bits = array();
		if ($a && is_array($a)) {
			foreach ($a as $b)
			{
				if (strstr($b, ':')) {
					$b = preg_split('#:#', $b);
					$bits[] = trim($b[0]) . '="' . trim($b[1]) . '"';
				}
			}
		}
		$attributes = implode(' ', $bits);
	}

	$type = '';
	$arr  = explode('.',$url);
	$type = end($arr);
	$type = (strlen($type) > 4) ? 'html' : $type;
	$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

	$width = (intval($width) > 0) ? $width : 0;
	$height = (intval($height) > 0) ? $height : 0;

	$images = array('png', 'jpeg', 'jpe', 'jpg', 'gif', 'bmp');
	$files = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages', 'ai', 'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'zip', 'rar', 'svg');

	if (is_file(JPATH_ROOT.$url)) {
		if (strtolower($type) == 'swf') {
			$height = '400px';
			if ($this->no_html) {
				$height = '100%';
			}
			$html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$height.'" id="SlideContent" VIEWASTEXT>'."\n";
			$html .= ' <param name="movie" value="'. $url .'" />'."\n";
			$html .= ' <param name="quality" value="high" />'."\n";
			$html .= ' <param name="menu" value="false" />'."\n";
			$html .= ' <param name="loop" value="false" />'."\n";
			$html .= ' <param name="scale" value="showall" />'."\n";
			$html .= ' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="'.$height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'."\n";
			$html .= '</object>'."\n";
		} else if (in_array(strtolower($type), $images)) {
			$html .= '<img ' . $attributes . ' src="' . $url . '" alt="Image" />'."\n";
		} else if (in_array(strtolower($type), $files)) {
			$token = '';
			$juser =& JFactory::getUser();
			if (!$juser->get('guest'))
			{
				$session =& JFactory::getSession();

				$session_id = $session->getId();

				jimport('joomla.utilities.simplecrypt');
				$crypter = new JSimpleCrypt();
				$token = base64_encode($crypter->encrypt($session_id));
			}
			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option=com_resources&id='.$this->activechild->id.'&task=download&file='.basename($this->activechild->path).'&token='.$token);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$html .= '<iframe src="https://docs.google.com/viewer?url='.urlencode($juri->base().$sef).'&amp;embedded=true#:0.page.0" width="100%" height="500" name="file_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
		} else {
			$html .= '<applet ' . $attributes . ' archive="'. $url .'" width="';
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
		$html .= '<p class="error">'.JText::_('COM_RESOURCES_FILE_NOT_FOUND').'</p>'."\n";
	}
}
echo $html;
