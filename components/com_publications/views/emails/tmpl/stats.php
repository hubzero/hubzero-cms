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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri =& JURI::getInstance();
$jconfig =& JFactory::getConfig();
$ih = new MembersImgHandler();

$baseManage = 'publications/submit';
$baseView = 'publications';

$base = trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$mconfig =& JComponentHelper::getParams( 'com_members' );
$pPath   = trim($mconfig->get('webpath'), DS);
$profileThumb = NULL;

if ($this->profilePic) {
	$profileThumb = $ih->createThumbName($this->profilePic);
	$profileThumb = $pPath . DS . Hubzero_View_Helper_Html::niceidformat($this->juser->get('id')) . DS . $profileThumb;
	
	if (!is_file(JPATH_ROOT . DS . $profileThumb))
	{
		$profileThumb = $pPath . DS . Hubzero_View_Helper_Html::niceidformat($this->juser->get('id')) . DS . 'thumb.png';
	}
	
	if (!is_file(JPATH_ROOT . DS . $profileThumb))
	{
		$profileThumb = NULL;
	}
	else
	{
		$profileThumb = $base . DS . $profileThumb;
	}
}

$profileLink = $base . DS . 'members' . DS . $this->juser->get('id');

?>
--<?php echo $this->boundary . "\n"; ?>
Content-type: text/plain;charset=utf-8

<?php 
$message  = 'Here is the monthly update on your recent publications usage' . "\n";
$message .= '----------------------------'."\n\n";

for ($a = 0; $a < count($this->pubstats); $a++)
{	
	// Check against limit
	if ($a >= $this->limit)
	{
		break;
	}
	
	$stat = $this->pubstats[$a];
	
	$sefManage 	= $baseManage . DS . $stat->publication_id;
	$sefView 	= $baseView . DS . $stat->publication_id;
	
	$message .= 'Publication #' . $stat->publication_id . ' "' . stripslashes($stat->title) . '"' . "\n";
	$message .= 'View publication:          ' . $base . DS . trim($sefView, DS) . "\n";
	$message .= 'Manage publication:        ' . $base . DS . trim($sefManage, DS) . "\n\n";
	
	$message .= 'Usage in the past month... ' . "\n";
	$message .= 'Page views:                ' . $stat->monthly_views. "\n";
	$message .= 'Primary content accesses:  ' . $stat->monthly_primary. "\n";
	$message .= 'Supporting docs downloads: ' . $stat->monthly_support. "\n";

	$message .= '----------------------------'."\n\n";
}

// More publications?
$more = count($this->pubstats) - $a;

if ($more > 0)
{
	//$message .= '... and ' . $more . ' more open tickets.' . "\n";
}

echo $message . "\n";
?>


