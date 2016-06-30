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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$sef  = Route::url($this->thread->link());
$link = $base . '/' . trim($sef, '/');

$bgcolor = '#f1f1f1';
$bdcolor = '#e1e1e1';
?>
<?php if ($this->delimiter) { ?>
	<!-- Start Header Spacer -->
	<table class="tbl-delimiter" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px dashed #b5c6b5;">
		<tbody>
			<tr>
				<td height="30" style="border-collapse: collapse; color: #9bac9b;">
					<div style="height: 0px; overflow: hidden; color: #fff; visibility: hidden;"><?php echo $this->delimiter; ?></div>
					<div style="text-align: center; font-size: 90%; display: block; padding: 1em;"><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_REPLY_ABOVE'); ?></div>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header Spacer -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->
<?php } ?>
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
					<?php echo Lang::txt('COM_GROUPS'); ?>
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

	<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bdcolor; ?>; background: <?php echo $bgcolor; ?>; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
		<?php /*<thead class="mobilehide">
			<tr>
				<th style="font-weight: normal; border-bottom: 1px solid <?php echo $bdcolor; ?>; padding: 8px; text-align: left" align="left">
					<?php echo $this->escape($this->thread->get('title')); ?>
				</th>
			</tr>
		</thead> */?>
		<tbody>
			<tr>
				<td width="100%" style="padding: 8px;">
					<div class="mobilehide" id="ticket-number" style="float: left; width: 1.2em; font-size: 4em; font-weight: bold; text-align: center; padding: 30px;" align="center">
						&#8220;
					</div>
					<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->thread->get('title')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_CREATED'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo Lang::txt('PLG_GROUPS_FORUM_CREATED', $this->thread->created('time'), $this->thread->created('date')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_GROUP'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->group->get('description')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_SECTION'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->section->get('title')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_CATEGORY'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->category->get('title')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right"><?php echo Lang::txt('PLG_GROUPS_FORUM_DETAILS_LINK'); ?>:</th>
								<td style="text-align: left; padding: 0 0.5em;" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table width="100%" id="ticket-comments" style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<th style="text-align: left;" align="left"><?php echo (!$this->post->get('anonymous') ? $this->post->creator('name') : Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS')); ?></th>
				<th class="timestamp" style="color: #999; text-align: right;" align="right"><span class="mobilehide"><?php echo Lang::txt('PLG_GROUPS_FORUM_CREATED', $this->post->created('time'), $this->post->created('date')); ?></span></th>
			</tr>
			<tr>
				<td colspan="2" style="padding: 0 2em;">
					<div style="line-height: 1.6em; margin: 0; padding: 0; text-align: left;"><?php echo $this->post->content('parsed'); ?></div>
					<?php /*if ($this->post->attachments()->total()) { ?>
						<div class="comment-attachments" style="margin: 2em 0 0 0; padding: 0; text-align: left;">
							<?php
							foreach ($this->post->attachments() as $attachment)
							{
								if (!trim($attachment->get('description')))
								{
									$attachment->set('description', $attachment->get('filename'));
								}
								echo '<p class="attachment" style="margin: 0.5em 0; padding: 0; text-align: left;"><a class="' . ($attachment->isImage() ? 'img' : 'file') . '" data-filename="' . $attachment->get('filename') . '" href="' . $base . '/' . ltrim(Route::url($attachment->link()), '/') . '">' . $attachment->get('description') . '</a></p>';
							}
							?>
						</div><!-- / .comment-body -->
					<?php }*/ ?>
				</td>
			</tr>
			<?php if ($this->unsubscribe) { ?>
				<tr>
					<td colspan="2" style="padding: 2em 0 0 0; font-size: 0.9em;">
						<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_UNSUBSCRIBE'); ?>:<br /><a href="<?php echo $this->get('unsubscribe'); ?>"><?php echo $this->get('unsubscribe'); ?></a>
					</td>
				</tr>
			<?php } ?>
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

	<!-- Start Header -->
	<table class="tbl-footer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom">
					<span><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_WHY_NOTFIED', Config::get('sitename'), $link, Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD_TITLE', $this->thread->get('id')), $base, $base); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->