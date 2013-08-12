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
$use_alias = $this->config->get('use_alias', 0);
$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="browse" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>"><?php echo JText::_('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>
<?php if ($this->getError() || $this->msg) { ?>
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
<?php } ?>
<div class="clear block">&nbsp;</div>
<div id="introduction" class="section">
 <div id="introbody">
	<div class="subject">
		<div class="two columns first">
			<h3><?php echo JText::_('COM_PROJECTS_INTRO_COLLABORATION_MADE_EASY'); ?></h3>
			<p><?php echo JText::_('COM_PROJECTS_INTRO_COLLABORATION_HOW'); ?></p>
			<p class="emphasized"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=start'); ?>" id="projects-intro-start"><?php echo JText::_('COM_PROJECTS_START_PROJECT'); ?></a></p>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('COM_PROJECTS_INTRO_WHAT_YOU_GET'); ?></h3>
			<ul>
				<li><?php echo JText::_('COM_PROJECTS_INTRO_GET_REPOSITORY'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_INTRO_GET_WIKI'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_INTRO_GET_TODO'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_INTRO_GET_BLOG'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_INTRO_GET_PUBLISHING'); ?></li>
			</ul>
			<p class="subnote">*<?php echo JText::_('COM_PROJECTS_INTRO_PUBLISHING_NOTE'); ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=features'); ?>" id="projects-intro-features"><?php echo JText::_('COM_PROJECTS_LEARN_MORE'); ?></a></p>
		</div>
	</div>
	<div class="clear"></div>
 </div>
</div><!-- / #introduction.section -->

<div class="clear"></div>
<div class="section myprojects">
	<div class="four columns first">
		<h2><?php echo JText::_('COM_PROJECTS_MY_PROJECTS'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<?php
		if(count($this->rows) > 0) { 	?>		
			<ul class="flow">
				<?php foreach($this->rows as $row) { 
				$goto  = $use_alias ? 'alias='.$row->alias : 'id='.$row->id;
				$thumb = ProjectsHtml::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
				$setup = ($row->setup_stage < $setup_complete) ? JText::_('COM_PROJECTS_COMPLETE_SETUP') : '';
				?>
				<li <?php if($setup) { echo 'class="s-dev"'; } else if($row->state == 0) { echo 'class="s-inactive"'; } else if($row->state == 5) { echo 'class="s-pending"'; } ?>>
					<?php  if(!$setup && $row->private) { ?><span class="s-private">&nbsp;</span><?php }  ?>	
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto); ?>"><img src="<?php echo $thumb; ?>" alt="" /><span class="block"><?php echo Hubzero_View_Helper_Html::shortenText(ProjectsHtml::cleanText($row->title), 30, 0); ?></span></a><?php if($setup) { ?><span class="s-complete"><?php echo JText::_('COM_PROJECTS_COMPLETE_SETUP'); ?></span><?php } else if($row->state == 0) { ?><span class="s-suspended"><?php echo JText::_('COM_PROJECTS_STATUS_INACTIVE'); ?></span> <?php } else if($row->state == 5) { ?><span class="s-suspended"><?php echo JText::_('COM_PROJECTS_STATUS_PENDING'); ?></span> <?php } ?>
				<?php if($row->newactivity && $row->state == 1 && !$setup) { ?><span class="s-new"><?php echo $row->newactivity; ?></span><?php } ?>	
				</li>
				<?php }	?>
			</ul>
		<?php } else { ?>
			<div class="noresults"><?php echo ($this->guest) ? JText::_('COM_PROJECTS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$this->option.a.'task=intro').'/?action=login" id="projects-intro-login">'.JText::_('COM_PROJECTS_LOGIN').'</a> '.JText::_('COM_PROJECTS_TO_VIEW_YOUR_PROJECTS') : JText::_('COM_PROJECTS_YOU_DONT_HAVE_PROJECTS'); ?></div>
		<?php }	?>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>

