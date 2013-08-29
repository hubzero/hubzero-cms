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

$sef_browse = JRoute::_('index.php?option=' . $this->option . a . 'task=browse');
if (substr($sef_browse,0,1) == '/') 
{
	$sef_browse = substr($sef_browse,1,strlen($sef_browse));
}

$message  = JText::_('COM_PROJECTS_EMAIL_ADMIN_NOTIFICATION') ."\n";
$message .= '-------------------------------' ."\n";
$message .= JText::_('COM_PROJECTS_PROJECT') . ': ' . $this->project->title . ' (' . $this->project->alias . ')' . "\n";
$message .= ucfirst(JText::_('COM_PROJECTS_CREATED')) . ' ' 
		 . JHTML::_('date', $this->project->created, $dateFormat, $tz) . ' ' 
		 . JText::_('COM_PROJECTS_BY') . ' ';
$message .= $this->project->owned_by_group 
			? $this->nativegroup->cn . ' ' . JText::_('COM_PROJECTS_GROUP') 
			: $this->project->fullname;
$message .= "\n";

if($this->project->private == 0)
{
	$message .= JText::_('COM_PROJECTS_EMAIL_URL') . ': ' . $juri->base() . $sef . "\n";
}
$message .= '-------------------------------' ."\n\n";
			
if($this->config->get('restricted_data', 0) && $this->reviewer == 'sensitive') 
{
	$message .= JText::_('COM_PROJECTS_EMAIL_HIPAA') . ': ' . $this->params->get('hipaa_data') ."\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_FERPA') . ': ' . $this->params->get('ferpa_data') ."\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_EXPORT') . ': ' . $this->params->get('export_data') ."\n";
	if($this->params->get('followup'))
	{
		$message .= JText::_('COM_PROJECTS_EMAIL_FOLLOWUP_NEEDED') . ': ' . $this->params->get('followup') ."\n";	
	}
	$message .= '-------------------------------' ."\n\n";
}
if($this->config->get('grantinfo', 0) && $this->reviewer == 'sponsored') 
{
	$message .= JText::_('COM_PROJECTS_EMAIL_GRANT_TITLE') . ': ' . $this->params->get('grant_title') ."\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_GRANT_PI') . ': ' . $this->params->get('grant_PI') ."\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_GRANT_AGENCY') . ': ' . $this->params->get('grant_agency') ."\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_GRANT_BUDGET') . ': ' . $this->params->get('grant_budget') ."\n";
	$message .= '-------------------------------' ."\n\n";
}

// Append a message
if ($this->message)
{
	$message .= $this->message ."\n";
}

if($this->config->get('ginfo_group', 0) && $this->reviewer == 'sponsored') 
{
	$message .= '-------------------------------' ."\n\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_LINK_SPS') ."\n";
	$message .= $juri->base() . $sef_browse . '?reviewer=sponsored' . "\n\n";
}

if($this->config->get('sdata_group', 0) && $this->reviewer == 'sensitive') 
{
	$message .= '-------------------------------' ."\n\n";
	$message .= JText::_('COM_PROJECTS_EMAIL_LINK_HIPAA') ."\n";
	$message .= $juri->base() . $sef_browse . '?reviewer=sensitive' . "\n";
}

echo $message;
?>
