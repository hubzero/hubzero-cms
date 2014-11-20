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

$bgColor  = '#ffffff';
$tblColor = '#ffffff';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<style type="text/css">
		<?php /* Client-specific Styles */ ?>
		html {
			background-color: <?php echo $bgColor; ?>;
			margin: 0;
			padding: 0;
			height: 100%;
		}
		body {
			width: 100% !important;
			font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important;
			background-color: <?php echo $bgColor; ?> !important;
			margin: 0 !important;
			padding: 0 !important;
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
			font-size: 12px;
			line-height: 1.4em;
			color: #666;
			text-rendering: optimizeLegibility;
			height: 100%;
		}
		<?php /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */ ?>
		<?php /* Force Hotmail to display emails at full width */ ?>
		.ExternalClass {
			width: 100%;
		}
		<?php /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */?>
		.ExternalClass,
		.ExternalClass p,
		.ExternalClass span,
		.ExternalClass font,
		.ExternalClass td,
		.ExternalClass div {
			line-height: 100%;
		}
		#backgroundTable {
			margin: 0;
			padding: 0;
			width: 100% !important;
			min-width: 100%;
			line-height: 100% !important;
			background-color: <?php echo $bgColor; ?>;
		}
		<?php /* End reset */ ?>

		<?php
		/* Some sensible defaults for images
		1. "-ms-interpolation-mode: bicubic" works to help ie properly resize images in IE. (if you are resizing them using the width and height attributes)
		2. "border:none" removes border when linking images.
		3. Updated the common Gmail/Hotmail image display fix: Gmail and Hotmail unwantedly adds in an extra space below images when using non IE browsers. You may not always want all of your images to be block elements. Apply the "image_fix" class to any image you need to fix.

		Bring inline: Yes.
		*/
		?>
		img {
			outline: none !important;
			text-decoration: none !important;
			-ms-interpolation-mode: bicubic;
		}
		a img {
			border: none;
		}
		.image_fix {
			display: block !important;
		}

		<?php /* Yahoo paragraph fix: removes the proper spacing or the paragraph (p) tag. To correct we set the top/bottom margin to 1em in the head of the document. */ ?>
		p {
			margin: 1em 0;
		}

		<?php /* Remove spacing around Outlook 07, 10 tables */ ?>
		table {
			border-collapse: collapse;
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}
		table tr {
			border-collapse: collapse;
		}
		<?php /* Outlook 07, 10 Padding issue */ ?>
		table td {
			border-collapse: collapse;
		}
		table.tbl-wrap {
			background-color: <?php echo $tblColor; ?>;
		}
		table.tbl-spacer td div {
			color: <?php echo $tblColor; ?> !important;
			height: 30px !important;
			visibility: hidden;
		}
		table.tbl-header {
			border-bottom: 2px solid #e1e1e1;
		}
		table.tbl-header td.sitename {
			font-size: 1.4em;
			color: #999;
			padding: 0 10px 5px 0;
			text-align: left;
		}
		table.tbl-header td.tagline {
			line-height: 1;
			padding: 0 0 5px 10px;
		}
		table.tbl-header span.home {
			font-weight: bold;
			font-size: 0.85em;
			color: #666;
			-webkit-text-size-adjust: none;
		}
		table.tbl-header span.home a {
			color: #666;
			font-weight: bold;
			text-decoration: none;
			border: none;
		}
		table.tbl-header span.description {
			font-size: 0.85em;
			color: #666;
			-webkit-text-size-adjust: none;
		}
		table.tbl-header td.component {
			border-left: 1px solid #e1e1e1;
			font-size: 1.2em;
			color: #999;
			padding: 0 0 5px 10px;
			text-align: right;
			vertical-align: bottom;
		}
		table.tbl-footer {
			border-collapse: collapse;
			border-top: 2px solid #e1e1e1;
		}
		table.tbl-footer td {
			line-height: 1;
			padding: 5px 0 0 0;
		}
		table.tbl-footer td span {
			font-size: 0.85em;
			color: #666;
			-webkit-text-size-adjust: none;
		}

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
	<body bgcolor="<?php echo $bgColor; ?>">
		<!-- Start Body Wrapper Table -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" bgcolor="<?php echo $bgColor; ?>">
			<tbody>
				<tr>
					<td bgcolor="<?php echo $bgColor; ?>" align="center">

						<!-- Start Content Wrapper Table -->
						<table class="tbl-wrap" width="670" cellpadding="0" cellspacing="0" border="0">
							<tbody>
								<tr>
									<td bgcolor="<?php echo $tblColor; ?>" width="10"></td>
									<td class="tbl-body" bgcolor="<?php echo $tblColor; ?>" width="650" align="left">

										<!-- Start Header Spacer -->
										<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr>
													<td height="30"></td>
												</tr>
											</tbody>
										</table>
										<!-- End Header Spacer -->

										<!-- Start content -->
										<jdoc:include type="component" />
										<!-- End content -->

										<!-- Start Footer Spacer -->
										<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr>
													<td height="30"><div>----</div></td>
												</tr>
											</tbody>
										</table>
										<!-- End Footer Spacer -->

									</td>
									<td bgcolor="<?php echo $tblColor; ?>" width="10"></td>
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