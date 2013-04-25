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

$output  = '<div id="link-image"';
$output .= $this->img ? '' : ' class="noimage"';
$output .= '>';
$output .= $this->img ? $this->img : '';
$output .= '</div>';
$output .= '<div id="link-info">';
$output .= '<h4 id="link-title"><a href="' . $this->url . '" rel="external">' . $this->title  . '</a></h4>';
$output .= '<p id="link-url">' . ProjectsHtml::shortenUrl($this->url, 25)  . '</p>';
$output .= '<p id="link-desc">' . $this->description  . '</p>';
$output .= '<div class="clear"></div>';
$output .= '</div>';

echo $output;
?>

