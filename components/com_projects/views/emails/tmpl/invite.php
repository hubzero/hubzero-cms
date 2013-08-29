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

$juri =& JURI::getInstance();

$sef = JRoute::_('index.php?option='.$this->option.a.$this->goto);
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}

if($this->user == $this->project->created_by_user)
{
	$message  = JText::_('COM_PROJECTS_EMAIL_CREATOR_NEW_PROJECT');
	$message .= "\n";
	$message .= '-------------------------------'."\n";
}
else {
	$message  = $this->project->fullname.' ';
	$message .= $this->uid ? JText::_('COM_PROJECTS_EMAIL_ADDED_YOU') : JText::_('COM_PROJECTS_EMAIL_INVITED_YOU');
	$message .= ' "'.$this->project->title.'" '.JText::_('COM_PROJECTS_EMAIL_IN_THE_ROLE').' ';
	$message .= $this->role == 1 ? JText::_('COM_PROJECTS_LABEL_OWNER') : JText::_('COM_PROJECTS_LABEL_COLLABORATOR');
	$message .= "\n";
	$message .= '-------------------------------'."\n";	
}

$message .= JText::_('COM_PROJECTS_PROJECT').': '.$this->project->title.' ('.$this->project->alias.')'."\n";
$message .= ucfirst(JText::_('COM_PROJECTS_CREATED')).' '.JHTML::_('date', $this->project->created, $dateFormat, $tz).' '.JText::_('COM_PROJECTS_BY').' ';
$message .= $this->project->owned_by_group ? $this->nativegroup->cn.' '.JText::_('COM_PROJECTS_GROUP') : $this->project->fullname;
$message .= "\n";
$message .= JText::_('COM_PROJECTS_EMAIL_URL').': '.$juri->base().$sef."\n\n";

$sef = $this->uid ? $sef : JRoute::_('index.php?option='.$this->option.a.$this->goto).'/?confirm='.$this->code.'&email='.$this->email;
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}
if($this->uid) {
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCESS_PROJECT')."\n";
	$message .= $juri->base().$sef."\n\n";
}
else {
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT').' '.$this->hubShortName.' ';
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCEPT')."\n";
	$message .= $juri->base().$sef."\n\n";
}

echo $message;
?>
