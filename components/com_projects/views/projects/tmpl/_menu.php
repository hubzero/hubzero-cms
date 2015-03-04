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

$assetTabs = array();

// Sort tabs so that asset tabs are together
foreach ($this->tabs as $tab)
{
	if ($tab['submenu'] == 'Assets')
	{
		$assetTabs[] = $tab;
	}
}
$a = 0;

?>
	<ul class="projecttools">
		<li<?php if ($this->active == 'feed') { echo ' class="active"'; }?>>
			<a class="newsupdate" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=feed'); ?>" title="<?php echo JText::_('COM_PROJECTS_VIEW_UPDATES'); ?>"><span><?php echo JText::_('COM_PROJECTS_TAB_FEED'); ?></span>
			<span id="c-new" class="mini highlight <?php if ($this->project->counts['newactivity'] == 0) { echo 'hidden'; } ?>"><span id="c-new-num"><?php echo $this->project->counts['newactivity']; ?></span></span></a>
		</li>
		<li<?php if ($this->active == 'info') { echo ' class="active"'; }?>><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=info'); ?>" class="inform" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower(JText::_('COM_PROJECTS_TAB_INFO')); ?>">
			<span><?php echo JText::_('COM_PROJECTS_TAB_INFO'); ?></span></a>
		</li>
<?php foreach ($this->tabs as $tab) {

		if ($tab['name'] == 'blog')
		{
			continue;
		}

		if (isset($tab['submenu']) && $tab['submenu'] == 'Assets' && count($assetTabs) > 1)
		{
		$a++; // counter for asset tabs

		// Header tab
		if ($a == 1)
		{
	?>
		<li class="assets">
			<span><?php echo JText::_('COM_PROJECTS_TAB_ASSETS'); ?></span>
		</li>
	</ul>
	<ul class="projecttools assetlist">
<?php
foreach ($assetTabs as $aTab)
{ ?>
		<li<?php if ($aTab['name'] == $this->active) { echo ' class="active"'; } ?>>
			<a class="<?php echo $aTab['name']; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=' . $aTab['name']); ?>/" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower($aTab['title']); ?>">
				<span><?php echo $aTab['title']; ?></span>
			<?php if (isset($this->project->counts[$aTab['name']]) && $this->project->counts[$aTab['name']] != 0) { ?>
				<span class="mini" id="c-<?php echo $aTab['name']; ?>"><span id="c-<?php echo $aTab['name']; ?>-num"><?php echo $this->project->counts[$aTab['name']]; ?></span></span>
			<?php } ?>
			</a>
		</li>
<?php } ?>
	</ul>
	<ul class="projecttools">
<?php } continue; } ?>
		<li<?php if ($tab['name'] == $this->active) { echo ' class="active"'; } ?>>
			<a class="<?php echo $tab['name']; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=' . $tab['name']); ?>/" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower($tab['title']); ?>">
				<span><?php echo $tab['title']; ?></span>
			<?php if (isset($this->project->counts[$tab['name']]) && $this->project->counts[$tab['name']] != 0) { ?>
				<span class="mini" id="c-<?php echo $tab['name']; ?>"><span id="c-<?php echo $tab['name']; ?>-num"><?php echo $this->project->counts[$tab['name']]; ?></span></span>
			<?php } ?>
			</a>
		</li>
<?php } ?>
</ul>
