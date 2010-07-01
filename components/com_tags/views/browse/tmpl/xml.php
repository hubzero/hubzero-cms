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

$document =& JFactory::getDocument();
$document->setMimeEncoding( 'text/xml' );

// Output XML header.
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

// Output root element.
echo '<root>' . "\n";

// Output the data.
echo "\t" . '<tags>' . "\n";
if ($this->rows) {
	foreach ($this->rows as $datum) 
	{
		echo "\t\t" . '<tag>' . "\n";
		echo "\t\t\t" . '<raw>' . htmlspecialchars( stripslashes($datum->raw_tag) ) . '</raw>' . "\n";
		echo "\t\t\t" . '<normalized>' . htmlspecialchars( $datum->tag ) . '</normalized>' . "\n";
		echo "\t\t" . '</tag>' . "\n";
	}
}
echo "\t" . '</tags>' . "\n";

// Terminate root element.
echo '</root>' . "\n";