<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Set some styles
$ulStyle = 'list-style: none; font-size: 0.9em; margin: 0.5em 0; line-height: 1.6em; text-align: left;';

$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');

$sef 		= Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias'));

$link 		= rtrim($base, DS) . DS . trim($sef, DS);
$projectUrl = $link;

// Page title
$title = Config::get('sitename') . ' ' . Lang::txt('COM_PROJECTS_PROJECTS');

// Get the actual message
$comment = $this->message;

// Parse admin comment
if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">'))
{
	$comment = str_replace("<br />", '', $comment);
	$comment = $this->escape($comment);
	$comment = nl2br($comment);
	$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
	$comment = preg_replace('/  /', ' &nbsp;', $comment);
}

if ($comment)
{
	$comment = '<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;">' . $comment . '</p>';
}

// Project owner
if ($this->project->isProvisioned())
{
	$owner = null;
}
else
{
	$owner = $this->project->groupOwner()
			? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
			: $this->project->owner('name');
}

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
					<a href="<?php echo $base; ?>"><?php echo $base; ?></a>
				</span>
				<br />
				<span class="description"><?php echo Config::get('MetaDesc'); ?></span>
			</td>
			<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
				<?php echo Lang::txt('COM_PROJECTS_PROJECTS'); ?>
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
			<?php echo Lang::txt('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS'); ?>
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

<table id="project-info" width="100%"  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; line-height: 1.6em;">
	<tbody>
		<tr>
			<td class="mobilehide" style="font-size: 2.5em; font-weight: bold; text-align: center; padding: 0 30px 8px 0; vertical-align: top;" align="center" valing="top">
			</td>
			<td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9;">
				<table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Project:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->project->get('title'); ?> (<?php echo $this->project->get('alias'); ?> <?php echo $this->project->isProvisioned() ? ' - ' . Lang::txt('COM_PROJECTS_PROVISIONED') : ''; ?>)</td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">@ <?php echo $this->project->created('time'); ?> on <?php echo $this->project->created('date'); ?></td>
						</tr>
						<?php if ($owner) { ?>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Owner:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $owner; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Link:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><a href="<?php echo $link; ?>"><?php echo $projectUrl; ?></a></td>
						</tr>
					</tbody>
				</table>

				<table width="100%" style="margin: 18px 0 0 0; border-top: 2px solid #e9e9e9; border-collapse: collapse; font-size: 1em;">
					<tbody>
						<tr>
							<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
								<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $comment; ?></div>
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
