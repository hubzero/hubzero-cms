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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Build link to wish
$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');
$link = $base . '/' . ltrim(Route::url($this->wish->link()), '/');

$this->css(
	'@media only screen and (max-device-width: 480px) {
		#wish-number {
			float: none !important;
			width: auto !important;
		}
		table#wish-comments>tbody>tr>td {
			padding: 0 !important;
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
					<?php echo Lang::txt('COM_WISHLIST'); ?>
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

	<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #c8e3c2; background: #eafbe6; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
		<thead class="mobilehide">
			<tr>
				<th style="font-weight: normal; border-bottom: 1px solid #c8e3c2; padding: 8px; text-align: left" align="left">
					<?php echo $this->escape($this->wish->get('subject')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="100%" style="padding: 8px;">
					<div id="wish-number" class="mobilehide" style="float: left; width: 5em; font-size: 2.5em; font-weight: bold; text-align: center; padding: 30px;" align="center">
						<?php /* &#x1f4a1; */ ?>&#x2736;
					</div>
					<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Wish:</th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"># <?php echo $this->wish->get('id'); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">@ <?php echo $this->wish->proposed('time'); ?> on <?php echo $this->wish->proposed('date'); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Creator:</th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->wish->get('anonymous') ? Lang::txt('COM_WISHLIST_ANONYMOUS') : $this->escape(stripslashes($this->wish->proposer()->get('name'))); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Status:</th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->wish->status('text'); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Tags:</th>
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->escape($this->wish->tags('string')); ?></td>
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

	<table width="100%" id="wish-comments" style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<th style="text-align: left;" align="left"><?php echo ($this->comment->get('anonymous') ? Lang::txt('COM_WISHLIST_UNKNOWN') : $this->escape($this->comment->creator->get('name'))); ?></th>
				<th class="timestamp" style="color: #999; text-align: right;" align="right"><span class="mobilehide"><?php echo Lang::txt('@%s on %s', $this->comment->created('time'), $this->comment->created('date')); ?></span></th>
			</tr>
			<tr>
				<td colspan="2" style="padding: 0 2em;">
					<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->comment->content('parsed'); ?></div>
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
					<span><?php echo Config::get('sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $link; ?>">wish #<?php echo $this->wish->get('id'); ?></a>. Visit our <a href="<?php echo rtrim(Request::base(), '/'); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim(Request::base(), '/'); ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Footer -->