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

$sef = JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias);
if (substr($sef,0,1) == '/') 
{
	$sef = substr($sef,1,strlen($sef));
}

$message  = JText::_('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS') ."\n";
$message .= '-------------------------------' ."\n";
$message .= JText::_('COM_PROJECTS_PROJECT') . ': ' . $this->project->title . ' (' . $this->project->alias ;
if ($this->project->provisioned == 1)
{
	$message .= ' - ' . JText::_('COM_PROJECTS_PROVISIONED');
}

$message .= ')' . "\n";
$message .= ucfirst(JText::_('COM_PROJECTS_CREATED')) . ' ' 
		 . JHTML::_('date', $this->project->created, $dateFormat, $tz) . ' ' 
		 . JText::_('COM_PROJECTS_BY') . ' ';
$message .= $this->project->owned_by_group 
			? $this->nativegroup->cn . ' ' . JText::_('COM_PROJECTS_GROUP') 
			: $this->project->fullname;
$message .= "\n";

if ($this->project->private == 0)
{
	$message .= JText::_('COM_PROJECTS_EMAIL_URL') . ': ' . $juri->base() . $sef . "\n";
}
$message .= '-------------------------------' ."\n\n";

// Append a message
if ($this->message)
{
	$message .= $this->message ."\n";
	$message .= '-------------------------------' ."\n\n";
}

echo $message;
?>
