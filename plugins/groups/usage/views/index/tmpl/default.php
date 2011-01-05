<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$cls = 'even';
?>
<h3><a name="usage"></a><?php echo JText::_('USAGE'); ?></h3>
<div class="aside">
	<p class="info"><?php echo JText::_('USAGE_EXPLANATION'); ?></p>
</div><!-- / .aside -->
<div class="subject" id="statistics">
	<table class="data" summary="<?php echo JText::_('TBL_SUMMARY_OVERVIEW'); ?>">
		<caption><?php echo JText::_('TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_MEMBERS'); ?>:</th>
				<td><?php echo count($this->group->get('members')); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_RESOURCES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getResourcesCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_WIKI_PAGES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikipageCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_WIKI_FILES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikifileCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_OPEN_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'open'); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_CLOSED_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'closed'); ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_TH_STICKY_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'sticky'); ?></td>
			</tr>
		</tbody>
	</table>

<?php 
	$xlog = new XGroupLog( $this->database );
	
	$group_edits          = $xlog->logCount($this->group->get('gidNumber'), 'group_edited');
	$membership_requests  = $xlog->logCount($this->group->get('gidNumber'), 'membership_requested');
	$membership_accepted  = $xlog->logCount($this->group->get('gidNumber'), 'membership_approved');
	$membership_denied    = $xlog->logCount($this->group->get('gidNumber'), 'membership_denied');
	$membership_cancelled = $xlog->logCount($this->group->get('gidNumber'), 'membership_cancelled');
	$invites_sent         = $xlog->logCount($this->group->get('gidNumber'), 'membership_invites_sent');
	$invites_accepted     = $xlog->logCount($this->group->get('gidNumber'), 'membership_invite_accepted');
	$promotions           = $xlog->logCount($this->group->get('gidNumber'), 'membership_promoted');
	$demotions            = $xlog->logCount($this->group->get('gidNumber'), 'membership_demoted');
?>
	<table class="data" summary="<?php echo JText::_('TBL_SUMMARY_ACTIVITY'); ?>">
		<caption><?php echo JText::_('TBL_CAPTION_ACTIVITY'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_GROUP_EDITS'); ?>:</th>
				<td><?php echo $group_edits; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_REQUESTS'); ?>:</th>
				<td><?php echo $membership_requests; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_ACCEPTED'); ?>:</th>
				<td><?php echo $membership_accepted; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_DENIED'); ?>:</th>
				<td><?php echo $membership_denied; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_CANCELLED'); ?>:</th>
				<td><?php echo $membership_cancelled; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_INVITES_SENT'); ?>:</th>
				<td><?php echo $invites_sent; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_INVITES_ACCEPTED'); ?>:</th>
				<td><?php echo $invites_accepted; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_PROMOTIONS'); ?>:</th>
				<td><?php echo $promotions; ?></td>
			</tr>
<?php $cls = (($cls == 'even') ? 'odd' : 'even'); ?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><?php echo JText::_('TBL_DEMOTIONS'); ?>:</th>
				<td><?php echo $demotions; ?></td>
			</tr>
		</tbody>
	</table>
</div><!-- / .subject -->