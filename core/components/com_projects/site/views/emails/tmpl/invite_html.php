<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');

$sef  = 'projects/' . $this->project->get('alias');

$projectUrl  = rtrim($base, '/') . '/' . trim($sef, '/');
$sef 		.= $this->uid ? '' : '/?confirm=' . $this->code . '&email=' . $this->email;
$link        = rtrim($base, '/') . '/' . trim($sef, '/');

// Main message
if ($this->uid == $this->project->get('created_by_user') && !$this->project->isProvisioned())
{
	$subtitle  = Lang::txt('COM_PROJECTS_EMAIL_CREATOR_NEW_PROJECT');
}
elseif ($this->project->isProvisioned())
{
	$pub = isset($this->pub) ? $this->pub : $this->project->getPublication();
	$subtitle = User::get('id')
			? Lang::txt('PLG_PROJECTS_TEAM_EMAIL_ADDED_AS_PUB_AUTHOR')
			: Lang::txt('PLG_PROJECTS_TEAM_EMAIL_INVITED_AS_PUB_AUTHOR');
	$subtitle .= ' "' . $pub->title . '"';
}
else
{
	$subtitle  = User::get('name') . ' ';//$this->project->owner('name') . ' ';
	$subtitle .= $this->uid ? Lang::txt('COM_PROJECTS_EMAIL_ADDED_YOU') : Lang::txt('COM_PROJECTS_EMAIL_INVITED_YOU');
	$subtitle .= ' "' . $this->project->get('title') . '" ' . Lang::txt('COM_PROJECTS_EMAIL_IN_THE_ROLE') . ' ';
	if ($this->role == 1)
	{
		$subtitle .= Lang::txt('COM_PROJECTS_LABEL_OWNER');
	}
	elseif ($this->role == 5)
	{
		$subtitle .= Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
	}
	else
	{
		$subtitle .= Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
	}
}

// Get the actual message
$comment = '';
if ($this->uid)
{
	$comment .= $this->project->isProvisioned()
			? Lang::txt('COM_PROJECTS_EMAIL_ACCESS_PUB_PROJECT') . "\n"
			: Lang::txt('COM_PROJECTS_EMAIL_ACCESS_PROJECT') . "\n";
}
else
{
	$comment .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT') . ' ' . Config::get('sitename') . ' ';
	$comment .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT') . "\n";
}

$comment .= $link . "\n\n";

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
				<?php echo $subtitle; ?>
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
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->project->get('title') . '(' . $this->project->get('alias') . ')'; ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">@ <?php echo $this->project->created('time'); ?> on <?php echo $this->project->created('date'); ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Owner:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->project->groupOwner() ? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP') : $this->project->owner('name'); ?></td>
						</tr>
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
							<?php
								if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">'))
								{
									$comment = str_replace("<br />", '', $comment);
									$comment = $this->escape($comment);
									$comment = nl2br($comment);
									$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
									$comment = preg_replace('/  /', ' &nbsp;', $comment);
								}
							?>
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

<!-- Start Footer -->
<table class="tbl-footer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td align="left" valign="bottom">
				<span><?php echo Config::get('sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>. Visit our <a href="<?php echo Request::base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo Request::base(); ?>/support">Support Center</a> if you have any questions.</span>
			</td>
		</tr>
	</tbody>
</table>
<!-- End Footer -->
