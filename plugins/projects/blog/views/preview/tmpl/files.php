<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$this->css()
     ->js();

$body   	= '';
$selected 	= $this->selected;

// Display images if available
if (count($selected) > 0)
{
	// Randomize
	shuffle($selected);

	$class = count($selected) == 1 ? 'net-single' : 'net-multi';
	$class = count($selected) == 2 ? 'net-double' : $class;
	$class = count($selected) == 3 ? 'net-triple' : $class;

	$body  = '<div class="previewnet">';
	$i = 1;
	foreach ($selected as $item)
	{
		if ($item['image'])
		{
			$body .= '<span class="img-container"><img class="' . $class . '" src="' . $item['image'] . '" alt="" /></span>';
		}
		$i++;
	}
	$body .= '</div>';
}

echo $body;
