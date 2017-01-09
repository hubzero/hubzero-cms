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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$sef  = Route::url($this->project->link('alias'));
$link = rtrim($base, '/') . '/' . trim($sef, '/');

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
				<?php echo Lang::txt('Projects'); ?>
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
			<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center; font-size: 1.5em;" colspan="2">
			<?php echo $this->subject; ?>
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

<table id="project-info" width="100%"  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; line-height: 1.6em; background-color: #fff7eb;">
	<tbody>
		<tr>
			<td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9; border-bottom: 2px solid #e9e9e9;">
				<table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Project:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->project->get('title') . ' (' . $this->project->get('alias') . ')'; ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Link:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
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

<table id="project-info" width="100%"  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; line-height: 1.6em;">
	<tbody>
		<tr>
			<td width="100%" style="padding: 18px 8px 8px 8px;">
				<table width="100%" style="border-collapse: collapse; font-size: 1.2em;" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<?php if (empty($this->activities)) { ?>
							<tr>
								<td style="text-align: center; padding: 0 1em;" width="100%" align="left">There has been no activity in this project.</td>
							</tr>
						<?php } else { ?>
							<?php
							foreach ($this->activities as $a)
							{
								$body     = NULL;
								$activity = $a->activity;

								switch ($a->class)
								{
									case 'quote':
										// Get comment
										$objC  = $this->project->table('Comment');
										if ($objC->loadComment($a->referenceid))
										{
											$body = stripslashes($objC->comment);
											$body = str_replace('<!-- {FORMAT:HTML} -->', '', $body);
										}
										break;

									case 'blog':
										$activity = 'posted a status update';
										$objM     = $this->project->table('Blog');
										$blog     = $objM->getEntries(
											$a->projectid,
											$bfilters = array('activityid' => $a->id),
											$a->referenceid
										);
										$body = $blog && !empty($blog[0]) ? trim($blog[0]->blogentry) : '';
										if (!preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $body))
										{
											$body = preg_replace("/\n/", '<br />', $body);
										}
										break;

									case 'todo':
										$objTD = $this->project->table('Todo');
										$todo = $objTD->getTodos(
											$a->projectid,
											$tfilters = array('activityid' => $a->id),
											$a->referenceid
										);
										if ($todo && !empty($todo[0]))
										{
											$body = $todo[0]->details ? $todo[0]->details : $todo[0]->content;
										}
										break;
								}
								// Display hyperlink
								if ($a->highlighted && $a->url)
								{
									$activity = str_replace($a->highlighted, '<a href="' . $base . $a->url . '">' . $a->highlighted . '</a>', $activity);
								}
								?>
								<tr>
									<th style="text-align: right; padding: 1em 0 0 0; white-space: nowrap; color: #999999; font-weight: normal;" align="right"><?php echo Date::of($a->recorded)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?> &#64; <?php echo Date::of($a->recorded)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></th>
									<td style="text-align: left; ?>; padding: 1em 3em 0 3em;" width="100%" align="left"><span style="color: #8a7460;"><?php echo $a->admin == 1 ? Lang::txt('Administrator') : $a->name; ?></span> <?php echo $activity; ?><?php if ($body) { ?>:<?php } ?></td>
								</tr>
								<?php if ($body) { ?>
									<tr>
										<th></th>
										<td style="text-align: <?php echo count($this->activities) > 0 ? 'left' : 'center'; ?>; padding: 0.5em 3em; color: #000000;" width="100%" align="left"><?php echo $body; ?></td>
									</tr>
								<?php } ?>
								<tr>
									<th style="text-align: center; padding: 0.5em 1em; <?php if (count($this->activities) > 0) echo 'border-bottom: 1px solid #e9e9e9;'; ?>"></th>
									<td style="text-align: center; padding: 0.5em 1em; <?php if (count($this->activities) > 0) echo 'border-bottom: 1px solid #e9e9e9;'; ?>" width="100%" align="left"></td>
								</tr>
								<?php
							}
							?>
						<?php } ?>
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
					<span>
						This email was sent to you on behalf of <?php echo $base; ?> because you are subscribed
						to watch this project. To unsubscribe, please go to the <a href="<?php echo $link; ?>">project feed</a> and adjust feed notification settings. Visit our <a href="<?php echo Request::base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo Request::base(); ?>/support">Support Center</a> if you have any questions.</span>
			</td>
		</tr>
	</tbody>
</table>
<!-- End Footer -->
