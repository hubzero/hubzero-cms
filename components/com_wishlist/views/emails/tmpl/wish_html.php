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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();

// Build link to wish
$base = rtrim($juri->base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');
$link = $base . '/' . ltrim(JRoute::_($this->wish->link()), '/');
?>
	<!-- Start Header -->
	<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
					<?php echo $jconfig->getValue('config.sitename'); ?>
				</td>
				<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
					<span class="home">
						<a href="<?php echo $juri->base(); ?>"><?php echo $juri->base(); ?></a>
					</span>
					<br />
					<span class="description"><?php echo $jconfig->getValue('config.MetaDesc'); ?></span>
				</td>
				<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
					<?php echo JText::_('COM_WISHLIST'); ?>
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
	<table width="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
					<?php
					switch ($this->action)
					{
						case 'assigned':
							echo 'The following wish has been assigned to ' . $this->escape(stripslashes($this->wish->owner('name'))) . '.';
						break;

						case 'created':
							echo 'A new wish has been submitted by ' . $this->escape(stripslashes($this->wish->proposer('name')));
						break;

						case 'moved':
							echo 'Wish #' . $this->escape($this->wish->get('id')) . 'has been moved to a new list.';
						break;

						case 'updated':
							echo 'Wish #' . $this->escape($this->wish->get('id')) . 'has been updated.';
						break;
					}
					?>
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
					<p style="display: block; border: 1px solid #c8e3c2; background: #eafbe6; margin:0; padding: 1em;"><?php /* &#x1f4a1; */ ?>&#x2736;</p>
				</td>
				<td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9;">
					<table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
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
								<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->wish->get('anonymous') ? JText::_('COM_WISHLIST_ANONYMOUS') : $this->escape(stripslashes($this->wish->proposer('name'))); ?></td>
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

					<table width="100%" style="margin: 18px 0 0 0; border-top: 2px solid #e9e9e9; border-collapse: collapse; font-size: 1em;">
						<tbody>
							<tr>
								<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
									<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->wish->get('subject'); ?></div>
								</td>
							</tr>
						<?php
						switch ($this->action)
						{
							case 'assigned':
							case 'created':
								?>
								<tr>
									<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
										<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->wish->content('parsed'); ?></div>
									</td>
								</tr>
								<?php
							break;
							case 'moved':
								?>
								<tr>
									<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
										<?php echo JText::sprintf('Wish <span style="color: #4e7ac7;">moved</span> from %s to %s', '<b style="color: #333;">' . $this->escape($this->oldlist->get('title')) . '</b>', '<b style="color: #333;">' . $this->escape($this->wishlist->get('title')) . '</b>'); ?>
									</td>
								</tr>
								<?php
							break;
							case 'updated':
								?>
								<tr>
									<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
										<?php echo JText::sprintf('Wish <span style="color: #4e7ac7;">status</span> changed to %s', '<b style="color: #333;">' . $this->status . '</b>'); ?>
									</td>
								</tr>
								<?php
							break;
						}
						?>
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
					<span><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $link; ?>">wish #<?php echo $this->wish->get('id'); ?></a>. Visit our <a href="<?php echo rtrim($juri->base(), '/'); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($juri->base(), '/'); ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->
