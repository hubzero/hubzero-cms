<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$config  = JComponentHelper::getParams( 'com_projects' );

$hubShortName = $jconfig->getValue('config.sitename');

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'projects/' . $this->project->alias;
}
else
{
	$sef = JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias);
}

$projectUrl = rtrim($base, DS) . DS . trim($sef, DS);
$sef 	.= $this->uid ? '' : '/?confirm=' . $this->code . '&email=' . $this->email;
$link = rtrim($base, DS) . DS . trim($sef, DS);

// Page title
$title = $hubShortName . ' ' . JText::_('COM_PROJECTS_PROJECTS');

// Main message
$subtitle  = $this->actor->get('name') . ' ';
if ($this->project->provisioned)
{
	$subtitle .= $this->uid
			? JText::_('COM_PROJECTS_EMAIL_ADDED_AS_PUB_AUTHOR')
			: JText::_('COM_PROJECTS_EMAIL_INVITED_AS_PUB_AUTHOR');
	$subtitle .= ' "'.$this->pub->title.'"';
}
else
{
	$subtitle .= $this->uid ? JText::_('COM_PROJECTS_EMAIL_ADDED_YOU') : JText::_('COM_PROJECTS_EMAIL_INVITED_YOU');
	$subtitle .= ' "'.$this->project->title.'" '.JText::_('COM_PROJECTS_EMAIL_IN_THE_ROLE').' ';
	$subtitle .= $this->role == 1 ? JText::_('COM_PROJECTS_LABEL_OWNER') : JText::_('COM_PROJECTS_LABEL_COLLABORATOR');
}

// Project owner
$owner   = $this->project->owned_by_group
		 ? $this->nativegroup->cn . ' ' . JText::_('COM_PROJECTS_GROUP')
		 : $this->project->fullname;

// Get the actual message
$comment = '';
if ($this->uid)
{
	$comment .= $this->project->provisioned
			? JText::_('COM_PROJECTS_EMAIL_ACCESS_PUB_PROJECT')."\n"
			: JText::_('COM_PROJECTS_EMAIL_ACCESS_PROJECT')."\n";
}
else
{
	$comment .= JText::_('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT') . ' ' . $hubShortName.' ';
	$comment .= JText::_('COM_PROJECTS_EMAIL_ACCEPT')."\n";
}
$comment .= $link ."\n\n";

// Extras
$footnote = JText::_('COM_PROJECTS_EMAIL_USER_IF_QUESTIONS') . ' '
	. $this->actor->get('name') . '  - ' . $this->actor->get('email') . "\n";

$showThumb = $config->get('showthumbemail', 0);

// Get project thumbnail
if ($showThumb)
{
	$thumb = ProjectsHtml::getThumbSrc($this->project->id, $this->project->alias, $this->project->picture, $config);
	$thumb = rtrim($base, DS) . DS . trim($thumb, DS);
}

// Set some styles
$bgcolor = '#f1f1f1';
$bdcolor = '#e1e1e1';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?php echo $title; ?></title>
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

		<!-- Start Body Wrapper Table -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0"  id="backgroundTable" style="background-color: #ffffff; min-width: 100%;" bgcolor="#ffffff">
			<tbody>
				<tr style="border-collapse: collapse;">
					<td bgcolor="#ffffff" align="center" style="border-collapse: collapse;">

						<!-- Start Content Wrapper Table -->
						<table width="670" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
							<tbody>
								<tr style="border-collapse: collapse;">
									<td bgcolor="#ffffff" width="10" style="border-collapse: collapse;"></td>
									<td bgcolor="#ffffff" width="650" align="left" style="border-collapse: collapse;">

										<!-- Start Header Spacer -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Header Spacer -->
<?php if ($this->delimiter) { ?>
										<!-- Start Header Spacer -->
										<table width="650" cellpadding="0" cellspacing="0" border="0" style="border: 1px dashed #b5c6b5;">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse; color: #9bac9b;">
													<div style="height: 0px; overflow: hidden; color: #fff; visibility: hidden;"><?php echo $this->delimiter; ?></div>
												</td>
											</tr>
										</table>
										<!-- End Header Spacer -->

										<!-- Start Header Spacer -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Header Spacer -->
<?php } ?>
										<!-- Start Header -->
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
														<?php echo $title; ?>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- End Header -->

										<!-- Start Header Spacer -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Header Spacer -->

										<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid <?php echo $bgcolor; ?>; background: <?php echo $bgcolor; ?>; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
													<th colspan="<?php echo $showThumb ? 3 : 2; ?>" style="font-weight: normal; border-bottom: 1px solid <?php echo $bdcolor; ?>; padding: 8px; text-align: left; background: #fbf7ee;" align="left">
														<?php echo $subtitle; ?>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<?php if ($showThumb) { ?>
													<td id="project-thumb" style="padding: 8px; text-align: left;" align="left">
														<div style="text-align: center; padding: 1em; width:50px; height: 50px; background: #FFFFFF;">
															<img width="50" border="0" src="<?php echo $thumb; ?>" alt="" />
														</div>
													</td>
													<?php } ?>
													<td id="project-alias" style="padding: 8px; font-size: 2.5em; font-weight: bold; text-align: center; padding: 8px 30px;" align="center">
														<?php echo $this->project->alias; ?>
													</td>
													<td width="100%" style="padding: 8px;">
														<table style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
															<tbody>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Title:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->project->title; ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Created:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">@ <?php echo JHTML::_('date', $this->project->created, JText::_('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', $this->project->created, JText::_('DATE_FORMAT_HZ1')); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Owner:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $owner; ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">URL:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left"><a href="<?php echo $link; ?>"><?php echo $projectUrl; ?></a></td>
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
													?>
														<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $comment; ?></p>

														<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left; font-size: 0.85em;"><?php echo $footnote; ?></p>
													</td>
												</tr>
											</tbody>
										</table>

										<!-- Start Footer Spacer -->
										<table width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="30" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- End Footer Spacer -->

										<!-- Start Header -->
										<table width="650" cellpadding="2" cellspacing="3" border="0" style="border-collapse: collapse; border-top: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td align="left" valign="bottom" style="line-height: 1; padding: 5px 0 0 0; ">
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you were added to the list of recipients on <a href="<?php echo $juri->base(); ?>"><?php echo $juri->base(); ?></a>. Visit our <a href="<?php echo $juri->base(); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo $juri->base(); ?>/support">Support Center</a> if you have any questions.</span>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- End Header -->

										<!-- Start Footer Spacer -->
										<table width="650" cellpadding="0" cellspacing="0" border="0">
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