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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/new?case=' . $this->report->id;
}
else
{
	$sef = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new&case=' . $this->report->id);
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

$base = rtrim(str_replace('/administrator', '', $base), DS);

$this->commentor = JFactory::getUser($this->reported->author);

$this->css(
	'@media only screen and (max-device-width: 480px) {
		#ticket-number {
			float: none !important;
			width: auto !important;
		}
	}'
);
?>
	<!-- Start Header -->
	<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
					<?php echo Config::get('sitename'); ?>
				</td>
				<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
					<span class="home">
						<a href="<?php echo $juri->base(); ?>"><?php echo $juri->base(); ?></a>
					</span>
					<br />
					<span class="description"><?php echo Config::get('MetaDesc'); ?></span>
				</td>
				<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
					<?php echo Lang::txt('COM_SUPPORT_CENTER'); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #e9bcbc; background: #ffd3d4; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
										color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent),
										color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)),
										color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent),
										to(transparent));
	background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
									-webkit-background-size: 30px 30px;
									-moz-background-size: 30px 30px;
									background-size: 30px 30px;">
		<thead>
			<tr>
				<th style="font-weight: normal; border-bottom: 1px solid #e9bcbc; padding: 16px 30px; text-align: center; font-size: 1.5em; color: #e96c6c;" align="left">
					<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_CONTENT_REPORTED'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="100%" style="padding: 8px;">
					<div id="ticket-number" style="float: left; width: 5em; font-size: 2.5em; font-weight: bold; text-align: center; padding: 30px;" align="center">
						<span style="white-space: nowrap;">case #</span><br />
						<span style="font-size: 2.5em; line-height: 1.1em; font-weight: bold;"><?php echo $this->report->id; ?></span>
					</div>
					<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Reason:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($this->report->subject); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Reported:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left">@ <?php echo JHTML::_('date', $this->report->created, Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', $this->report->created, Lang::txt('DATE_FORMAT_HZ1')); ?></td>
							</tr>
						<?php if (!$this->author) { ?>
							<?php $reporter = JFactory::getUser($this->report->created_by); ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Reported by:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($reporter->get('name')); ?> (<?php echo $this->escape($reporter->get('username')); ?>)</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Comments:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($this->report->report); ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->report->reviewed && $this->report->reviewed != '0000-00-00 00:00:00') { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Reviewed:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left">@ <?php echo JHTML::_('date', $this->report->reviewed, Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', $this->report->reviewed, Lang::txt('DATE_FORMAT_HZ1')); ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->report->note && !$this->author) { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Note:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->report->note; ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->author) { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Dispute:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left">The content marked as inappropriate is presented below in its entirety. If you wish to dispute the report, please file a ticket with our <a href="<?php echo $link; ?>">support center</a> and reference case #<?php echo $this->report->id; ?>.</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table width="100%" id="ticket-comments" style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<th style="text-align: left;" align="left"><?php echo $this->commentor->get('name'); ?> (<?php echo $this->commentor->get('username'); ?>)</th>
				<th class="timestamp" style="color: #999; text-align: right;" align="right"><span class="mobilehide">@ <?php echo JHTML::_('date', $this->reported->created, Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', $this->reported->created, Lang::txt('DATE_FORMAT_HZ1')); ?></span></th>
			</tr>
			<tr>
				<td colspan="2" style="padding: 0 2em;">
					<?php
					if (!strstr($this->reported->text, '</p>') && !strstr($this->reported->text, '<pre class="wiki">'))
					{
						$this->reported->text = str_replace("<br />", '', $this->reported->text);
						$this->reported->text = $this->escape($this->reported->text);
						$this->reported->text = nl2br($this->reported->text);
						$this->reported->text = str_replace("\t", ' &nbsp; &nbsp;', $this->reported->text);
						$this->reported->text = preg_replace('/  /', ' &nbsp;', $this->reported->text);
					}
					?>
					<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->reported->text; ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<!-- Start Footer -->
	<table class="tbl-footer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom">
					<span><?php echo Config::get('sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $base; ?>"><?php echo $base; ?></a>. Visit our <a href="<?php echo $base; ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo $base; ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->