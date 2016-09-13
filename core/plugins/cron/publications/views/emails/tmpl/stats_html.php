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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dateFormat = 'M d, Y';

$baseManage = 'publications/submit';
$baseView = 'publications';

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');

$mconfig = Component::params('com_members');
$pPath   = trim($mconfig->get('webpath'), DS);
$profileThumb = NULL;

// CSS
$backgroundColor = '#FFFFFF';
$introTextColor  = '#efd09c';
$headerBgColor   = '#000000';
$headerTextColor = '#CCCCCC';
$footerBgColor   = '#f8f8f8';
$borderColor     = '#cbb185';
$textColor       = '#616161';
$linkColor       = '#33a9cf';
$titleLinkColor  = '#333';
$footerTextColor = '#999999';
$boxBgColor      = '#f6eddd';

$append = '?from=' . $this->user->get('email');
$lastMonth = date('M Y', strtotime("-1 month"));

$profileLink = $this->user->link();
$profileThumb = $this->user->picture();

// More publications?
$more = count($this->pubstats) - $this->limit;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Publication Stats Update</title>
		<style type="text/css">

		/* Client-specific Styles */
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: <?php echo $backgroundColor; ?> !important; margin: 0 !important; padding: 0 !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }

		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
		.ExternalClass { width:100%; } /* Force Hotmail to display emails at full width */
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; } /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
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
				color: #33a9cf; /* or whatever your want */
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
				color: #33a9cf;
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
	<body style="width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif; font-size: 12px; -webkit-text-size-adjust: none; color: <?php echo $textColor; ?>; line-height: 1.4em; background: <?php echo $backgroundColor; ?>; text-rendering: optimizeLegibility; border-color: <?php echo $backgroundColor; ?>; border: none">

		<!-- ====== Start Body Wrapper Table ====== -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; min-width: 100%;" bgcolor="<?php echo $backgroundColor; ?>">
			<tbody>
				<tr>
					<td align="center">

						<!-- ====== Start Content Wrapper Table ====== -->
						<table width="670" cellpadding="0" cellspacing="0" border="0" style="width: 670px !important;">
							<tbody>
								<tr>
									<td>

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>;border-color: <?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="50" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 50px !important; visibility: hidden; color: <?php echo $backgroundColor; ?>">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->

									<!-- ====== Header Table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $headerBgColor; ?>; color: <?php echo $headerTextColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>; border-top: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $headerBgColor; ?>">
										<tbody>
											<tr>
												<td width="20" height="30"></td>
												<?php if ($this->image) { ?>
												<td width="55">
													<a href="<?php echo $base . $append; ?>">
														<img width="55" border="0" src="<?php echo $this->image; ?>" alt="" />
													</a>
												</td>
												<td width="10"></td>
												<td width="425">
												<?php } else { ?>
												<td width="480">
												<?php } ?>
													<p style="color: <?php echo $introTextColor; ?>; margin: 10px 0 3px 0; font-weight: bold;"><strong>Dear <?php echo $this->user->get('name'); ?>,</strong></p>
												<p style="margin: 0; font-size: 12px;">Here is a monthly usage report for your published datasets in <?php echo Config::get('sitename'); ?>: <?php echo date('M Y'); ?> </p>
												</td>
												<td width="40">
													<?php if ($profileThumb) { ?>
													<a href="<?php echo $profileLink . DS . 'profile' . $append; ?>">
														<img width="30" border="0" src="<?php echo $profileThumb; ?>" alt="" />
													</a>
													<?php } ?>
												</td>
												<td width="15"></td>
											</tr>
										</tbody>
									</table>

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="30" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 30px !important; visibility: hidden;">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->

									<!-- ====== Start Content Table ====== -->
									<?php if ($more > 1)
									{ ?>
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td width="25"></td>
												<td width="645"><p>Latest usage statistics on your <?php echo $this->limit; ?> top publications:</p></td>
											</tr>
										</tbody>
									</table>
									<?php }
									?>
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<?php
												$i = 0;
												foreach ($this->pubstats as $stat)
												{
													$i++;

													if ($i > $this->limit)
													{
														break;
													}

													$sefManage = $baseManage . DS . $stat->publication_id;
													$sefView   = $baseView . DS . $stat->publication_id;

													$thumb = $base . DS . $baseView . DS . $stat->publication_id . DS . $stat->publication_version_id . DS . 'Image:thumb';
													$link  = $base . DS . trim($sefView, DS) . $append;
													$manageLink  = $base . DS . trim($sefManage, DS) . $append;
											?>
											<tr>
												<td width="25"></td>
												<td width="75">
													<a href="<?php echo $link; ?>"><img width="55" src="<?php echo $thumb; ?>" style="width:55px;" alt=""></a>
												</td>
												<td width="545">
													<p style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"><a href="<?php echo $link; ?>" style="color: <?php echo $titleLinkColor; ?>; text-decoration: none;"><?php echo $stat->title; ?></a></p>
													<table cellpadding="0" cellspacing="0" border="0" style="font-size: 12px; padding: 0; margin: 0;">
														<tbody>
															<tr style="padding: 0; margin: 0;">
																<td width="265">Page views last month:</td>
																<td width="50" style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"><?php echo $stat->monthly_views; ?></td>
																<td width="30"></td>
																<td width="200"></td>
															</tr>
															<tr>
																<td height="10" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 10px !important; visibility: hidden;">&nbsp;</div></td>
															</tr>
															<tr style="padding: 0; margin: 0;">

																<td width="265">Downloads last month:</td>
																<td width="50" style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"><?php echo $stat->monthly_primary; ?></td>
																<td width="30"></td>
																<td width="200" style="padding: 0; margin: 0;">
																</td>

															</tr>
															<tr>
																<td height="10" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 10px !important; visibility: hidden;">&nbsp;</div></td>
																<td></td>
																<td></td>
																<td></td>
															</tr>
															<tr style="padding: 0; margin: 0;">

																<td width="265">Total downloads to date:</td>
																<td width="50" style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"><?php echo $stat->total_primary; ?></td>
																<td width="30"></td>
																<td width="200" style="color: #777; font-style: italic;text-align: right;"><a href="<?php echo $link;  ?>" style="color: <?php echo $linkColor; ?>;">View publication</a> | <a href="<?php echo $manageLink; ?>" style="color: <?php echo $linkColor; ?>;">Manage</a></td>

															</tr>
														</tbody>
													</table>
												</td>
												<td width="25"></td>
											</tr>
											<tr>
												<td width="25" height="25"><div style="height: 25px !important; visibility: hidden; color: <?php echo $backgroundColor; ?>">&nbsp;</div></td>
												<td width="75"></td>
												<td width="545"></td>
												<td width="25"></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<!-- ====== End Content Table ====== -->

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="20" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 20px !important; visibility: hidden;">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->

										<!-- ====== All datasets table ====== -->
										<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
											<tbody>
												<tr>
													<td style="background-color: <?php echo $backgroundColor; ?>; text-align: center; font-size: 12px;"><p style="font-size: 12px; margin: 0">All of your published datasets have been downloaded a total of <span style="font-weight: bold;"><?php echo $this->totals->all_total_primary; ?></span> times to date. <a href="<?php echo $profileLink . '/impact' . $append; ?>" style="font-weight: bold; color: <?php echo $linkColor; ?>">View all usage</a><p></td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End All datasets table ====== -->

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="20" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 20px !important; visibility: hidden;">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->

									<!-- ====== Summary table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td width="25"></td>
												<td width="620">
													<div style="font-size: 12px; line-height: 24px; color: #666666; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; background-color: <?php echo $boxBgColor; ?>; padding: 10px; border-radius:6px 6px 6px 6px; -moz-border-radius: 6px 6px 6px 6px; -webkit-border-radius:6px 6px 6px 6px; -webkit-font-smoothing: antialiased; text-align: center;">
														<p style="margin: 0;">Publishing your data on <?php echo Config::get('sitename'); ?> increases access to and impact of your research!</p>
														<div style=""><a href="<?php echo $base . DS . 'publications' . DS . 'submit' . $append; ?>" style="color: #ffffff; background-color: #000000; padding: 5px 10px; border-radius:6px 6px 6px 6px; -moz-border-radius: 6px 6px 6px 6px; text-decoration: none;">View all publications and publish more data</a></div>
													</div>
												</td>
												<td width="25"></td>
											</tr>
										</tbody>
									</table>

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>;border-color: <?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="30" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 30px !important; visibility: hidden; color: <?php echo $backgroundColor; ?>;">&nbsp;</div></td>
											</tr>
										</tbody>
									</table>
									<!-- ====== End Spacer ====== -->

									<!-- ====== Footer Table ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing: antialiased; background-color: <?php echo $footerBgColor; ?>; color: <?php echo $borderColor; ?>; border-right: 1px solid <?php echo $borderColor; ?>; border-left: 1px solid <?php echo $borderColor; ?>; border-bottom: 1px solid <?php echo $borderColor; ?>;" bgcolor="<?php echo $footerBgColor; ?>">
										<tbody>
											<tr>
												<td width="25"></td>
												<td width="620"><p style="text-align: right; font-size: 12px; color: <?php echo $footerTextColor; ?>; margin: 15px 0; ">To unsubscribe, adjust "Receive monthly usage reports and other news" setting on your profile at <a href="<?php echo $profileLink . '/profile' . $append; ?>" style="color: <?php echo $linkColor; ?>;"><?php echo $base; ?></a></p></td>
												<td width="25"></td>
											</tr>
										</tbody>
									</table>

									<!-- ====== Start Spacer ====== -->
									<table width="670" cellpadding="0" cellspacing="0" border="0" style="background-color: <?php echo $backgroundColor; ?>;" bgcolor="<?php echo $backgroundColor; ?>;border-color: <?php echo $backgroundColor; ?>">
										<tbody>
											<tr>
												<td height="50" style="color: <?php echo $backgroundColor; ?> !important;background-color: <?php echo $backgroundColor; ?>;"><div style="height: 50px !important; visibility: hidden;">&nbsp;</div></td>
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
		body { width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Verdana, Arial, sans-serif !important; background-color: <?php echo $backgroundColor; ?> !important; margin: 0 !important; padding: 0 !important; }
		img { outline: none !important; text-decoration: none !important; display: block !important; }
		@media only screen and (min-device-width: 481px) { body { -webkit-text-size-adjust: 140% !important; } }
		</style>
	</body>
</html>
