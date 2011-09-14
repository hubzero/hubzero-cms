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
		echo "\t\t\t" . '<total>' . htmlspecialchars( $datum->total ) . '</total>' . "\n";
		echo "\t\t" . '</tag>' . "\n";
	}
}
echo "\t" . '</tags>' . "\n";

// Terminate root element.
echo '</root>' . "\n";
