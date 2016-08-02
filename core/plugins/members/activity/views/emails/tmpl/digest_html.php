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

$base = rtrim(Request::base(), '/');
$sef  = Route::url($this->member->link());
$link = $base . '/' . trim($sef, '/');
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
					<?php echo Lang::txt('COM_MEMBERS'); ?>
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

	<!-- Start Header -->
	<table class="tbl-message" width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
					<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_EMAIL_MEMBERS_EXPLANATION', $link, $this->member->get('name') . ' (' . $this->member->get('username') . ')'); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="20"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

<?php foreach ($this->rows as $row) { ?>
	<?php
	$border  = '#e1e1e1';
	$bground = '#f1f1f1';

	if ($row->log->get('action') == 'deleted')
	{
		$border  = '#e9e1bc';
		$bground = '#fbf1be';
	}
	if ($row->log->get('action') == 'created')
	{
		$border  = '#c8e3c2';
		$bground = '#eafbe6';
	}
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $border; ?>; background: <?php echo $bground; ?>; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
		<tbody>
			<tr>
				<td width="15%" style="padding: 8px; font-size: 1.2em; font-weight: bold; text-align: right; vertical-align: middle; padding: 8px 30px;" valign="middle" align="center">
					<?php echo $row->log->get('action'); ?>
				</td>
				<td style="font-weight: normal; padding: 8px; text-align: left; " align="left">
					<span class="activity-actor">
						<?php
						$creator = User::getInstance($row->log->get('created_by'));
						$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN'))));

						if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) { ?>
							<a href="<?php echo $base . '/' . trim(Route::url($creator->link()), '/'); ?>">
								<?php echo $name; ?>
							</a>
						<?php } else { ?>
							<?php echo $name; ?>
						<?php } ?>
					</span><br />
					<span class="activity-event">
						<?php echo str_replace('href="/', 'href="' . $base . '/', $row->log->get('description')); ?>
					</span>
				</td>
				<td width="25%" style="padding: 8px; font-size: 1.2em; text-align: right; vertical-align: middle; padding: 8px 30px;" valign="top" align="right">
					<span class="activity-time"><time datetime="<?php echo $row->get('created'); ?>"><?php
						$dt = Date::of($row->get('created'));
						$ct = Date::of('now');

						$lapsed = $ct->toUnix() - $dt->toUnix();

						if ($lapsed < 30)
						{
							echo Lang::txt('PLG_MEMBERS_ACTIVITY_JUST_NOW');
						}
						elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
						{
							echo $dt->toLocal('M j, Y');
						}
						elseif ($lapsed > 86400)
						{
							echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
						}
						else
						{
							echo $dt->relative();
						}
					?></time></span>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="10"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->
<?php } ?>

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="20"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<!-- Start Header -->
	<table class="tbl-footer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom">
					<span><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_EMAIL_WHY_NOTFIED', Config::get('sitename'), $base, $base, $base, $base); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->