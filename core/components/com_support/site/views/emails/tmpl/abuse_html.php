<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/new?case=' . $this->report->id;
}
else
{
	$sef = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new&case=' . $this->report->id);
}
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$base = rtrim(str_replace('/administrator', '', $base), '/');

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
						<a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>
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
						<span style="white-space: nowrap;"><?php echo Lang::txt('COM_SUPPORT_EMAIL_CASE_NUM'); ?></span><br />
						<span style="font-size: 2.5em; line-height: 1.1em; font-weight: bold;"><?php echo $this->report->id; ?></span>
					</div>
					<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_REASON'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($this->report->subject); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_REPORTED'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left">@ <?php echo Date::of($this->report->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo Date::of($this->report->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
							</tr>
						<?php if (!$this->author) { ?>
							<?php $reporter = User::getInstance($this->report->created_by); ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_REPORTED_BY'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($reporter->get('name')); ?> (<?php echo $this->escape($reporter->get('username')); ?>)</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_COMMENTS'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->escape($this->report->report); ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->report->reviewed && $this->report->reviewed != '0000-00-00 00:00:00') { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_REVIEWED'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left">@ <?php echo Date::of($this->report->reviewed)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo Date::of($this->report->reviewed)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->report->note && !$this->author) { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_NOTE'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo $this->report->note; ?></td>
							</tr>
						<?php } ?>
						<?php if ($this->author) { ?>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('COM_SUPPORT_EMAIL_DISPUTE'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em; vertical-align: top;" align="left"><?php echo Lang::txt('COM_SUPPORT_EMAIL_DISPUTE_EXPLANATION', $link, $this->report->id); ?></td>
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
		<?php if ($this->reported) {
			$this->commentor = User::getInstance($this->reported->author);
			?>
			<tr>
				<th style="text-align: left;" align="left"><?php echo $this->commentor->get('name'); ?> (<?php echo $this->commentor->get('username'); ?>)</th>
				<th class="timestamp" style="color: #999; text-align: right;" align="right"><span class="mobilehide"><?php echo Lang::txt('COM_SUPPORT_EMAIL_CREATED_AT_ON', Date::of($this->reported->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')), Date::of($this->reported->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))); ?></span></th>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="2" style="padding: 0 2em;">
					<?php
					if ($this->reported)
					{
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
						<?php
					}
					else
					{
						?>
						<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo Lang::txt('COM_SUPPORT_EMAIL_REPORTED_ITEM_NOT_FOUND'); ?></p>
						<?php
					}
					?>
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