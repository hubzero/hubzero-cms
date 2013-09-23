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

$tool = JRequest::getVar( 'tool', '', 'request', 'object' );	
						
//$side = ($this->task == 'view' or $this->task == 'page' or $this->task == 'wiki') ? 1 : 0;
//$side = ($this->task == 'view' or $this->task == 'page') && !$tool && $this->page ? 1 : 0;
$side = ($this->task == 'view' or $this->task == 'page' or $this->task == 'wiki') && $this->page ? 1 : 0;
$toolOpt = $tool && $tool->id && ($this->task == 'view' or $this->task == 'page' or $this->task == 'wiki') ? 1 : 0;

// Breadcrumbs
$bcrumb = '';
if ($this->parent_notes && count($this->parent_notes) > 0) {
	foreach ($this->parent_notes as $parent) {			
		$bcrumb .= ' &raquo; <span class="subheader"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.a.'active=notes'.a.'scope='.$parent->scope.a.'pagename='.$parent->pagename).'">'. $parent->title.'</a></span>';		
	}
}
if ($this->task == 'new') {
	$bcrumb .= ' &raquo; <span class="subheader">'.JText::_('COM_PROJECTS_NOTES_TASK_NEW').'</span>';
}
else if($this->page && (($this->task != 'view' && !$this->tool) || ($this->firstnote && $this->pagename != $this->firstnote))) {
	$bcrumb .= ' &raquo; <span class="subheader"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.a.'active=notes'.a.'scope='.$this->scope.a.'pagename='.$this->pagename).'">'. $this->page->title.'</a></span>';
	$tasks = array( 'edit', 'history', 'comments', 'delete', 'compare', 'addcomment', 'renamepage' );
	
	if($this->task != 'view' && in_array($this->task, $tasks)) {
		$bcrumb .= ' &raquo; <span class="subheader">'.JText::_('COM_PROJECTS_NOTES_TASK_'.strtoupper($this->task)).'</span> ';
	}
} 

// Get public stamp for page
$pubstamp = NULL;
$listed   = NULL;
if ($this->page && is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
	.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php')
	&& $this->task == 'view' && $this->page && $this->pparams->get('enable_publinks'))
{
	require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
		.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
	
	$objSt = new ProjectPubStamp( $this->database );
	
	// Build reference for latest revision of page
	$reference = array(
		'pageid'   => $this->page->id,
		'pagename' => $this->page->pagename,
		'revision' => NULL
	);
	
	if ($objSt->checkStamp($this->project->id, json_encode($reference), 'notes'))
	{
		$pubstamp = $objSt->stamp;
		$listed   = $objSt->listed;
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
		$show = 1;
		
		// For tool wiki, only show tool pages
		if ($tool && $tool->id)
		{
			$show = 0;
			$startScope = trim(str_replace('projects' . DS . $this->project->alias . DS . 'notes', '', $note->scope), DS);

			// Does this page belong to an tool?
			if ((preg_match("/^tool:" . $tool->name . "/", $note->pagename) || preg_match("/^tool:" . $tool->name . "/", $startScope) ))
			{
				$show = 1;
			}
		}
		
		if (!$show)
		{
			// Skip
			continue;
		}
		
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
		else if($level >= 3) {
			$r = array_shift($remaining);
			$thirdlevel[$r][] = $note;
		}
	}
}

// Get parent scope (to add subpages)
$parentScope = $this->scope . DS . $this->pagename;

