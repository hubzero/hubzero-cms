<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->question->isOpen() && !$this->question->isReported())
{
	$status = 'open';
}
else if ($this->question->isReported())
{
	$status = 'underreview';
}
else
{
	$status = 'closed';
}

$link = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->question->link()), '/');
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
					<?php echo Lang::txt('Questions &amp; Answers'); ?>
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

	<!-- Start Message -->
	<table class="tbl-message" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
					<?php echo Lang::txt('A question has been posted by %s.', $this->question->get('anonymous') ? Lang::txt('JANONYMOUS') : $this->escape(stripslashes($this->question->creator->get('name')))); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Message -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<table id="question-info" width="100%"  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; line-height: 1.6em;">
		<tbody>
			<tr>
				<td class="mobilehide" style="font-size: 2.5em; font-weight: bold; text-align: center; padding: 0 30px 8px 0; vertical-align: top;" align="center" valing="top">
					<p style="display: block; border: 1px solid #c8e3c2; background: #eafbe6; margin:0; padding: 1em;">?</p>
				</td>
				<td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9;">
					<table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('Question:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"># <?php echo $this->question->get('id'); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('Created:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">@ <?php echo $this->question->created('time'); ?> on <?php echo $this->question->created('date'); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('Creator:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->question->get('anonymous') ? Lang::txt('JANONYMOUS') : $this->escape(stripslashes($this->question->creator->get('name'))); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('Status:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $status ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Tags:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->escape($this->question->tags('string')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('Link:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
							</tr>
						</tbody>
					</table>

					<table width="100%" style="margin: 18px 0 0 0; border-top: 2px solid #e9e9e9; border-collapse: collapse; font-size: 1em;">
						<tbody>
							<tr>
								<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
									<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->question->subject; ?></div>
								</td>
							</tr>
							<tr>
								<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
									<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->question->question; ?></div>
								</td>
							</tr>
						</tbody>
					</table>
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
