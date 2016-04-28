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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = Request::root() . 'courses/' . $this->course->get('alias');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Instructor Digest</title>
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

		a { color: rgb(134, 174, 255); }

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
														<?php echo Config::get('sitename'); ?>
													</td>
													<td width="80%" align="left" valign="bottom" style="line-height: 1; padding: 0 0 5px 10px;">
														<span style="font-weight: bold; font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;">
															<a href="<?php echo Request::root(); ?>" style="color: #666; font-weight: bold; text-decoration: none; border: none;"><?php echo Request::root(); ?></a>
														</span>
														<br />
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;">
															<?php echo Config::get('MetaDesc'); ?>
														</span>
													</td>
													<td width="10%" nowrap="nowrap" align="right" valign="bottom" style="border-left: 1px solid #e1e1e1; font-size: 1.2em; color: #999; padding: 0 0 5px 10px; text-align: right; vertical-align: bottom;">
														Instructor Digest
													</td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End Header ====== -->

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="20" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<table id="course-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #F3F3F3; border: 1px solid #DDDDDD;">
											<tr>
												<td width="85" style="padding: 0 0 0 15px; opacity: 0.8">
													<?php $cap_path = rtrim(Request::root(), '/') . '/core/components/com_courses/site/assets/img/cap.png'; ?>
													<img width="80" src="<?php echo $cap_path; ?>" />
												</td>
												<td width="565" style="padding: 14px; border-bottom: 1px solid #CCCCCC;">
													<span style="font-weight: bold; font-size:14px;">Course Update:</span>
													<br />
													<span><?php echo $this->course->get('title'); ?></span>
												</td>
											</tr>
											<tr>
												<td></td>
												<td style="padding: 10px 14px;">
													Link: <a href="<?php echo $base; ?>"><?php echo $base; ?></a>
												</td>
											</tr>
										</table>

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="20" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<table id="course-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background-color: #EEEEEE;">
											<tr>
												<td width="315" style="background-color: #FFFFFF;">
													<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #DDDDDD;">
														<tr style="border-collapse: collapse;">
															<td height="30" width="40%" style="border-collapse: collapse; padding: 10px;">
																<span style="">Enrollments</span>
																<br />
																<span style="line-height: 40px; font-size:35px;"><?php echo $this->enrollments; ?></span>
															</td>
															<td height="30" width="60%" style="border-collapse: collapse;">
																<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																	<tr style="border-collapse: collapse; background-color: rgb(229, 244, 235);">
																		<td nowrap="nowrap" style="border-collapse: collapse; padding: 10px; border-bottom: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD;">
																			<span style="color: green; font-weight: bold; font-size: 15px;"><?php echo $this->passing; ?> passing</span>
																		</td>
																	</tr>
																	<tr style="border-collapse: collapse; background-color: rgb(252, 229, 229);">
																		<td nowrap="nowrap" style="border-collapse: collapse; padding: 10px; border-left: 1px solid #DDDDDD;">
																			<span style="color: red; font-weight: bold; font-size: 15px;"><?php echo $this->failing; ?> failing</span>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td height="20" width="20" style="border-collapse: collapse; background-color: #FFFFFF; border: none;"></td>
												<td width="315">
													<table  width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #DDDDDD;">
														<tr style="border-collapse: collapse;">
															<td height="30" width="50%" style="border-collapse: collapse; padding: 10px; background-color: #FFFFFF;">
																<span style="">Discussion Topics</span>
																<br />
																<span style="line-height: 40px; font-size:35px;"><?php echo $this->posts_cnt; ?></span>
															</td>
															<td height="30" width="50%" style="border-collapse: collapse; border-left: 1px solid #DDDDDD; padding: 10px; background-color:rgb(252, 243, 223); color: rgb(230, 158, 0)">
																<span style="">New</span>
																<br />
																<span style="line-height: 40px; font-size:35px;"><?php echo $this->latest_cnt; ?></span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>

										<!-- ====== Start Header Spacer ====== -->
										<table  width="650" cellpadding="0" cellspacing="0" border="0">
											<tr style="border-collapse: collapse;">
												<td height="20" style="border-collapse: collapse;"></td>
											</tr>
										</table>
										<!-- ====== End Header Spacer ====== -->

										<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
											<tr style="border-bottom: 1px solid #DDDDDD;">
												<td style="font-size: 13px; font-weight: bold; padding: 4px 0;">
													Latest Discussions
												</td>
												<td style="text-align: right;">
													<a href="<?php echo $base . '/' . $this->offering->get('alias') . '/discussions'; ?>"><?php echo $base . '/' . $this->offering->get('alias') . '/discussions'; ?></a>
												</td>
											</tr>
										</table>
										<?php if (count($this->latest) > 0) : ?>
											<?php foreach ($this->latest as $post) : ?>
											<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
												<tr>
													<td width="75" style="padding: 10px 0;">
														<img width="50" src="<?php echo Request::root() . User::getInstance($post->created_by)->picture(); ?>" />
													</td>
													<td style="padding: 10px 0;">
														<div style="position: relative; border: 1px solid #CCCCCC; padding: 12px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;">
															<div style="background: #FFFFFF; border: 1px solid #CCCCCC; width: 15px; height: 15px;
																position: absolute; top: 50%; left: -10px; margin-top: -7px;
																transform:rotate(45deg); -ms-transform:rotate(45deg); -webkit-transform:rotate(45deg);"></div>
															<div style="background: #FFFFFF; width: 11px; height: 23px; position: absolute; top: 50%; left: -1px; margin-top: -10px;"></div>
															<div style="color: #AAAAAA; font-size: 11px; text-align:center;">
																<?php echo User::getInstance($post->created_by)->get('name'); ?> | created: <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a'); ?>
															</div>
															<div>
																<?php echo $post->comment; ?>
															</div>
															<div style="color: #AAAAAA; font-size: 11px; text-align:center;">
																<?php $reply  = $base . '/' . $this->offering->get('alias') . '/discussions'; ?>
																<?php $thread = ($post->parent) ? $post->parent : $post->id; ?>
																<a href="<?php echo $reply . '?thread='.$thread.'&reply='.$post->id; ?>">reply</a>
															</div>
														</div>
													</td>
												</tr>
											</table>
											<?php endforeach; ?>
										<?php else : ?>
											<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
												<tr>
													<td style="padding: 10px 0;">
														<div>
															No new comments to display
														</div>
													</td>
												</tr>
											</table>
										<?php endif; ?>

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
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo Request::root(); ?> sent this email because you are the primary instructor of a course. Visit our <a href="<?php echo Request::root(); ?>legal/privacy">Privacy Policy</a> and <a href="<?php echo Request::root(); ?>support">Support Center</a> if you have any questions.</span>
													</td>
												</tr>
											</tbody>
										</table>
										<!-- ====== End Header ====== -->

										<!-- ====== Start Footer Spacer ====== -->
										<table width="650" cellpadding="0" cellspacing="0" border="0">
											<tbody>
												<tr style="border-collapse: collapse;">
													<td height="20" style="border-collapse: collapse; color: #fff !important;"><div style="height: 30px !important; visibility: hidden;">----</div></td>
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