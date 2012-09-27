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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>"><?php echo JText::_('Create User Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<div id="introduction" class="section">
	<div class="aside">
		<h3>Questions?</h3>
		<ul>
			<li>
				<a href="/kb/groups/faq">Groups FAQ</a>
			</li>
			<li>
				<a href="/kb/groups/guide">Group Guidelines</a>
			</li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3>What are groups?</h3>
			<p>Groups are an easy way to share content and conversation, either privately or with the world. Many times, a group already exists for a specific interest or topic. If you can't find one you like, feel free to start your own.</p>
		</div>
		<div class="two columns second">
			<h3>How do groups work?</h3>
			<p>Groups can either be public, restricted (users may read a brief description or overview but not view content) or completely private. Every group has a wiki, a pool for resources, and a discussion board for talking.</p>
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
					<li><?php echo $invite->description; ?><a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$invite->cn.'&task=accept'); ?>">Accept Invite</a></li>
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
					<li><?php echo $applicant->description; ?><a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$applicant->cn.'&task=cancel'); ?>">Cancel Request</a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	
	<div class="four columns first">
		<h2>Find a group</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<label for="gsearch">Keyword or phrase:</label>
						<input type="text" name="search" id="gsearch" value="" />
						<input type="submit" value="Search" />
					</p>
					<p>
						Search group names and public descriptions. Private groups do not show up in results.
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>">Browse the list of available groups</a></p>
				<p>A list of all public and restricted groups. Private groups are not listed.</p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	<?php if(!$this->user->get("guest")) : ?>
		<?php if($this->config->get("intro_mygroups", 1)) : ?>
			<div class="clearfix">
				<div class="four columns first">
					<h2><?php echo JText::_('COM_GROUPS_MY_GROUPS'); ?></h2>
				</div><!-- / .four columns first -->
				<div class="four columns second third fourth">
					<div class="clearfix top">
						<?php echo Hubzero_Group_Helper::listGroups(JText::_('COM_GROUPS_MY_GROUPS'),$this->config,$this->mygroups['members'],2,true,true,0); ?>
					</div>
				</div><!-- / .four columns second third fourth -->
			</div><!-- /.clearfix -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if(!$this->user->get("guest")) : ?>
		<?php if($this->config->get("intro_interestinggroups", 1)) : ?>
			<div class="clearfix">
				<div class="four columns first">
					<h2><?php echo JText::_('COM_GROUPS_INTERESTING_GROUPS'); ?></h2>
				</div><!-- / .four columns first -->
				<div class="four columns second third fourth">
					<div class="clearfix top">
						<?php echo Hubzero_Group_Helper::listGroups(JText::_('COM_GROUPS_INTERESTING_GROUPS'),$this->config,$this->interestinggroups,2,true,false,150); ?>
					</div>
				</div><!-- / .four columns second third fourth -->
			</div><!-- /.clearfix -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if($this->config->get("intro_populargroups", 1)) : ?>
		<div class="clearfix">
			<div class="four columns first">
				<h2><?php echo JText::_('COM_GROUPS_POPULAR_GROUPS'); ?></h2>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<div class="clearfix top">
					<?php echo Hubzero_Group_Helper::listGroups(JText::_('COM_GROUPS_POPULAR_GROUPS'),$this->config,$this->populargroups,2,true,false,150); ?>
				</div>
			</div><!-- / .four columns second third fourth -->
		</div><!-- /.clearfix -->
	<?php endif; ?>
</div><!-- / .section -->
