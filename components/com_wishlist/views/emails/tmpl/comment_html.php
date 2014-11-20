<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?php echo JText::_('COM_SUPPORT_CENTER'); ?></title>
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
			body {
				-webkit-text-size-adjust: 100% !important;
				-ms-text-size-adjust: 100% !important;
				font-size: 100% !important;
			}
			table.tbl-wrap,
			table.tbl-wrap td.tbl-body {
				width: auto !important;
				margin: 0 2em !important;
			}
			table.tbl-header td {
				width: auto !important;
			}
			td.tbl-body .mobilehide {
				display: none !important;
			}
			#wish-number {
				float: none !important;
				width: auto !important;
			}
			table#wish-comments>tbody>tr>td {
				padding: 0 !important;
			}
		}
		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
			/* tablets, smaller screens, etc */
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

		<!-- Start Body Wrapper Table -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0"  id="backgroundTable" style="background-color: #ffffff; min-width: 100%;" bgcolor="#ffffff">
			<tbody>
				<tr style="border-collapse: collapse;">
					<td bgcolor="#ffffff" align="center" style="border-collapse: collapse;">

						<!-- Start Content Wrapper Table -->
						<table class="tbl-wrap" width="670" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
							<tbody>
								<tr style="border-collapse: collapse;">
									<td bgcolor="#ffffff" width="10" style="border-collapse: collapse;"></td>
									<td class="tbl-body" bgcolor="#ffffff" width="650" align="left" style="border-collapse: collapse;">

										<!-- Start Header Spacer -->
										<table  width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Header Spacer -->

										<!-- Start Header -->
										<table class="tbl-header" cellpadding="2" cellspacing="3" border="0" width="100%" style="border-collapse: collapse; border-bottom: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td width="10%" nowrap="nowrap" align="left" valign="bottom" style="font-size: 1.4em; color: #999; padding: 0 10px 5px 0; text-align: left;">
														<?php echo $jconfig->getValue('config.sitename'); ?>
													</td>
													<td class="mobilehide" width="80%" align="left" valign="bottom" style="line-height: 1; padding: 0 0 5px 10px;">
														<span style="font-weight: bold; font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;">
															<a href="<?php echo $juri->base(); ?>" style="color: #666; font-weight: bold; text-decoration: none; border: none;"><?php echo $juri->base(); ?></a>
														</span>
														<br />
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.MetaDesc'); ?></span>
													</td>
													<td width="10%" nowrap="nowrap" align="right" valign="bottom" style="border-left: 1px solid #e1e1e1; font-size: 1.2em; color: #999; padding: 0 0 5px 10px; text-align: right; vertical-align: bottom;">
														<?php echo JText::_('COM_WISHLIST'); ?>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- End Header -->

										<!-- Start Header Spacer -->
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Header Spacer -->

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
															&#x1f4a1;
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
													</td>
												</tr>
											</tbody>
										</table>

										<table width="100%" id="wish-comments" style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr>
													<th style="text-align: left;" align="left"><?php echo ($this->comment->get('anonymous') ? JText::_('COM_WISHLIST_UNKNOWN') : $this->escape($this->comment->creator('name'))); ?></th>
													<th class="timestamp" style="color: #999; text-align: right;" align="right"><span class="mobilehide"><?php echo JText::sprintf('@%s on %s', $this->comment->created('time'), $this->comment->created('date')); ?></span></th>
												</tr>
												<tr>
													<td colspan="2" style="padding: 0 2em;">
														<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $this->comment->content('parsed'); ?></div>
													</td>
												</tr>
											</tbody>
										</table>

										<!-- Start Footer Spacer -->
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Footer Spacer -->

										<!-- Start Header -->
										<table width="100%" cellpadding="2" cellspacing="3" border="0" style="border-collapse: collapse; border-top: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td align="left" valign="bottom" style="line-height: 1; padding: 5px 0 0 0; ">
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $link; ?>">wish #<?php echo $this->wish->get('id'); ?></a>. Visit our <a href="<?php echo rtrim($juri->base(), '/'); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($juri->base(), '/'); ?>/support">Support Center</a> if you have any questions.</span>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- End Header -->

										<!-- Start Footer Spacer -->
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr style="border-collapse: collapse;">
													<td height="30" style="border-collapse: collapse; color: #fff !important;"><div style="height: 30px !important; visibility: hidden;">----</div></td>
												</tr>
											</tbody>
										</table>
										<!-- End Footer Spacer -->

									</td>
									<td bgcolor="#ffffff" width="10" style="border-collapse: collapse;"></td>
								</tr>
							</tbody>
						</table>
						<!-- End Content Wrapper Table -->
					</td>
				</tr>
			</tbody>
		</table>
		<!-- End Body Wrapper Table -->
	</body>
</html>