<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$cls = 'even';

$this->css('usage', 'com_usage');
?>
<h3 class="section-header"><?php echo JText::_('PLG_MEMBERS_USAGE'); ?></h3>

<div class="aside">
	<p class="info"><?php echo JText::_('PLG_MEMBERS_USAGE_EXPLANATION'); ?></p>
</div><!-- / .aside -->
<div class="subject" id="statistics">
	<table class="data">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS'); ?>:</th>
				<td><?php echo $this->contribution['contribs']; ?></td>
			</tr>
		<?php if ($this->total_tool_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_TOOLS'); ?>:</th>
				<td><?php echo number_format($this->total_tool_users); ?></td>
			</tr>
		<?php } ?>
		<?php if ($this->total_andmore_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_ANDMORE'); ?>:</th>
				<td><?php echo number_format($this->total_andmore_users); ?></td>
			</tr>
		<?php } ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK'); ?>:</th>
				<td><?php echo $this->rank; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST'); ?>:</th>
				<td><?php echo $this->contribution['first']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST'); ?>:</th>
				<td><?php echo $this->contribution['last']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_CITATIONS'); ?>:</th>
				<td><?php echo $this->citation_count; ?></td>
			</tr>
		<?php if ($this->cluster_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_CLUSTERS'); ?>:</th>
				<td><?php echo number_format($this->cluster_users).' users served in '.number_format($this->cluster_classes).' courses from '.number_format($this->cluster_schools).' institutions'; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<table class="data">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_TOOLS'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_TOOL_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->tool_stats)
		{
			$count = 1;
			$cls = 'even';
			$sum_simcount_12 = 0;
			$sum_simcount_14 = 0;
			foreach ($this->tool_stats as $row)
			{
				$sim_count_12 = plgMembersUsage::get_simcount($row->id, 12);
				$sim_count_14 = plgMembersUsage::get_simcount($row->id, 14);
				$sum_simcount_12 += $sim_count_12;
				$sum_simcount_14 += $sim_count_14;
			?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<td><?php echo $count; ?></td>
				<td class="textual-data"><a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php $result = plgMembersUsage::get_usercount($row->id, 12, 7); echo (is_numeric($result)) ? number_format($result) : $result; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php echo (is_numeric($sim_count_12)) ? number_format($sim_count_12) : $sim_count_12; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php $result = plgMembersUsage::get_usercount($row->id, 14, 7); echo (is_numeric($result)) ? number_format($result) : $result; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php echo (is_numeric($sim_count_14)) ? number_format($sim_count_14) : $sim_count_14; ?></a></td>
				<td><?php echo plgMembersUsage::get_citationcount($row->id, 0); ?></td>
				<td><?php echo $row->publish_up; ?></td>
			</tr>
			<?php
				$count++;
			}
			if ($this->tool_total_14 && $this->tool_total_12) {
			?>
			<tr class="summary">
				<td></td>
				<td class="textual-data"><?php echo JText::_('TOTAL'); ?></td>
				<td><?php echo number_format($this->tool_total_12); ?></td>
				<td><?php echo number_format($sum_simcount_12); ?></td>
				<td><?php echo number_format($this->tool_total_14); ?></td>
				<td><?php echo number_format($sum_simcount_14); ?></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			}
		} else { ?>
			<tr class="odd">
				<td colspan="8" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<table class="data">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_RESOURCES'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_RESOURCE_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->andmore_stats)
		{
			$cls = 'even';
			$count = 1;
			foreach ($this->andmore_stats as $row)
			{
			?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<td><?php echo $count; ?></td>
				<td class="textual-data"><a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a> <span class="small"><?php echo $row->type; ?></span></td>
				<td><?php $result = plgMembersUsage::get_usercount($row->id,12);
				echo (is_numeric($result)) ? number_format($result) : $result; ?></td>
				<td><?php
				$result = plgMembersUsage::get_usercount($row->id,14);
				echo (is_numeric($result)) ? number_format($result) : $result; ?></td>
				<td><?php echo plgMembersUsage::get_citationcount($row->id, 0); ?></td>
				<td><?php echo $row->publish_up; ?></td>
			</tr>
			<?php
				$count++;
			}
			if ($this->andmore_total_14 && $this->andmore_total_12) {
			?>
			<tr class="summary">
				<td></td>
				<td><?php echo JText::_('TOTAL'); ?></td>
				<td><?php echo number_format($this->andmore_total_12); ?></td>
				<td><?php echo number_format($this->andmore_total_14); ?></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			}
		} else { ?>
			<tr class="odd">
				<td colspan="6" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php echo '* Total only includes versions of the tools this author contributed to.'; ?>
</div><!-- / .subject -->
