<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// get group logger to get created log entry
$logger     = \Components\Groups\Models\Log\Archive::getInstance();

//parse the logs
$group_edits          = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'group_edited'), true)->count();
$membership_requests  = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_requested'), true)->count();
$membership_accepted  = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_approved'), true)->count();
$membership_denied    = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_denied'), true)->count();
$membership_cancelled = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_cancelled'), true)->count();
$invites_sent         = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_invites_sent'), true)->count();
$invites_accepted     = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_invite_accepted'), true)->count();
$promotions           = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_promoted'), true)->count();
$demotions            = $logger->logs('list', array('gidNumber' => $this->group->get('gidNumber'), 'action' => 'membership_demoted'), true)->count();
?>
<h3 class="heading"><?php echo Lang::txt('USAGE'); ?></h3>

<section class="main section">
	<div id="page_views">

		<div id="page_views_heading">
			<h3>Group Page Views</h3>
			<div id="page_view_settings">
				<form name="page_selector" action="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=usage'); ?>" method="get">
					<select name="pid" id="page_view_selector">
						<option value=""<?php if ($this->pid == '') { echo "selected"; } ?>>All Group Page Views</option>
						<?php foreach ($this->pages as $page) : ?>
							<?php $sel = ($this->pid == $page['id']) ? "selected" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $page['id']; ?>"><?php echo $page['title']; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="datepickers">
						<input type="text" name="start" id="date_start" class="datepicker" value="<?php echo date("m/d/Y", strtotime($this->start)); ?>" />&nbsp;-&nbsp;
						<input type="text" name="end" id="date_end" class="datepicker" value="<?php echo date("m/d/Y", strtotime($this->end)); ?>" />
					</span>
					<input type="submit" id="submit" value="Go" />
				</form>
			</div><!-- /.end page_view_settings -->
		</div><!-- /.end page_views_heading -->

		<div id="page_views_chart">
			<noscript>
				<p class="info">To view this page views graph, Javascript must be enabled.</p>
			</noscript>
		</div>
	</div>

	<table class="data">
		<caption><?php echo Lang::txt('TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo Lang::txt('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_PAGES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupPagesCount($this->group); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_TH_MEMBERS'); ?>:</th>
				<td><?php echo count($this->group->get('members')); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_RESOURCES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getResourcesCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_TH_OPEN_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'open'); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_CLOSED_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'closed'); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_TH_STICKY_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'sticky'); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_WIKI_PAGES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikipageCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_TH_WIKI_FILES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikifileCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_BLOG'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupBlogCount($this->group->get('gidNumber')); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_TH_BLOG_COMMENTS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupBlogCommentCount($this->group->get('gidNumber')); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_TH_CALENDAR'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupCalendarCount($this->group->get('gidNumber')); ?></td>
			</tr>
		</tbody>
	</table>

	<table class="data">
		<caption><?php echo Lang::txt('TBL_CAPTION_ACTIVITY'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo Lang::txt('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_GROUP_EDITS'); ?>:</th>
				<td><?php echo $group_edits; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_MEMBERSHIP_REQUESTS'); ?>:</th>
				<td><?php echo $membership_requests; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_MEMBERSHIP_ACCEPTED'); ?>:</th>
				<td><?php echo $membership_accepted; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_MEMBERSHIP_DENIED'); ?>:</th>
				<td><?php echo $membership_denied; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_MEMBERSHIP_CANCELLED'); ?>:</th>
				<td><?php echo $membership_cancelled; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_INVITES_SENT'); ?>:</th>
				<td><?php echo $invites_sent; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_INVITES_ACCEPTED'); ?>:</th>
				<td><?php echo $invites_accepted; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo Lang::txt('TBL_PROMOTIONS'); ?>:</th>
				<td><?php echo $promotions; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo Lang::txt('TBL_DEMOTIONS'); ?>:</th>
				<td><?php echo $demotions; ?></td>
			</tr>
		</tbody>
	</table>
</section><!-- /.main section -->