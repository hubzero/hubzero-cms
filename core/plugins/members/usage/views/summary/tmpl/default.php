<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = 'even';

$this->css('usage', 'com_usage');
?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_USAGE'); ?></h3>

<p class="info"><?php echo Lang::txt('PLG_MEMBERS_USAGE_EXPLANATION'); ?></p>

<div id="statistics">
	<table class="data">
		<caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS'); ?>:</th>
				<td><?php echo $this->contribution['contribs']; ?></td>
			</tr>
		<?php /*if ($this->total_tool_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_TOOLS'); ?>:</th>
				<td><?php echo number_format($this->total_tool_users); ?></td>
			</tr>
		<?php } ?>
		<?php if ($this->total_andmore_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_ANDMORE'); ?>:</th>
				<td><?php echo number_format($this->total_andmore_users); ?></td>
			</tr>
		<?php }*/ ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK'); ?>:</th>
				<td><?php echo $this->rank; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST'); ?>:</th>
				<td><?php echo $this->contribution['first']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST'); ?>:</th>
				<td><?php echo $this->contribution['last']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CITATIONS'); ?>:</th>
				<td><?php echo $this->citation_count; ?></td>
			</tr>
		<?php if ($this->cluster_users) { ?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CLUSTERS'); ?>:</th>
				<td><?php echo number_format($this->cluster_users).' users served in '.number_format($this->cluster_classes).' courses from '.number_format($this->cluster_schools).' institutions'; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<table class="data">
		<caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_TOOLS'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_TOOL_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->tool_stats)
		{
			$count = 0;
			$cls = 'even';
			$sum_usercount_12 = 0;
			$sum_usercount_14 = 0;
			$sum_simcount_12 = 0;
			$sum_simcount_14 = 0;
			foreach ($this->tool_stats as $row)
			{
				$user_count_12 = plgMembersUsage::get_usercount($row->id, 12, 7);
				$user_count_14 = plgMembersUsage::get_usercount($row->id, 14, 7);
				$sim_count_12 = plgMembersUsage::get_simcount($row->id, 12);
				$sim_count_14 = plgMembersUsage::get_simcount($row->id, 14);

				$sum_usercount_12 += $user_count_12;
				$sum_usercount_14 += $user_count_14;
				$sum_simcount_12 += $sim_count_12;
				$sum_simcount_14 += $sim_count_14;
				?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<td><?php echo ($count + 1); ?></td>
					<td class="textual-data"><a href="<?php echo Route::url('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a></td>
					<td><a href="<?php echo Route::url('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php echo (is_numeric($user_count_12)) ? number_format($user_count_12) : $user_count_12; ?></a></td>
					<td><a href="<?php echo Route::url('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php echo (is_numeric($sim_count_12)) ? number_format($sim_count_12) : $sim_count_12; ?></a></td>
					<td><a href="<?php echo Route::url('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php  echo (is_numeric($user_count_14)) ? number_format($user_count_14) : $user_count_14; ?></a></td>
					<td><a href="<?php echo Route::url('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php echo (is_numeric($sim_count_14)) ? number_format($sim_count_14) : $sim_count_14; ?></a></td>
					<td><?php echo plgMembersUsage::get_citationcount($row->id, 0); ?></td>
					<td><?php echo $row->publish_up; ?></td>
				</tr>
				<?php
				$count++;
			}
			if ($count) //$this->tool_total_14 && $this->tool_total_12)
			{
				?>
				<tr class="summary">
					<td></td>
					<td class="textual-data"><?php echo Lang::txt('TOTAL'); ?></td>
					<td><?php echo number_format($sum_usercount_12); //$this->tool_total_12); ?></td>
					<td><?php echo number_format($sum_simcount_12); ?></td>
					<td><?php echo number_format($sum_usercount_14); //$this->tool_total_14); ?></td>
					<td><?php echo number_format($sum_simcount_14); ?></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}
		} else { ?>
			<tr class="odd">
				<td colspan="8" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<table class="data">
		<caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_RESOURCES'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_RESOURCE_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->andmore_stats)
		{
			$cls = 'even';
			$count = 0;
			$total = array(
				'usercount12' => 0,
				'usercount14' => 0,
				'citations'   => 0
			);
			foreach ($this->andmore_stats as $row)
			{
				$result = plgMembersUsage::get_usercount($row->id, 12);
				$usercount12 = (is_numeric($result)) ? number_format($result) : $result;

				$result = plgMembersUsage::get_usercount($row->id, 14);
				$usercount14 = (is_numeric($result)) ? number_format($result) : $result;

				$cites = plgMembersUsage::get_citationcount($row->id, 0);

				$total['usercount12'] += (int)$usercount12;
				$total['usercount14'] += (int)$usercount14;
				$total['citations'] += (int)$cites;
				?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<td><?php echo ($count + 1); ?></td>
					<td class="textual-data"><a href="<?php echo Route::url('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a> <span class="small"><?php echo $row->type; ?></span></td>
					<td><?php echo $usercount12; ?></td>
					<td><?php echo $usercount14 ?></td>
					<td><?php echo $cites ?></td>
					<td><?php echo $row->publish_up; ?></td>
				</tr>
				<?php
				$count++;
			}
			//if ($this->andmore_total_14 && $this->andmore_total_12) {
			if ($count)
			{
				?>
				<tr class="summary">
					<td></td>
					<td><?php echo Lang::txt('TOTAL'); ?></td>
					<td><?php echo $total['usercount12']; //number_format($this->andmore_total_12); ?></td>
					<td><?php echo $total['usercount14']; //number_format($this->andmore_total_14); ?></td>
					<td><?php echo $total['citations']; ?></td>
					<td></td>
				</tr>
				<?php
			}
		} else { ?>
			<tr class="odd">
				<td colspan="6" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p><?php echo Lang::txt('PLG_MEMBERS_USAGE_FOOTNOTE'); ?></p>
</div>
