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

$html = '';
	$url = $this->resource->path;
	
	// Get some attributes
	$attribs =& new JParameter( $this->resource->attribs );
	$width  = $attribs->get( 'width', '' );
	$height = $attribs->get( 'height', '' );
	
	$type = '';
	$arr  = explode('.',$url);
	$type = end($arr);
	$type = (strlen($type) > 4) ? 'html' : $type;
	$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

	$width = (intval($width) > 0) ? $width : 0;
	$height = (intval($height) > 0) ? $height : 0;
	
	$html .= '<p class="error"> There is no "play" action on this resource. </p>'."\n";
	$html .= '<a href="'.$url.'">Go Back To The Resource</a>';

echo $html;
?>