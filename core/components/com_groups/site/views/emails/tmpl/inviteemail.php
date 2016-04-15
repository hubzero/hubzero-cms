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

// build urls
$base  = rtrim(str_replace('administrator', '', Request::base()), '/');

$group    = $base . '/' . ltrim(Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')), '/');
$accept   = $base . '/' . ltrim(Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&task=accept&token=' . $this->token), '/');
$register = $base . '/' . ltrim(Route::url('index.php?option=com_members&controller=register'), '/') . '?return=' . base64_encode($group);
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

	<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #e1e1e1; background: #f1f1f1; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
				<th colspan="2" style="font-weight: bold; border-bottom: 1px solid #e1e1e1; padding: 8px; text-align: left; font-style: italic;" align="left">
					<?php echo Lang::txt('Group Overview'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id="ticket-number" style="padding: 8px; font-size: 2.5em; font-weight: bold; text-align: center; padding: 8px 30px;" align="center">
					<img src="<?php echo $base . '/' . ltrim($this->group->getLogo(), '/'); ?>" width="100px" />
				</td>
				<td width="100%" style="padding: 8px;">
					<table style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Group:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php echo $this->group->get('description'); ?>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Alias:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php echo $this->group->get('cn'); ?>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Link:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<a href="<?php echo $group; ?>">
										<?php echo $group; ?>
									</a>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Invited by:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php echo $this->user->get('name'); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
		<tbody>
			<tr>
				<td width="100%" style="padding: 8px;">

					<table width="100%" style="margin: 0; border-collapse: collapse; font-size: 1em;">
						<tbody>
							<tr>
								<td style="text-align: left; padding: 1em 0.5em 0 0.5em;" align="left">
									<?php if ($this->msg) { ?>
										<p style="font-size: 1em; line-height: 1.6em; margin: 0 auto 1rem; max-width: 100%; word-break: break-word; margin-bottom: 2rem;">
											<?php echo $this->msg; ?>
										</p>
									<?php } else { ?>
										<p style="font-size: 1em; line-height: 1.6em; margin: 0 auto 1rem; text-align: center; max-width: 100%; word-break: break-word; margin-bottom: 2rem;">
											<?php echo Lang::txt('You\'ve been invited to the "%s" group!', $this->group->get('description')); ?>
										</p>
									<?php } ?>
									<div style="text-align: center; margin: 2rem 0 1rem;">
										<table cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #439fe0; border-bottom: 2px solid #3286b1; padding: 1em 2em; display: inline-block;">
											<tr>
												<td style="border-collapse: collapse;">
													<a target="_blank" style="color: white; font-weight: normal; text-decoration: none; word-break: break-word; display: inline-block; letter-spacing: 1px; font-size: 1.5em; text-shadow: 0 1px 1px rgba(0,0,0,0.25);" align="center" href="<?php echo $accept; ?>"><?php echo Lang::txt('Accept Invite'); ?></a>
												</td>
											</tr>
										</table>
										<p style="font-size: 1em; line-height: 1.6em; margin: 1rem auto 1rem; color: #AAA; text-align: center; max-width: 100%; word-break: break-word; margin-bottom: 2rem;">
											<?php echo Lang::txt('You may copy/paste this link into your browser:'); ?><br />
											<a href="<?php echo $accept; ?>" style="font-weight: bold; text-decoration: none; word-break: break-word;"><?php echo $accept; ?></a>
										</p>
									</div>
									<div style="text-align: center; margin: 2rem 0 1rem;">
										<p style="font-size: 1em; line-height: 1.6em; margin: 0 auto 1rem; color: #AAA; text-align: center; max-width: 100%; word-break: break-word; margin-bottom: 2rem;">
											<?php echo Lang::txt('Don\'t already have an account? Register one here:'); ?><br />
											<a href="<?php echo $register; ?>" style="font-weight: bold; text-decoration: none; word-break: break-word;"><?php echo $register; ?></a>
										</p>
									</div>
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
					<span><?php echo Config::get('sitename'); ?> sent this email because you are a group manager for this group. Visit our <a href="<?php echo rtrim($base, '/'); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($base, '/'); ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->
