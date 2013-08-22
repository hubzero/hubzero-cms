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

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = ProjectsHtml::cleanText($this->project->about);

$goto  = 'alias=' . $this->project->alias;

$title = $this->project->title ? JText::_('COM_PROJECTS_NEW_PROJECT').': '.$this->project->title : $this->title;

$html  = '';
?>
<div id="content-header" class="full">
	<h2><?php echo $title; ?> <?php if($this->gid && is_object($this->group)) { ?> <?php echo JText::_('COM_PROJECTS_FOR').' '.ucfirst(JText::_('COM_PROJECTS_GROUP')); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups'.a.'gid='.$this->group->get('cn')); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($this->group->get('description'), 50, 0); ?></a><?php } ?></h2>
</div><!-- / #content-header -->
<div class="main section" id="setup">
	<ul id="status-bar" class="moving">
		<li <?php if($this->stage == 0) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=0'; ?>"<?php if($this->project->setup_stage >= 1) { echo ' class="c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?></a><?php } ?></li>
		<li <?php if($this->stage == 1) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=1'; ?>"><?php } ?><?php echo JText::_('COM_PROJECTS_ADD_TEAM'); ?><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?></a><?php } ?></li>
		<li><?php echo JText::_('COM_PROJECTS_READY_TO_GO'); ?></li>
	</ul>
<div class="clear"></div>
	<div class="info_blurb">
		<div class="pthumb"><img src="<?php echo $this->thumb_src; ?>" alt="" /></div>
		<div class="pinfo">
			<p class="info_title"><span class="block italic"><?php echo $this->typetitle.' '.strtolower(JText::_('COM_PROJECTS_PROJECT')); ?>:</span> <?php echo $this->project->title; ?> (<span class="aliasname"><?php echo $this->project->alias; ?></span>)</p>
			<?php if ($this->project->about && $this->project->about != '') { ?>
			<p class="mini"><?php echo Hubzero_View_Helper_Html::shortenText($this->project->about, 100, 0); ?></p>
			<?php } ?>
			<p class="actionlink"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=0'; ?>">&laquo; <?php echo JText::_('COM_PROJECTS_CHANGE_THIS_INFO'); ?></a></p>
		</div>
		<div class="clear"></div>
	</div>
		<div class="status-msg">
		<?php 
			// Display error or success message
			if ($this->getError()) { 
				echo ('<p class="witherror">' . $this->getError().'</p>');
			}
			else if($this->msg) {
				echo ('<p>' . $this->msg . '</p>');
			} ?>
		</div>
	<?php 
		$html .= t.' <form id="hubForm" method="post" action="index.php">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<h4>'.JText::_('COM_PROJECTS_HOWTO_TITLE_ROLES').'</h4>'.n;
		$html .= t.t.'<p><span class="italic prominent">'.ucfirst(JText::_('COM_PROJECTS_LABEL_COLLABORATORS')).'</span> '.JText::_('COM_PROJECTS_CAN').':</p>'.n;
		$html .= t.t.'<ul>'.n;
		$html .= t.t.'<li>'.JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE').'</li>'.n;
		$html .= t.t.'<li>'.JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO').'</li>'.n;
		$html .= t.t.'<li>'.JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE').'</li>'.n;
		$html .= t.t.'</ul>'.n;
		$html .= t.t.'<p><span class="italic prominent">'.ucfirst(JText::_('COM_PROJECTS_LABEL_OWNERS')).'</span> '.JText::_('COM_PROJECTS_CAN').':</p>'.n;
		$html .= t.t.'<ul>'.n;
		$html .= t.t.'<li>'.JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE').'</li>'.n;
		$html .= t.t.'<li>'.JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO').'</li>'.n;
		$html .= t.t.'<li><strong>'.JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE').'</strong></li>'.n;
		$html .= t.t.'</ul>'.n;
		if($this->gid) {
			$html .= t.t.'<h4>'.JText::_('COM_PROJECTS_HOWTO_GROUP_PROJECT').'</h4>'.n;		
			$html .= t.t.'<p>'.JText::_('COM_PROJECTS_HOWTO_GROUP_EXPLAIN').'</p>'.n;			
		}
		$html .= t.'</div>'.n;
		$html .= t.t.'<fieldset class="wider">'.n;
		$html .= t.t.t.'<input type="hidden"  name="task" value="setup" />'.n;
		$html .= t.t.t.'<input type="hidden"  name="step" value="1" />'.n;
		$html .= t.t.t.'<input type="hidden"  name="save_stage" value="2" />'.n;
		$html .= t.t.t.'<input type="hidden" id="option" name="option" value="'.$this->option.'" />'.n;
		$html .= t.t.t.'<input type="hidden" id="pid" name="id" value="'.$this->project->id.'" />'.n;
		$html .= t.t.t.'<input type="hidden" id="tempid" name="tempid" value="'.$this->tempid.'" />'.n;	
		$html .= t.t.t.'<input type="hidden" id="gid" name="gid" value="'.$this->gid.'" />'.n;
		$html .= t.t.'<h2>'.JText::_('COM_PROJECTS_ADD_MEMBERS_TO_PROJECT').'</h2>'.n;
		echo $html;
	?>
	<div id="cbody">
		<?php echo $this->content; ?>
	</div>
	<?php
		$html  = t.t.t.'<p class="submitarea"><input type="submit" value="'.JText::_('COM_PROJECTS_SAVE_AND_CONTINUE').'" class="btn" /></p>'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.' </form>'.n;
		echo $html;
	?>
	<div class="clear"></div>
</div>
