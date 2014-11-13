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

$config = JFactory::getConfig();

if ($title = $this->getTitle())
{
	$this->setTitle($config->getValue('config.sitename') . ' - ' . $title);
}
$this->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<style type="text/css">
		<?php /* Client-specific Styles */ ?>
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: #ffffff !important; margin: 0 !important; padding: 0 !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
		<?php /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */ ?>
		<?php /* Force Hotmail to display emails at full width */ ?>
		.ExternalClass { width:100%; }
		<?php /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */?>
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
		#backgroundTable { margin:0; padding:0; width:100% !important; line-height: 100% !important; }
		<?php /* End reset */ ?>

		<?php
		/* Some sensible defaults for images
		1. "-ms-interpolation-mode: bicubic" works to help ie properly resize images in IE. (if you are resizing them using the width and height attributes)
		2. "border:none" removes border when linking images.
		3. Updated the common Gmail/Hotmail image display fix: Gmail and Hotmail unwantedly adds in an extra space below images when using non IE browsers. You may not always want all of your images to be block elements. Apply the "image_fix" class to any image you need to fix.

		Bring inline: Yes.
		*/
		?>
		img { outline: none !important; text-decoration: none !important; -ms-interpolation-mode: bicubic; }
		a img { border: none; }
		.image_fix { display: block !important; }

		<?php /* Yahoo paragraph fix: removes the proper spacing or the paragraph (p) tag. To correct we set the top/bottom margin to 1em in the head of the document. */ ?>
		p { margin: 1em 0; }

		<?php /* Outlook 07, 10 Padding issue */ ?>
		table td { border-collapse: collapse; }

		<?php /* Remove spacing around Outlook 07, 10 tables */ ?>
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
		}
		</style>

		<?php /* Targeting Windows Mobile
		<!--[if IEMobile 7]>
		<style type="text/css">
		</style>
		<![endif]-->
		 */ ?>

		<?php /* Outlook 2007/10 List Fix */ ?>
		<!--[if gte mso 9]>
		<style type="text/css" >
		.article-content ol, .article-content ul {
			margin: 0 0 0 24px;
			padding: 0;
			list-style-position: inside;
		}
		</style>
		<![endif]-->

		<jdoc:include type="head" />
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

										<!-- Start content -->
										<jdoc:include type="component" />
										<!-- End content -->

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