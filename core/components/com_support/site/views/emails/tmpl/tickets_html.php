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

// No direct access.
defined('_HZEXEC_') or die();

$bdcolor = array(
	'critical' => '#e9bcbc',
	'major'    => '#e9e1bc',
	'normal'   => '#e1e1e1',
	'minor'    => '#bccbe9',
	'trivial'  => '#bce1e9'
);
$bgcolor = array(
	'critical' => '#ffd3d4',
	'major'    => '#fbf1be',
	'normal'   => '#f1f1f1',
	'minor'    => '#d3e3ff',
	'trivial'  => '#d3f9ff'
);
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=';
$site = rtrim(Request::base(), '/');
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

	<!-- Start Header -->
	<table class="tbl-message" width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
					Below is a list of support tickets currently assigned to you.
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

	<?php
	if (isset($this->tickets['critical']))
	{
		foreach ($this->tickets['critical'] as $ticket)
		{
			if (!$ticket->summary)
			{
				$ticket->summary = substr($ticket->report, 0, 70);
				if (strlen($ticket->summary) >= 70)
				{
					$ticket->summary .= '...';
				}
				if (!trim($ticket->summary))
				{
					$ticket->summary = Lang::txt('(no content found)');
				}
			}
			$ticket->summary = str_replace("\r", "", $ticket->summary);
			$ticket->summary = str_replace("\t", " ", $ticket->summary);
			$ticket->summary = str_replace("\n", " ", $ticket->summary);

			$sef = Route::url($base . $ticket->id);
			if (substr($site, -13) == 'administrator')
			{
				$sef = 'support/ticket/' . $ticket->id;
			}
			$link = $site . '/' . trim($sef, '/');
			$link = str_replace('/administrator', '', $link);

			$st = new \Components\Support\Models\Tags($ticket->id);
			$tags = $st->render('string');
			?>
			<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bdcolor['critical']; ?>; background: <?php echo $bgcolor['critical']; ?>; font-size: 0.9em; line-height: 1.6em;
				background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent), color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)), color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent), to(transparent));
				background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				-webkit-background-size: 30px 30px;
				-moz-background-size: 30px 30px;
				background-size: 30px 30px;">
				<thead>
					<tr>
						<th colspan="2" style="font-weight: normal; border-bottom: 1px solid <?php echo $bdcolor['critical']; ?>; padding: 8px; text-align: left" align="left">
							<?php echo $this->escape($ticket->summary); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="25%" style="padding: 8px; font-size: 2em; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 30px;" valign="middle" align="center">
							#<?php echo $ticket->id; ?>
						</td>
						<td width="75%" style="padding: 8px;">
							<table style="border-collapse: collapse;" width="100%" cellpadding="0" cellspacing="0" border="0">
								<tbody>
								<?php if (!$this->config->get('email_terse')) { ?>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->created; ?></td>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Last activity:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo '--'; ?></td>
									</tr>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Creator:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->name ? $ticket->name : 'Unknown'; ?> <?php echo $ticket->login ? '(' . $ticket->login . ')' : ''; ?></td>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Severity:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->severity; ?></td>
									</tr>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Tags:</th>
										<td colspan="3" style="text-align: left; padding: 0 0.5em; vertical-align: top;" valign="top" align="left"><?php echo ($tags ? $tags : '--'); ?></td>
									</tr>
								<?php } ?>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Link:</th>
										<td colspan="3" style="text-align: left; padding: 0 0.5em; vertical-align: top;" valign="top" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
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
			<?php
		}
	}

	if (isset($this->tickets['major']))
	{
		foreach ($this->tickets['major'] as $ticket)
		{
			if (!$ticket->summary)
			{
				$ticket->summary = substr($ticket->report, 0, 70);
				if (strlen($ticket->summary) >= 70)
				{
					$ticket->summary .= '...';
				}
				if (!trim($ticket->summary))
				{
					$ticket->summary = Lang::txt('(no content found)');
				}
			}
			$ticket->summary = str_replace("\r", "", $ticket->summary);
			$ticket->summary = str_replace("\t", " ", $ticket->summary);
			$ticket->summary = str_replace("\n", " ", $ticket->summary);

			$sef = Route::url($base . $ticket->id);
			if (substr($site, -13) == 'administrator')
			{
				$sef = 'support/ticket/' . $ticket->id;
			}
			$link = $site . '/' . trim($sef, '/');
			$link = str_replace('/administrator', '', $link);

			$st = new \Components\Support\Models\Tags($ticket->id);
			$tags = $st->render('string');
			?>
			<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bdcolor['major']; ?>; background: <?php echo $bgcolor['major']; ?>; font-size: 0.9em; line-height: 1.6em;
				background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent), color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)), color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent), to(transparent));
				background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				-webkit-background-size: 30px 30px;
				-moz-background-size: 30px 30px;
				background-size: 30px 30px;">
				<thead>
					<tr>
						<th colspan="2" style="font-weight: normal; border-bottom: 1px solid <?php echo $bdcolor['major']; ?>; padding: 8px; text-align: left" align="left">
							<?php echo $this->escape($ticket->summary); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="25%" style="padding: 8px; font-size: 2em; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 30px;" valign="middle" align="center">
							#<?php echo $ticket->id; ?>
						</td>
						<td width="75%" style="padding: 8px;">
							<table style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
								<tbody>
								<?php if (!$this->config->get('email_terse')) { ?>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->created; ?></td>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Last activity:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo '0000-00-00 00:00:00'; ?></td>
									</tr>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Creator:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->name ? $ticket->name : 'Unknown'; ?> <?php echo $ticket->login ? '(' . $ticket->login . ')' : ''; ?></td>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Severity:</th>
										<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $ticket->severity; ?></td>
									</tr>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Tags:</th>
										<td colspan="3" style="text-align: left; padding: 0 0.5em; vertical-align: top;" valign="top" align="left"><?php echo ($tags ? $tags : '--'); ?></td>
									</tr>
								<?php } ?>
									<tr>
										<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Link:</th>
										<td colspan="3" style="text-align: left; padding: 0 0.5em; vertical-align: top;" valign="top" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
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
			<?php
		}
	}

	if ((isset($this->tickets['critical']) && count($this->tickets['critical']) > 0)
	 || (isset($this->tickets['major']) && count($this->tickets['major']) > 0))
	{
		?>
			<!-- Start Spacer -->
			<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody>
					<tr>
						<td height="30"></td>
					</tr>
				</tbody>
			</table>
			<!-- End Spacer -->
		<?php
	}

	$more = 0;
	//if (isset($this->tickets['normal']))
	//{
	$i = 0;
	foreach ($this->tickets as $severity => $tickets)
	{
		if ($severity == 'critical' || $severity == 'major')
		{
			continue;
		}
		// Add the ticket count to the total
		$more += count($tickets);
		if ($i >= 5)
		{
			continue;
		}

		$k = 0;
		foreach ($tickets as $ticket)
		{
			if ($k >= 10)
			{
				break;
			}
			if (!$ticket->summary)
			{
				$ticket->summary = substr($ticket->report, 0, 70);
				if (strlen($ticket->summary) >= 70)
				{
					$ticket->summary .= '...';
				}
				if (!trim($ticket->summary))
				{
					$ticket->summary = Lang::txt('(no content found)');
				}
			}
			$ticket->summary = str_replace("\r", "", $ticket->summary);
			$ticket->summary = str_replace("\t", " ", $ticket->summary);
			$ticket->summary = str_replace("\n", " ", $ticket->summary);

			$sef = Route::url($base . $ticket->id);
			if (substr($site, -13) == 'administrator')
			{
				$sef = 'support/ticket/' . $ticket->id;
			}
			$link = $site . '/' . trim($sef, '/');
			$link = str_replace('/administrator', '', $link);
			?>
			<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bdcolor[$severity]; ?>; background: <?php echo $bgcolor[$severity]; ?>; font-size: 0.9em; line-height: 1.6em;
				background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent), color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)), color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent), to(transparent));
				background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%, transparent 75%, transparent);
				-webkit-background-size: 30px 30px;
				-moz-background-size: 30px 30px;
				background-size: 30px 30px;">
				<tbody>
					<tr>
						<td width="25%" rowspan="2" style="padding: 8px; font-size: 2em; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 30px;" valign="middle" align="center">
							#<?php echo $ticket->id; ?>
						</td>
						<td width="75%" colspan="2" style="font-weight: normal; padding: 8px 8px 0 8px; text-align: left;" align="left">
							<?php echo (!$this->config->get('email_terse') ? $this->escape($ticket->summary) : Lang::txt('COM_SUPPORT_TICKET')); ?>
						</td>
					</tr>
					<tr>
						<th style="font-weight: normal; padding: 0 8px 8px 8px; text-align: left; font-weight: bold;" align="left">Link:</th>
						<td style="font-weight: normal; padding: 0 8px 8px 8px; text-align: left;" width="100%" align="left">
							<a href="<?php echo $link; ?>"><?php echo $link; ?></a>
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
			<?php
			$i++;
			$k++;
			// Subtract one from total for each ticket passed
			$more--;
		}
	}
	?>

	<?php if ($more) { ?>
		<!-- Start More -->
		<table width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td align="left" valign="bottom" style="line-height: 1; padding: 5px;">
						... and <b><?php echo $more; ?></b> more open tickets.
					</td>
				</tr>
			</tbody>
		</table>
		<!-- End More -->
	<?php } ?>

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