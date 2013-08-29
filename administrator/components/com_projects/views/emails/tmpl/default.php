<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = false;
}

if (substr($this->url,0,1) == '/') {
	$this->url = substr($this->url,1,strlen($this->url));
}

$message  = $this->subject."\n";
$message .= '-------------------------------'."\n";
$message .= JText::_('COM_PROJECTS_PROJECT').': '.$this->project->title.' ('.$this->project->alias.', id #'.$this->project->id.')'."\n";
$message .= ucfirst(JText::_('COM_PROJECTS_CREATED')).' '.JHTML::_('date', $this->project->created, $dateFormat, $tz).' '.JText::_('COM_PROJECTS_BY').' ';
$message .= $this->project->owned_by_group ? $this->nativegroup->cn.' '.JText::_('COM_PROJECTS_GROUP') : $this->project->fullname;
$message .= "\n";
$message .= JText::_('COM_PROJECTS_EMAIL_URL').': '.$this->url."\n";
$message .= '-------------------------------'."\n";

echo $message;
?>
