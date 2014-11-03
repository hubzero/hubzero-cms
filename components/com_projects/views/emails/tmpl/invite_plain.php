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

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'projects/' . $this->project->alias;
}
else
{
	$sef = JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias);
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

if ($this->uid == $this->project->created_by_user)
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

$message .= JText::_('COM_PROJECTS_PROJECT') . ': ' . $this->project->title
		 . ' (' . $this->project->alias.')' . "\n";
$message .= ucfirst(JText::_('COM_PROJECTS_CREATED')) . ' '
		 . JHTML::_('date', $this->project->created, 'M d, Y') . ' ' . JText::_('COM_PROJECTS_BY').' ';
$message .= $this->project->owned_by_group
			? $this->nativegroup->cn . ' ' . JText::_('COM_PROJECTS_GROUP')
			: $this->project->fullname;
$message .= "\n";
$message .= JText::_('COM_PROJECTS_EMAIL_URL').': ' . $link . "\n\n";

$sef 	.= $this->uid ? '' : '/?confirm=' . $this->code . '&email=' . $this->email;
$link = rtrim($base, DS) . DS . trim($sef, DS);

if ($this->uid)
{
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCESS_PROJECT')."\n";
}
else
{
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT') . ' ' . $this->hubShortName.' ';
	$message .= JText::_('COM_PROJECTS_EMAIL_ACCEPT') . "\n";
}
$message .= $link ."\n\n";

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;

?>
