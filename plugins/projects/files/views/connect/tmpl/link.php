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

$google = $this->connect->getConfigs('google');
$dropbox = $this->connect->getConfigs('dropbox');

// Some connection active
$active = ($google['active'] || $dropbox['active']) ? 1 : 0;
$on = ($google['on'] || $dropbox['on']) ? 1 : 0;

// Project creator
$creator = ($this->project->owned_by_user == $this->uid) ? 1 : 0;

$limited = $this->params->get('connectedProjects') ? ProjectsHelper::getParamArray($this->params->get('connectedProjects')) : array();

$authorized = (empty($limited) || (!empty($limited) && in_array($this->project->alias, $limited))) ? true : false;

$connected = (($google && $this->oparams->get('google_token')) || ($dropbox && $this->oparams->get('dropbox_token'))) ? 1 : 0;
?>
<?php if ($on && (($google || $dropbox) && $active || (!$active && $creator && $authorized))) { ?>
<p id="connector">
	<?php if(!$active || !$connected) { ?>
	<span class="i-new-container">
		<span class="i-new"><?php echo JText::_('New'); ?></span>
	</span>
	<?php } ?>
	<span>
		<?php if (!$active || !$connected) {  ?>
		<?php if ($google) { ?>
		<span class="google"></span>
		<?php } ?>
		<?php if ($dropbox) { ?>
		<span class="dropbox"></span>
		<?php } ?>
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=files') . '?action=connect'; ?>"><?php echo JText::_('Connect!'); ?></a>
		<?php } ?>
		
		<?php 
			// Connected to Google
			if ($this->oparams->get('google_token') && $active) {  ?>
				<span class="connect-email"><span class="google"></span> <?php echo $this->oparams->get('google_email'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=files') . '?action=connect'; ?>">[&raquo;]</a></span>
		<?php } ?>
	</span>
</p>
<?php } else { ?>
	<p class="editing mini pale"><?php echo JText::_('COM_PROJECTS_FILES_MAX_UPLOAD').' '.$this->sizelimit; ?></p>
<?php } ?>