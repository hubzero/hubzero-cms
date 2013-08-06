<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>">
				<?php echo JText::_('COM_GROUPS_NEW'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach($this->notifications as $notification) 
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<div id="introduction" class="section">
	<div class="aside">
		<h3>Questions?</h3>
		<ul>
			<li>
				<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=groups&page=index'); ?>">
					<?php echo JText::_('Need Help?'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3><?php echo JText::_('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_TITLE'); ?></h3>
			<p><?php echo JText::_('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_DESC'); ?></p>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('COM_GROUPS_INTRO_HOW_DO_GROUPS_WORK_TITLE'); ?></h3>
			<p><?php echo JText::_('COM_GROUPS_INTRO_HOW_DO_GROUPS_WORK_DESC'); ?></p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	<?php if(isset($this->mygroups['invitees']) && count($this->mygroups['invitees']) > 0) : ?>
		<div class="invites">
			<div class="header">
				<h2>Group Invites</h2>
				<p>Below is a list of your current group invites.</p>
			</div>
			<ul>
				<?php foreach($this->mygroups['invitees'] as $invite) : ?>
					<li><?php echo $invite->description; ?><a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$invite->cn.'&task=accept'); ?>">Accept Invite</a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
		
	<?php if(isset($this->mygroups['applicants']) && count($this->mygroups['applicants']) > 0) : ?>
		<div class="requests">
			<div class="header">
				<h2>Group Requests</h2>
				<p>Below is a list of your pending group requests.</p>
			</div>
			<ul>
				<?php foreach($this->mygroups['applicants'] as $applicant) : ?>
					<li><?php echo $applicant->description; ?><a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$applicant->cn.'&task=cancel'); ?>">Cancel Request</a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	
	<div class="four columns first">
		<h2>
			<?php echo JText::_('COM_GROUPS_INTRO_FIND_GROUP'); ?>
		</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<label for="gsearch"><?php echo JText::_('COM_GROUPS_INTRO_FIND_GROUP_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="gsearch" value="" />
						<input type="submit" value="Search" />
					</p>
					<p><?php echo JText::_('COM_GROUPS_INTRO_FIND_GROUP_SEARCH_HELP'); ?></p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p>
					<a class="group-intro-browse" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>">
						<?php echo JText::_('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_BUTTON_TEXT'); ?>
					</a>
				</p>
				<p><?php echo JText::_('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_HELP'); ?></p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	<?php if(!$this->juser->get("guest")) : ?>
		<?php if($this->config->get("intro_mygroups", 1)) : ?>
			<div class="clearfix">
				<div class="four columns first">
					<h2><?php echo JText::_('COM_GROUPS_INTRO_MY_GROUPS_TITLE'); ?></h2>
				</div><!-- / .four columns first -->
				<div class="four columns second third fourth">
					<div class="clearfix top">
						<?php
							$mygroups_members = (isset($this->mygroups['members'])) ? $this->mygroups['members'] : array();
							echo Hubzero_Group_Helper::listGroups('My Groups',$this->config,$mygroups_members,2,true,true,0); ?>
					</div>
				</div><!-- / .four columns second third fourth -->
			</div><!-- /.clearfix -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if(!$this->juser->get("guest")) : ?>
		<?php if($this->config->get("intro_interestinggroups", 1)) : ?>
			<div class="clearfix">
				<div class="four columns first">
					<h2><?php echo JText::_('COM_GROUPS_INTRO_INTERESTING_GROUPS_TITLE'); ?></h2>
				</div><!-- / .four columns first -->
				<div class="four columns second third fourth">
					<div class="clearfix top">
						<?php echo Hubzero_Group_Helper::listGroups('Interesting Groups',$this->config,$this->interestinggroups,2,true,false,150); ?>
					</div>
				</div><!-- / .four columns second third fourth -->
			</div><!-- /.clearfix -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if($this->config->get("intro_populargroups", 1)) : ?>
		<div class="clearfix">
			<div class="four columns first">
				<h2><?php echo JText::_('COM_GROUPS_INTRO_POPULAR_GROUPS_TITLE'); ?></h2>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<div class="clearfix top">
					<?php echo Hubzero_Group_Helper::listGroups('Popular Groups',$this->config,$this->populargroups,2,true,false,150); ?>
				</div>
			</div><!-- / .four columns second third fourth -->
		</div><!-- /.clearfix -->
	<?php endif; ?>
	
	<?php if($this->config->get("intro_featuredgroups", 1) && count($this->featuredgroups) > 0) : ?>
		<div class="clearfix">
			<div class="four columns first">
				<h2><?php echo JText::_('COM_GROUPS_INTRO_FEATURED_GROUPS_TITLE'); ?></h2>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<div class="clearfix top">
					<?php echo Hubzero_Group_Helper::listGroups('Featured Groups',$this->config,$this->featuredgroups,2,true,false,150); ?>
				</div>
			</div><!-- / .four columns second third fourth -->
		</div><!-- /.clearfix -->
	<?php endif; ?>
</div><!-- / .section -->