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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=registrations.csv");
header("Pragma: no-cache");
header("Expires: 0");

foreach ($this->rows as $row)
{

	$section = CoursesModelSection::getInstance($row->get('section_id'));

	echo encodeCSVField($row->get('user_id'));
	echo ',';
	echo encodeCSVField($row->get('name'));
	echo ',';
	echo encodeCSVField($row->get('email'));
	echo ',';
	echo encodeCSVField($section->exists()) ? $this->escape(stripslashes($section->get('title'))) : JText::_('(none)');
	echo ',';
	if ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00') {
		echo encodeCSVField(JHTML::_('date', $row->get('enrolled'), JText::_('DATE_FORMAT_HZ1')));
	}
	else {
		echo encodeCSVField(JText::_('(unknown)'));
	}
	echo "\n";
	
}

die;

function encodeCSVField($string) {
    if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        $string = '"' . str_replace('"', '""', $string) . '"';
    }
    return $string;
}