--<?php echo $this->boundary . "\n"; ?>
Content-type: text/html;charset=utf-8";

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Publication Stats Update</title>
		<style type="text/css">
		
		/* Client-specific Styles */		
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: #FFFFFF !important; margin: 0 !important; padding: 0 !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
		
		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
		.ExternalClass { width:100%; } /* Force Hotmail to display emails at full width */
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; } /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
		#backgroundTable { margin:0; padding:0; width:100% !important; line-height: 100% !important; background-color: #dedede; }
		/* End reset */

		/* Some sensible defaults for images
		1. "-ms-interpolation-mode: bicubic" works to help ie properly resize images in IE. (if you are resizing them using the width and height attributes)
		2. "border:none" removes border when linking images.
		3. Updated the common Gmail/Hotmail image display fix: Gmail and Hotmail unwantedly adds in an extra space below images when using non IE browsers. You may not always want all of your images to be block elements. Apply the "image_fix" class to any image you need to fix.

		Bring inline: Yes.
		*/
		img { outline: none !important; text-decoration: none !important; -ms-interpolation-mode: bicubic; display: block !important; }
		a img { border: none; }
		.image_fix { display: block !important; }

		/* Yahoo paragraph fix: removes the proper spacing or the paragraph (p) tag. To correct we set the top/bottom margin to 1em in the head of the document. */
		p { margin: 1em 0; }

		/* Outlook 07, 10 Padding issue */
		table td, table tr { border-collapse: collapse; }
		
		tbody { border: none; }

		/* Remove spacing around Outlook 07, 10 tables */
		table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0; margin: 0; }

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
				color: #33a9cf !important;
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
				color: #33a9cf !important;
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
	<body style="width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif; font-size: 12px; -webkit-text-size-adjust: none; color: #616161; line-height: 1.4em; background: #FFFFFF; text-rendering: optimizeLegibility; border-color: #FFFFFF; border: none" bgcolor="#FFFFFF">

		<!-- ====== Start Body Wrapper Table ====== -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; min-width: 100%;" bgcolor="#FFFFFF">
			<tbody>
				<tr>
					<td align="center">

						<!-- ====== Start Content Wrapper Table ====== -->
						<table width="670" cellpadding="0" cellspacing="0" border="0" style="width: 670px !important;">
							<tbody>
								<tr>
									<td>
										
									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF;" bgcolor="#ffffff;border-color: #FFFFFF">
										<tbody>
											<tr>
												<td height="50" style="color: #FFFFFF !important;background-color: #FFFFFF;"><div style="height: 50px !important; visibility: hidden; color: #FFFFFF">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->
											
									<!-- ====== Header Table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f8f8; color: #5c3a14; border-right: 1px solid #dedede; border-left: 1px solid #dedede; border-top: 1px solid #dedede;" bgcolor="#f8f8f8">
										<tbody>
											<tr>
												<td width="20" height="30"></td>
												<td width="55">
													<a href="<?php echo $base; ?>">
														<img width="55" border="0" src="<?php echo $this->image; ?>" alt="" />
													</a>
												</td>
												<td width="10"></td>
												<td width="425">
													<p style="color: #000000; margin: 10px 0 3px 0; font-weight: bold;"><strong>Dear <?php echo $this->juser->get('name'); ?>,</strong></p>
												<p style="margin: 0; font-size: 12px;">Here is a monthly update on your published datasets on <?php echo $jconfig->getValue('config.sitename'); ?></p>
												</td>
												<td width="40">
													<?php if ($profileThumb) { ?>
													<a href="<?php echo $profileLink . DS . 'profile'; ?>">
														<img width="30" border="0" src="<?php echo $profileThumb; ?>" alt="" />
													</a>
													<?php } ?>
												</td>
												<td width="15"></td>
											</tr>
										</tbody>
									</table>
									
									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff">
										<tbody>
											<tr>
												<td height="30" style="color: #fff !important;background-color: #FFFFFF;"><div style="height: 30px !important; visibility: hidden;">----</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->
									
									<!-- ====== Start Content Table ====== -->
									<?php if ($more > 1)
									{ ?>
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff">
										<tbody>
											<tr>
												<td width="25"></td>
												<td width="645"><p>Latest usage statistics on your 3 top publications:</p></td>
											</tr>
										</tbody>
									</table>
									<?php } 
									?>
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff">
										<tbody>											
											<?php 
												$i = 0;
												foreach ($this->pubstats as $stat) { 
												// Get pub image
												$pubthumb = $this->helper->getThumb(
													$stat->publication_id, 
													$stat->publication_version_id, 
													$this->config, 
													false, 
													$stat->cat_url
												);
												
												$i++;
												
												if ($i > $this->limit)
												{
													break;
												}
												
												$sefManage 	= $baseManage . DS . $stat->publication_id;
												$sefView 	= $baseView . DS . $stat->publication_id;
												
												$thumb = $base . DS . trim($pubthumb, DS);
												$link  = $base . DS . trim($sefView, DS);	
												
												?>
											<tr>
												<td width="25"></td>
												<td width="75">
													<a href="<?php echo $link; ?>"><img width="55" border="0" src="<?php echo $thumb; ?>" label="Image" editable="true"></a>
												</td>
												<td width="545">
													<p style="color: #333; font-weight:bold;"><a href="<?php echo $link; ?>" style="color: #333; text-decoration: none;"><?php echo $stat->title; ?></a></p>
													<table cellpadding="0" cellspacing="0" border="0" align="left" style="font-size: 12px; padding: 0; margin: 0;">
				                                        <tbody>
															<tr style="padding: 0; margin: 0;">
																<td width="200" style="color: #777; font-style: italic;">Usage in the past 30 days</td>
																<td width="30"></td>
																<td width="280">Page views:</td>
																<td width="50" style="color: #333; font-weight:bold;"><?php echo $stat->monthly_views; ?></td>																																											
															</tr>
															<tr style="padding: 0; margin: 0;">
																<td width="200" style="padding: 0; margin: 0;">
																</td>
																<td width="30"></td>
																<td width="280">Primary content accesses:</td>
																<td width="50" style="color: #333; font-weight:bold;"><?php echo $stat->monthly_primary; ?></td>				
															</tr>
																<tr style="padding: 0; margin: 0;">
																	<td width="200" style="padding: 0; margin: 0;">
																		<a href="<?php echo $link; ?>" style="color: #33a9cf;">View publication</a>
																	</td>
																	<td width="30"></td>
																	<td width="280">Supporting docs downloads:</td>
																	<td width="50" style="color: #333; font-weight:bold;"><?php echo $stat->monthly_support; ?></td>				
																</tr>
				                                    	</tbody>
													</table>	
												</td>
												<td width="25"></td>
											</tr>
											<tr>
												<td width="25" height="25"><div style="height: 25px !important; visibility: hidden; color: #FFFFFF">----</div></td>
												<td width="75"></td>
												<td width="545"></td>
												<td width="25"></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<!-- ====== End Content Table ====== -->	
									
									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff">
										<tbody>
											<tr>
												<td height="30" style="color: #fff !important;background-color: #FFFFFF;"><div style="height: 30px !important; visibility: hidden;">----</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->
								
									<!-- ====== Summary table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff">
										<tbody>
											<tr>
												<td width="25"></td>
												<td width="620">
													<div style="font-size: 12px; line-height: 24px; color: #666666; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; background-color: #f6eddd; padding: 10px; border-radius:6px 6px 6px 6px; -moz-border-radius: 6px 6px 6px 6px; -webkit-border-radius:6px 6px 6px 6px; -webkit-font-smoothing: antialiased; text-align: center;">
			                                        <p style="margin: 0;">Publishing your data on <?php echo $jconfig->getValue('config.sitename'); ?> increases access to and impact of your research!</p>
													<div style=""><a href="<?php echo $base . DS . 'publications'; ?>" style="color: #ffffff; background-color: #000000; padding: 5px 10px; border-radius:6px 6px 6px 6px; -moz-border-radius: 6px 6px 6px 6px; text-decoration: none;">View all publications and publish more data</a></div>
			                                    </div>
												</td>
												<td width="25"></td>
											</tr>
										</tbody>
									</table>
																		
									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF; border-right: 1px solid #dedede; border-left: 1px solid #dedede;" bgcolor="#ffffff;border-color: #FFFFFF">
										<tbody>
											<tr>
												<td height="30" style="color: #fff !important;background-color: #FFFFFF;"><div style="height: 30px !important; visibility: hidden; color: #FFFFFF;">----</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->
									
									<!-- ====== Footer Table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing: antialiased; background-color: #f8f8f8; color: #ededed; border-right: 1px solid #dedede; border-left: 1px solid #dedede; border-bottom: 1px solid #dedede;" bgcolor="#f8f8f8">
										<tbody>
										
											<tr>
												<td width="25"></td>
												<td width="620"><p style="text-align: right; font-size: 12px; color: #999; margin: 15px 0; ">To unsubscribe, adjust "Receive monthly usage reports and other news" setting on your profile at <a href="<?php echo $profileLink . '/profile'; ?>" style="color: #33a9cf;"><?php echo $base; ?></a></p></td>
												<td width="25"></td>
											</tr>
										</tbody>
									</table>
									
									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFFF;" bgcolor="#ffffff;border-color: #FFFFFF">
										<tbody>
											<tr>
												<td height="50" style="color: #fff !important;background-color: #FFFFFF;"><div style="height: 50px !important; visibility: hidden;">----</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->
									</td>
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
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: #FFFFFF !important; margin: 0 !important; padding: 0 !important; }
		img { outline: none !important; text-decoration: none !important; display: block !important; }
		@media only screen and (min-device-width: 481px) { body { -webkit-text-size-adjust: 140% !important; } }
		</style>
	</body>
</html>

--<?php echo $this->boundary; ?>--
