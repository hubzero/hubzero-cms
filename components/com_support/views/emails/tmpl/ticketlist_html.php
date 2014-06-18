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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Support Center</title>
		<style type="text/css">
		/* Client-specific Styles */
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: #ffffff !important; margin: 0 !important; padding: 0 !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
		.ExternalClass { width:100%; } /* Force Hotmail to display emails at full width */
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; } /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
		#backgroundTable { margin:0; padding:0; width:100% !important; line-height: 100% !important; }
		/* End reset */

		/* Some sensible defaults for images
		1. "-ms-interpolation-mode: bicubic" works to help ie properly resize images in IE. (if you are resizing them using the width and height attributes)
		2. "border:none" removes border when linking images.
		3. Updated the common Gmail/Hotmail image display fix: Gmail and Hotmail unwantedly adds in an extra space below images when using non IE browsers. You may not always want all of your images to be block elements. Apply the "image_fix" class to any image you need to fix.

		Bring inline: Yes.
		*/
		img { outline: none !important; text-decoration: none !important; -ms-interpolation-mode: bicubic; }
		a img { border: none; }
		.image_fix { display: block !important; }

		/* Yahoo paragraph fix: removes the proper spacing or the paragraph (p) tag. To correct we set the top/bottom margin to 1em in the head of the document. */
		p { margin: 1em 0; }

		/* Outlook 07, 10 Padding issue */
		table td { border-collapse: collapse; }

		/* Remove spacing around Outlook 07, 10 tables */
		table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }

		@media only screen and (max-device-width: 480px) {
			/*body { -webkit-text-size-adjust: 140% !important; }*/
			/* Step 1: Reset colors */
			a[href^="tel"], a[href^="sms"] {
				text-decoration: none;
				color: #333; /* or whatever your want */
				pointer-events: none;
				cursor: default;
			}
			/* Step 2: Set colors for inteded items */
			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				text-decoration: default;
				color: #0fa1ca !important;
				pointer-events: auto;
				cursor: default;
			}
		}
		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
			/* tablets, smaller screens, etc */
			/* Step 1a: Repeating for the iPad */
			a[href^="tel"], a[href^="sms"] {
				text-decoration: none;
				color: #333;
				pointer-events: none;
				cursor: default;
			}
			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				text-decoration: default;
				color: #0fa1ca !important;
				pointer-events: auto;
				cursor: default;
			}
		}
		</style>

		<!--[if IEMobile 7]>
		<style type="text/css">
		/* Targeting Windows Mobile */
		</style>
		<![endif]-->

		<!--[if gte mso 9]>
		<style type="text/css" >
		/* Outlook 2007/10 List Fix */
		.article-content ol, .article-content ul {
		  margin: 0 0 0 24px;
		  padding: 0;
		  list-style-position: inside;
		}
		</style>
		<![endif]-->
	</head>
	<body style="width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif; font-size: 12px; -webkit-text-size-adjust: none; color: #616161; line-height: 1.4em; color: #666; background: #fff; text-rendering: optimizeLegibility;" bgcolor="#ffffff">

		<!-- ====== Start Body Wrapper Table ====== -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="background-table" style="background-color: #ffffff; min-width: 100%;" bgcolor="#ffffff">
			<tbody>
				<tr style="border-collapse: collapse;">
					<td bgcolor="#ffffff" align="center" style="border-collapse: collapse;">

						<!-- ====== Start Content Wrapper Table ====== -->
						<table width="670" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
							<tbody>
								<tr style="border-collapse: collapse;">
									<td bgcolor="#ffffff" width="10" style="border-collapse: collapse;"></td>
									<td bgcolor="#ffffff" width="650" align="left" style="border-collapse: collapse;">

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<!-- ====== Start Header ====== -->
										<table cellpadding="2" cellspacing="3" border="0" width="100%" style="border-collapse: collapse; border-bottom: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td width="10%" nowrap="nowrap" align="left" valign="bottom" style="font-size: 1.4em; color: #999; padding: 0 10px 5px 0; text-align: left;">
														<?php echo $jconfig->getValue('config.sitename'); ?>
													</td>
													<td width="80%" align="left" valign="bottom" style="line-height: 1; padding: 0 0 5px 10px;">
														<span style="font-weight: bold; font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;">
															<a href="<?php echo $juri->base(); ?>" style="color: #666; font-weight: bold; text-decoration: none; border: none;"><?php echo $juri->base(); ?></a>
														</span>
														<br />
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.MetaDesc'); ?></span>
													</td>
													<td width="10%" nowrap="nowrap" align="right" valign="bottom" style="border-left: 1px solid #e1e1e1; font-size: 1.2em; color: #999; padding: 0 0 5px 10px; text-align: right; vertical-align: bottom;">
														Support Center
													</td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End Header ====== -->

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #fff; background: #fff; font-size: 0.9em; line-height: 1.6em;">
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
												$ticket->summary = JText::_('(no content found)');
											}
										}
										$ticket->summary = str_replace("\r", "", $ticket->summary);
										$ticket->summary = str_replace("\t", " ", $ticket->summary);
										$ticket->summary = str_replace("\n", " ", $ticket->summary);

										$sef = JRoute::_($base . $ticket->id);
										$link = rtrim($juri->base(), DS) . DS . trim($sef, DS);

										//$tags = $st->get_tag_string($ticket->id, 0, 0, NULL, 0, 1);
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

										<!-- ====== Start Footer Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="20" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Footer Spacer ====== -->

										<!-- ====== Start Header ====== -->
										<table width="650" cellpadding="2" cellspacing="3" border="0" style="border-collapse: collapse; border-top: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td align="left" valign="bottom" style="line-height: 1; padding: 5px 0 0 0; ">
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $juri->base(); ?>"><?php echo $juri->base(); ?></a>. Visit our <a href="<?php echo $juri->base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo $juri->base(); ?>/support">Support Center</a> if you have any questions.</span>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End Header ====== -->

										<!-- ====== Start Footer Spacer ====== -->
										<table width="650" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr style="border-collapse: collapse;">
													<td height="30" style="border-collapse: collapse; color: #fff !important;"><div style="height: 30px !important; visibility: hidden;">----</div></td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End Footer Spacer ====== -->

									</td>
									<td bgcolor="#ffffff" width="10" style="border-collapse: collapse;"></td>
								</tr>
							</tbody>
						</table>
						<!-- ====== End Content Wrapper Table ====== -->
					</td>
				</tr>
			</tbody>
		</table>
		<!-- ====== End Body Wrapper Table ====== -->
		<style type="text/css">
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: #ffffff !important; margin: 0 !important; padding: 0 !important; }
		img { outline: none !important; text-decoration: none !important; display: block !important; }
		@media only screen and (min-device-width: 481px) { body { -webkit-text-size-adjust: 140% !important; } }
		</style>
	</body>
</html>