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
$html  = '';

// Add new activity count to page title
$document =& JFactory::getDocument();
$title = $this->project->counts['newactivity'] > 0 
	&& $this->active == 'feed' 
	? $this->title.' (' . $this->project->counts['newactivity'].')' 
	: $this->title;
$document->setTitle( $title );

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);

// Use id or alias in urls?
$goto  = 'alias=' . $this->project->alias;
?>
<div id="project-wrap">	
	<div id="project-innerwrap">
			<div class="main-menu">
				<?php echo ProjectsHtml::embedProjectImage($this); ?>	
			<ul class="projecttools">
				<li<?php if($this->active == 'feed') { echo ' class="active"'; }?>>
					<a class="newsupdate" href="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . a . 'active=feed'); ?>" title="<?php echo JText::_('COM_PROJECTS_VIEW_UPDATES'); ?>"><span><?php echo JText::_('COM_PROJECTS_TAB_FEED'); ?></span>
					<span id="c-new" class="mini highlight <?php if($this->project->counts['newactivity'] == 0) { echo 'hidden'; } ?>"><span id="c-new-num"><?php echo $this->project->counts['newactivity'];?></span></span></a>
				</li>				
				<li<?php if($this->active == 'info') { echo ' class="active"'; }?>><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . a . 'active=info'); ?>" class="inform" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower(JText::_('COM_PROJECTS_TAB_INFO')); ?>">
					<span><?php echo JText::_('COM_PROJECTS_TAB_INFO'); ?></span></a>
				</li>					
<?php if ($this->tabs) {
	foreach($this->tabs as $tab) { 
		if($tab['name'] == 'blog')
		{
			continue;
		}
?>
				<li<?php if($tab['name'] == $this->active) { echo ' class="active"'; } ?>>
					<a class="<?php echo $tab['name']; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . a . 'active=' . $tab['name']); ?>/" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower($tab['title']); ?>">
						<span><?php echo $tab['title']; ?></span> 
<?php if (isset($this->project->counts[$tab['name']]) 
&& $this->project->counts[$tab['name']] != 0) { ?>
						<span class="mini" id="c-<?php echo $tab['name']; ?>"><span id="c-<?php echo $tab['name']; ?>-num"><?php echo $this->project->counts[$tab['name']]; ?></span></span>
<?php } ?>
					</a>
				</li>
<?php }
 } ?>
		</ul>				
			</div><!-- / .main-menu -->
			<div class="main-content">
				<?php echo ProjectsHtml::writeProjectHeader($this, 0, 1, 2, 0); ?>	
				<?php echo ProjectsHtml::writeMemberOptions($this); ?>		
				<div class="status-msg" id="status-msg">
<?php 
// Display error or success message
if ($this->getError()) { 
	echo ('<p class="witherror">' . $this->getError().'</p>');
}
else if($this->msg) {
	echo ('<p>' . $this->msg . '</p>');
} ?>
				</div>		
			<div id="plg-content" class="content-<?php echo $this->active; ?>">
			<?php if($this->notification) { echo $this->notification; } ?>
<?php if($this->side_modules) { ?> 
				<div class="aside">
					<?php echo $this->side_modules; ?>
				</div>
				<div class="subject">
<?php } ?>
<?php if($this->content) { echo $this->content; } ?>
			<?php if($this->active == 'info') 
			{ 
					// Display project info
					$view = new JView(
						array(
							'name' => 'info'
						)
					);
					$view->info = $this;
					$view->goto = $goto;
					echo $view->loadTemplate();
			 } ?>
<?php if($this->side_modules) { ?> 
				</div> <!-- / .subject -->
<?php } ?>
			<div class="clear"></div>
			</div><!-- / plg-content -->
		</div><!-- / .main-content -->
	</div>
</div>