?>
<?php if (isset($this->tool) && $this->tool->name) { 
	
	echo ProjectsHtml::toolDevHeader( $this->option, $this->config, $this->project, $this->tool, 'wiki', $bcrumb);
	
 } else { ?>
<div id="plg-header">
	<h3 class="notes"><?php if($this->task != 'view' || ($this->firstnote && $this->pagename != $this->firstnote)) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'); ?>"><?php } ?><?php echo $this->title; ?><?php if($this->task != 'view' || ($this->firstnote && $this->pagename != $this->firstnote)) { ?></a><?php } ?> <?php  echo $bcrumb; ?></h3>
</div>
<?php } ?>
<div id="notes-wrap" <?php if ($side) { echo 'class="withside"'; } ?>>
	<?php if ($side) { ?>
	<div class="aside">
		<?php if ($toolOpt && count($this->parent_notes) < 2) { ?>	
			<div class="addanote"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'scope='.$parentScope.a.'action=new'); ?>" class="addnew"><?php echo JText::_('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?></a></div>
		<?php } ?>
		<?php if (!$toolOpt) { ?>	
		<div class="addanote"><?php if (count($this->parent_notes) < 2) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'scope='.$parentScope.a.'action=new'); ?>" class="addnew"><?php echo JText::_('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?></a> &nbsp; <?php } ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=new'; ?>" class=" addnew"><?php echo JText::_('COM_PROJECTS_NOTES_ADD_NOTE'); ?></a></div>
		<?php } ?>
		<div class="sidebox">
			<h4><?php echo $toolOpt ? ucfirst(JText::_('COM_PROJECTS_NOTES_TOOL_WIKI_PAGES')) : ucfirst(JText::_('COM_PROJECTS_NOTES_MULTI')); ?></h4>
			<ul <?php echo $toolOpt ? 'class="appindex"' : ''; ?>>
			<?php if ($notes) { ?>
				<?php foreach ($notes as $note) { 
					    foreach ($note as $level => $parent) {
						 foreach ($parent as $entry) { ?>
							<li <?php if($entry->pagename == $this->pagename) { echo 'class="active"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$entry->scope.a.'pagename='.$entry->pagename); ?>" class="note wikilevel_<?php echo $level; ?>"><?php echo Hubzero_View_Helper_Html::shortenText($entry->title, 35, 0); ?></a>
							</li>
							<?php 
								// Third level of notes
								if(isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0) { 
									foreach($thirdlevel[$entry->pagename] as $subpage) { ?>
									<li <?php if($subpage->pagename == $this->pagename) { echo 'class="active"'; } ?>>
										<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$subpage->scope.a.'pagename='.$subpage->pagename); ?>" class="note wikilevel_3"><?php echo Hubzero_View_Helper_Html::shortenText($subpage->title, 35, 0); ?></a>
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
		if ($this->templates && !$toolOpt) { ?>
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
	<?php if ($side) { ?>
	<div class="subject">
	<?php } ?>
		<div id="notes-content" class="<?php echo $listed ? 'listed-note' : 'unlisted-note'; ?>">
		<?php if ($listed) { ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes') . '?action=share&p=' . $this->page->id; ?>" class="showinbox" title="<?php echo JText::_('COM_PROJECTS_NOTES_PUBLIC_CONFIGURE'); ?>"><span class="n-pub">&nbsp;</span></a>
		<?php } ?>
		<?php echo ($this->notes or $this->task == 'new' or $this->preview) ? $content : '<p class="s-notes"><a href="'.JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes').'?action=new">'.JText::_('COM_PROJECTS_NOTES_START_A_NOTE').'</a></p>'; ?>
		</div>
		<?php if ($pubstamp) { 
			$juri =& JURI::getInstance();
		?>
			<p class="publink"><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LINK') . ' <a href="' . trim($juri->base(), DS) . JRoute::_('index.php?option=' . $this->option . a . 'action=get') . '?s=' . $pubstamp .'">' . trim($juri->base(), DS) . JRoute::_('index.php?option=' . $this->option . a . 'action=get') . '?s=' . $pubstamp . '</a>'; ?>
			<?php if ($this->project->private == 0) { 
				$act = $listed ? 'unlist' : 'publist'; ?>
			<span><?php echo JText::_('COM_PROJECTS_NOTES_THIS_PAGE_IS'); ?>  <strong class="<?php echo $listed ? 'green' : 'urgency'; ?>"><?php echo $listed ? JText::_('COM_PROJECTS_NOTES_LISTED') : JText::_('COM_PROJECTS_NOTES_UNLISTED'); ?></strong>. <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes') . '?action=share&p=' . $this->page->id; ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_SETTINGS'); ?> &rsaquo;</a></span>	
			<?php } ?>
			</p>
		<?php } elseif ($this->pparams->get('enable_publinks') && $this->page) { ?>
			<p class="publink"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_GET_LINK'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes') . '?action=share&p=' . $this->page->id; ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_GENERATE_LINK'); ?></a></p>
		<?php } ?>
	<?php 
	// Allow subpages up until third level
	if(($this->task == 'view' or $this->task == 'page') && count($this->parent_notes) < 2) { ?>
	</div>
	<div class="clear"></div>
	<?php } ?>
</div>

