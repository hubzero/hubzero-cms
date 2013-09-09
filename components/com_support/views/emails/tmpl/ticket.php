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

$juri =& JURI::getInstance();
$jconfig =& JFactory::getConfig();

$dateFormat = '%d %b %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/' . $this->ticket->id;
}
else
{
	$sef = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket->id);
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

switch ($this->ticket->severity)
{
	case 'critical': $bgcolor = '#ffd3d4'; $bdcolor = '#e9bcbc'; break;
	case 'major':    $bgcolor = '#fbf1be'; $bdcolor = '#e9e1bc'; break;
	case 'minor':    $bgcolor = '#d3e3ff'; $bdcolor = '#bccbe9'; break;
	case 'trivial':  $bgcolor = '#d3f9ff'; $bdcolor = '#bce1e9'; break;

	case 'normal':
	default:
		$bgcolor = '#f1f1f1';
		$bdcolor = '#e1e1e1';
	break;
}

$usertype = JText::_('Unknown');

$submitter = JUser::getInstance($this->ticket->login);
if ($submitter && is_object($submitter))
{
	if (version_compare(JVERSION, '1.6', 'lt'))
	{
		$usertype = $submitter->get('usertype');
	} 
	else
	{
		jimport( 'joomla.user.helper' );
		$usertype = implode(', ', JUserHelper::getUserGroups($submitter->get('uidNumber')));
	}
}

$comment = $this->ticket->report;
?>

--<?php echo $this->boundary . "\n"; ?>
Content-type: text/plain;charset=utf-8

<?php if ($this->delimiter) { ?>
<?php echo $this->delimiter . "\n"; ?>
You can reply to this message, just include your reply text above this area
Attachments (up to 2MB each) are permitted
Message from <?php echo rtrim($juri->base(), DS); ?>/support / Ticket #<?php echo $this->ticket->id; ?>
<?php } ?>

<?php 
$message  = '----------------------------'."\n";
$message .= strtoupper(JText::_('TICKET')).': '.$this->ticket->id."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.$this->ticket->summary."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$this->ticket->created."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->name . ($this->ticket->login ? ' ('.$this->ticket->login.')' : '') . "\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_USERTYPE')).': '.$usertype."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_EMAIL')).': '. $this->ticket->email ."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_IP_HOSTNAME')).': '. $this->ticket->ip .' ('.$this->ticket->hostname.')' ."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_OS')).': '. $this->ticket->os . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_BROWSER')).': '. $this->ticket->browser . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_UAS')).': '. $this->ticket->uas . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_COOKIES')).': ' . ($this->ticket->cookies ? JText::_('COM_SUPPORT_COOKIES_ENABLED') : JText::_('COM_SUPPORT_COOKIES_DISABLED')) . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_REFERRER')).': '. $this->ticket->referrer . "\n";
$message .= '----------------------------'."\n\n";
$message .= $this->attach->parse($comment);

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
?>

--<?php echo $this->boundary . "\n"; ?>
Content-type: text/html;charset=utf-8";

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
		<table width="100%" cellpadding="0" cellspacing="0" border="0"  id="backgroundTable" style="background-color: #ffffff; min-width: 100%;" bgcolor="#ffffff">
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
<?php if ($this->delimiter) { ?>
										<!-- ====== Start Header Spacer ====== -->
										<table width="650" cellpadding="0" cellspacing="0" border="0" style="border: 1px dashed #b5c6b5;">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse; color: #9bac9b;">
													<div style="height: 0px; overflow: hidden; color: #fff; visibility: hidden;"><?php echo $this->delimiter; ?></div>
													<div style="text-align: center; font-size: 90%; display: block; padding: 1em;">&uarr; You can reply to this message, just include your reply text above this area. Attachments (up to 2MB each) are permitted.</div>
												</td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->
<?php } ?>
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

										<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bdcolor; ?>; background: <?php echo $bgcolor; ?>; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
													<th colspan="2" style="font-weight: normal; border-bottom: 1px solid <?php echo $bdcolor; ?>; padding: 8px; text-align: left" align="left">
														New support ticket
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td id="ticket-number" style="padding: 8px; font-size: 2.5em; font-weight: bold; text-align: center; padding: 8px 30px;" align="center">
														#<?php echo $this->ticket->id; ?>
													</td>
													<td width="100%" style="padding: 8px;">
														<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
															<tbody>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Created:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">@ <?php echo JHTML::_('date', $this->ticket->created, $timeFormat, $tz); ?> on <?php echo JHTML::_('date', $this->ticket->created, $dateFormat, $tz); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Creator:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->ticket->name ? $this->escape($this->ticket->name) : 'Unknown'; ?> <?php echo $this->ticket->login ? '(' . $this->escape($this->ticket->login) . ')' : ''; ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('TICKET_DETAILS_USERTYPE'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $usertype; ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_EMAIL'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->email); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_IP_HOSTNAME'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->ip) . ' (' . $this->escape($this->ticket->hostname) . ')'; ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_OS'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->os); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_BROWSER'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->browser); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_UAS'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->uas); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo JText::_('COM_SUPPORT_REFERRER'); ?>:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->escape($this->ticket->referrer); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Link:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>

										<table width="650" id="ticket-comments" style="border-collapse: collapse; margin: 2em 0 0 0; padding: 0" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr>
													<td style="padding: 0 2em;">
													<?php
														if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">')) 
														{
															$comment = str_replace("<br />", '', $comment);
															$comment = $this->escape($comment);
															$comment = nl2br($comment);
															$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
															$comment = preg_replace('/  /', ' &nbsp;', $comment);
														}
														$comment = $this->attach->parse($comment);
													?>
														<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $comment; ?></p>
													</td>
												</tr>
											</tbody>
										</table>

										<!-- ====== Start Footer Spacer ====== -->
										<table width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Footer Spacer ====== -->

										<!-- ====== Start Header ====== -->
										<table width="650" cellpadding="2" cellspacing="3" border="0" style="border-collapse: collapse; border-top: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td align="left" valign="bottom" style="line-height: 1; padding: 5px 0 0 0; ">
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $link; ?>"><?php echo $link; ?></a>. Visit our <a href="<?php echo $juri->base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo $juri->base(); ?>/support">Support Center</a> if you have any questions.</span>
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
	</body>
</html>

--<?php echo $this->boundary; ?>--