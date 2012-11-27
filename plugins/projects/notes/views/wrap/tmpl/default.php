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

$content = $this->content;
$content = str_replace('projects/projects/', 
						'projects/', 
						$content);
// Breadcrumbs
$bcrumb = '';
if ($this->parent_notes && count($this->parent_notes) > 0) {
	foreach($this->parent_notes as $parent) {
		$bcrumb .= ' &raquo; <span class="subheader"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.a.'active=notes'.a.'scope='.$parent->scope.a.'pagename='.$parent->pagename).'">'. $parent->title.'</a></span>';
	}
}
if($this->task == 'new') {
	$bcrumb .= ' &raquo; <span class="subheader">'.JText::_('COM_PROJECTS_NOTES_TASK_NEW').'</span>';
}
else if($this->page && ($this->task != 'view' || ($this->firstnote && $this->pagename != $this->firstnote))) {
	$bcrumb .= ' &raquo; <span class="subheader"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.a.'active=notes'.a.'scope='.$this->scope.a.'pagename='.$this->pagename).'">'. $this->page->title.'</a></span>';
	$tasks = array( 'edit', 'history', 'comments', 'delete', 'compare', 'addcomment', 'renamepage' );
	
	if($this->task != 'view' && in_array($this->task, $tasks)) {
		$bcrumb .= ' &raquo; <span class="subheader">'.JText::_('COM_PROJECTS_NOTES_TASK_'.strtoupper($this->task)).'</span> ';
	}
} 

// Sort notes to display hierarchy by scope
$notes = array();
$order = array();
$thirdlevel = array();

if ($this->notes) 
{
	foreach ($this->notes as $note) 
	{ 
		$parts = explode ( '/', $note->scope );	
		$remaining = array_slice($parts, 3);
		$level = count($remaining) + 1;
		$parent = $level > 1 ? array_shift($remaining) : '';

		if($level == 1) {
			$notes[$note->pagename] = array( $level => array($note));
		}
		else if($level == 2) {
			$notes[$parent][$level][] = $note;
		}
		else if($level == 3) {
			$r = array_shift($remaining);
			$thirdlevel[$r][] = $note;
		}
	}
}

// Get parent scope (to add subpages)
$parentScope = $this->scope . DS . $this->pagename;

?>
<div id="plg-header">
	<h3 class="notes"><?php if($this->task != 'view' || ($this->firstnote && $this->pagename != $this->firstnote)) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'); ?>"><?php } ?><?php echo $this->title; ?><?php if($this->task != 'view' || ($this->firstnote && $this->pagename != $this->firstnote)) { ?></a><?php } ?> <?php  echo $bcrumb; ?></h3>
</div>

<div id="notes-wrap" <?php if($this->task == 'view' or $this->task == 'page') { echo 'class="withside"'; } ?>>
	<?php if($this->task == 'view' or $this->task == 'page') { ?>
	<div class="aside">
		<div class="addanote"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'scope='.$parentScope.a.'action=new'); ?>" class="addnew"><?php echo JText::_('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?></a> &nbsp; <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=new'; ?>" class=" addnew"><?php echo JText::_('COM_PROJECTS_NOTES_ADD_NOTE'); ?></a></div>

		<div class="sidebox">
			<h4><?php echo ucfirst(JText::_('COM_PROJECTS_NOTES_MULTI')); ?></h4>
			<ul>
			<?php if($notes) { ?>
				<?php foreach($notes as $note) { 
					    foreach($note as $level => $parent) {
						 foreach($parent as $entry) { ?>
							<li <?php if($entry->pagename == $this->pagename) { echo 'class="active"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$entry->scope.a.'pagename='.$entry->pagename); ?>" class="note wikilevel_<?php echo $level; ?>"><?php echo Hubzero_View_Helper_Html::shortenText($entry->title, 35, 0); ?></a>
							</li>
							<?php 
								// Third level of notes
								if(isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0) { 
									foreach($thirdlevel[$entry->pagename] as $subpage) { ?>
									<li <?php if($subpage->pagename == $this->pagename) { echo 'class="active"'; } ?>>
										<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$subpage->scope.a.'pagename='.$subpage->pagename); ?>" class="wikipage wikilevel_3"><?php echo Hubzero_View_Helper_Html::shortenText($subpage->title, 35, 0); ?></a>
									</li>		
							<?php	}
							 } ?>	
				<?php	}
					}				
				?>
			<?php } ?>	
			<?php } else { ?>
			<li class="faded"><?php echo JText::_('COM_PROJECTS_NOTES_NO_NOTES'); ?></li>
			<?php } ?>
			</ul>
		</div>
		<?php /* if(count($notes) > 1) { ?>
		<p class="rightfloat reorder"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=reorder'; ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_NOTES_REORDER'); ?></a></p>
		<?php } */ ?>
		<?php 
		if ($this->templates) { ?>
		 <div class="sidebox">
			<h4><?php echo ucfirst(JText::_('COM_PROJECTS_NOTES_TEMPLATES')); ?></h4>
			<ul>	
		<?php
			foreach ($this->templates as $template)
			{
		?>
			<li <?php if($template->pagename == $this->pagename) { echo 'class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$template->scope.'&pagename='.$template->pagename); ?>" class="wikitemplate"><?php echo stripslashes($template->pagename); ?></a></li>
		<?php } ?>
			<li  class="addnew"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=new&pagename=Template:New'; ?>"><?php echo JText::_('COM_PROJECTS_NOTES_ABOUT_TEMPLATES_START'); ?></a></li>
			 </ul>
		 </div>
		<?php } ?>
	</div>
	<?php } ?>
	<?php if($this->task == 'view' or $this->task == 'page') { ?>
	<div class="subject">
	<?php } ?>
		<div id="notes-content">
		<?php echo ($this->notes or $this->task == 'new' or $this->preview) ? $content : '<p class="s-notes"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=new">'.JText::_('COM_PROJECTS_NOTES_START_A_NOTE').'</a></p>'; ?>
		</div>
	<?php 
	// Allow subpages up until third level
	if(($this->task == 'view' or $this->task == 'page') && count($this->parent_notes) < 2) { ?>
	</div>
	<div class="clear"></div>
	<?php } ?>
</div>

