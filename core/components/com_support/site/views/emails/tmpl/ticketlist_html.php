<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

	<table id="ticket-info" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #fff; background: #fff; font-size: 0.9em; line-height: 1.6em;">
		<thead>
			<tr>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap;" align="left">Number</th>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap; border-left: 1px solid #fff;" align="left">Issue</th>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap; border-left: 1px solid #fff;" align="left">Created</th>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap; border-left: 1px solid #fff;" align="left">Creator</th>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap; border-left: 1px solid #fff;" align="left">Assigned</th>
				<th style="text-align: left; padding: 0.7em; font-weight: bold; white-space: nowrap; border-left: 1px solid #fff;" align="left">Severity</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (isset($this->tickets))
		{
			foreach ($this->tickets as $ticket)
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
				$link = rtrim(Request::base(), '/') . '/' . trim($sef, '/');
				?>
				<tr style="background: <?php echo $bgcolor[$ticket->severity]; ?>; border: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>">
					<td style="text-align: left; padding: 0.7em;" valign="top" align="left"><a href="<?php echo $link; ?>">#<?php echo $ticket->id; ?></a></td>
					<td style="text-align: left; padding: 0.7em; border-left: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>;" align="left"><?php echo $this->escape($ticket->summary); ?></td>
					<td style="text-align: left; padding: 0.7em; white-space: nowrap; border-left: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>;" align="left"><?php echo $this->escape($ticket->created); ?></td>
					<td style="text-align: left; padding: 0.7em; white-space: nowrap; border-left: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>;" align="left"><?php echo $ticket->name ? $this->escape($ticket->name) : 'Unknown'; ?> <?php echo $ticket->login ? '(' . $this->escape($ticket->login) . ')' : ''; ?></td>
					<td style="text-align: left; padding: 0.7em; white-space: nowrap; border-left: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>;" align="left"><?php echo $ticket->owner ? $this->escape($ticket->owner_name) : '--'; ?> <?php echo $ticket->owner ? '(' . $this->escape($ticket->owner) . ')' : ''; ?></td>
					<td style="text-align: left; padding: 0.7em; white-space: nowrap; border-left: 1px solid <?php echo $bdcolor[$ticket->severity]; ?>;" align="left"><?php echo $this->escape($ticket->severity); ?></td>
				</tr>
				<?php
			}
		}
		?>

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
					<span><?php echo Config::get('sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo request::base(); ?>"><?php echo Request::base(); ?></a>. Visit our <a href="<?php echo Request::base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo Request::base(); ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Footer -->