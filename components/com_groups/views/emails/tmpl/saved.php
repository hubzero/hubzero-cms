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

$juri    = JURI::getInstance();
$jconfig = JFactory::getConfig();

// build urls
$base      = rtrim(str_replace('administrator', '', $juri->base()), DS);
$groupLink = $base . DS . 'groups' . DS . $this->group->get('cn');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background-color: #fff; margin: 0; padding: 0;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Groups</title>
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

										<!-- ====== Start Header ====== -->
										<table cellpadding="2" cellspacing="3" border="0" width="100%" style="border-collapse: collapse; border-bottom: 2px solid #e1e1e1;">
											<tbody>
												<tr>
													<td width="10%" nowrap="nowrap" align="left" valign="bottom" style="font-size: 1.4em; color: #999; padding: 0 10px 5px 0; text-align: left;">
														<?php echo $jconfig->getValue('config.sitename'); ?>
													</td>
													<td width="80%" align="left" valign="bottom" style="line-height: 1; padding: 0 0 5px 10px;">
														<span style="font-weight: bold; font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;">
															<a href="<?php echo $base; ?>" style="color: #666; font-weight: bold; text-decoration: none; border: none;"><?php echo $base; ?></a>
														</span>
														<br />
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.MetaDesc'); ?></span>
													</td>
													<td width="10%" nowrap="nowrap" align="right" valign="bottom" style="border-left: 1px solid #e1e1e1; font-size: 1.2em; color: #999; padding: 0 0 5px 10px; text-align: right; vertical-align: bottom;">
														Groups
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

										<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #e1e1e1; background: #f1f1f1; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
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
													<th colspan="2" style="font-weight: bold; border-bottom: 1px solid #e1e1e1; padding: 8px; text-align: left; font-style: italic;" align="left">
														Group Saved
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td id="ticket-number" style="padding: 8px; font-size: 2.5em; font-weight: bold; text-align: center; padding: 8px 30px;" align="center">
														<img src="<?php echo $base . DS . ltrim($this->group->getLogo(), DS); ?>" width="100px" />
													</td>
													<td width="100%" style="padding: 8px;">
														<table style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
															<tbody>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Group:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">
																		<?php echo $this->group->get('description'); ?>
																	</td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Alias:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">
																		<?php echo $this->group->get('cn'); ?>
																	</td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Updated:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">@ <?php echo JHTML::_('date', 'now', JText::_('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', 'now', JText::_('DATE_FORMAT_HZ1')); ?></td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Updated By:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">
																		<?php
																			echo $this->juser->get('name') . ' ('.$this->juser->get('email').')';
																		?>
																	</td>
																</tr>
																<tr>
																	<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Link:</th>
																	<td style="text-align: left; padding: 0 0.5em;" align="left">
																		<a href="<?php echo $groupLink; ?>">
																			<?php echo $groupLink; ?>
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>

										<table width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; background: #fff; ">
											<tbody>
												<tr>
													<td width="100%" style="padding: 8px;">
														<table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
															<tbody>
																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Name:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																		<?php echo $this->group->get('description'); ?>
																	</td>
																</tr>

																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Interests (Tags):'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																		<?php
																			$gt = new GroupsTags( JFactory::getDBO() );
																			$tags = $gt->get_tag_string($this->group->get('gidNumber'));
																		?>
																		<?php if ($tags) : ?>
																			<?php echo $tags; ?>
																		<?php else : ?>
																			&lt;Empty&gt;
																		<?php endif; ?>
																	</td>
																</tr>

																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Public Description:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left;padding: 1em; line-height:18px;" align="left">
																		<?php if ($this->group->get('public_desc')) : ?>
																			<?php echo $this->group->get('public_desc'); ?>
																		<?php else : ?>
																			&lt;Empty&gt;
																		<?php endif; ?>
																	</td>
																</tr>

																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Private Description:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																		<?php if ($this->group->get('private_desc')) : ?>
																			<?php echo $this->group->get('private_desc'); ?>
																		<?php else : ?>
																			&lt;Empty&gt;
																		<?php endif; ?>
																	</td>
																</tr>

																<?php if (!$this->group->isSuperGroup()) : ?>
																	<tr>
																		<td align="left">&nbsp;</td>
																	</tr>
																	<tr>
																		<td style="text-align: left;font-weight: bold;" align="left">
																			<?php echo JText::_('Logo:'); ?>
																		</td>
																	</tr>
																	<tr>
																		<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																			<?php if ($this->group->get('logo')) : ?>
																				<img src="<?php echo $base . DS . ltrim($this->group->getLogo(), DS); ?>" width="50px" />
																			<?php else : ?>
																				&lt;Not Set&gt;
																			<?php endif; ?>
																		</td>
																	</tr>
																<?php endif ;?>

																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Membership Settings/Join Policy:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																		<?php
																		// Determine the join policy
																		switch ($this->group->get('join_policy'))
																		{
																			case 3: $policy = JText::_('Closed');      break;
																			case 2: $policy = JText::_('Invite Only'); break;
																			case 1: $policy = JText::_('Restricted');  break;
																			case 0:
																			default: $policy = JText::_('Open'); break;
																		}
																		echo $policy;

																		if ($this->group->get('join_policy') == 1)
																		{
																			echo '<br /><em>' . $this->group->get('restrict_msg') . '</em>';
																		}
																		?>
																	</td>
																</tr>


																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Discoverability:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
																		<?php
																		// Determine the discoverability
																		switch ($this->group->get('discoverability'))
																		{
																			case 1:  $discoverability = JText::_('Hidden'); break;
																			case 0:
																			default: $discoverability = JText::_('Visible'); break;
																		}
																		echo $discoverability;
																		?>
																	</td>
																</tr>

																<tr>
																	<td align="left">&nbsp;</td>
																</tr>
																<tr>
																	<td style="text-align: left;font-weight: bold;" align="left">
																		<?php echo JText::_('Access Permissions:'); ?>
																	</td>
																</tr>
																<tr>
																	<td style="text-align:left; padding:1em; line-height:18px;" align="left">
																		<?php
																		//access levels
																		$levels = array(
																			//'anyone' => 'Enabled/On',
																			'anyone' => 'Any HUB Visitor',
																			'registered' => 'Only Registered User of the HUB',
																			'members' => 'Only Group Members',
																			'nobody' => 'Disabled/Off'
																		);

																		// Get plugins
																		JPluginHelper::importPlugin('groups');
																		$dispatcher = JDispatcher::getInstance();
																		$group_plugins = $dispatcher->trigger('onGroupAreas', array());
																		array_unshift($group_plugins, array(
																			'name'             => 'overview',
																			'title'            => 'Overview',
																			'default_access'   => 'anyone',
																			'display_menu_tab' => true
																		));

																		$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group);

																		foreach ($group_plugins as $plugin)
																		{
																			if ($plugin['display_menu_tab'] == 1)
																			{
																				$title  = $plugin['title'];
																				$perm = $access[$plugin['name']];
																				echo $title . ' => ' . $levels[$perm] . '<br />';
																			}
																		}
																		?>
																	</td>
																</tr>
																<?php $params = JComponentHelper::getParams('com_groups'); ?>

																<?php if ($params->get('email_comment_processing')) :?>
																	<tr>
																		<td align="left">&nbsp;</td>
																	</tr>
																	<tr>
																		<td style="text-align: left;font-weight: bold;" align="left">
																			<?php echo JText::_('Discussion Group Emails Autosubscribe:'); ?>
																		</td>
																	</tr>
																	<tr>
																		<td style="text-align:left; padding:1em; line-height:18px;" align="left">
																			<?php
																			if ($this->group->get('discussion_email_autosubscribe'))
																			{
																				echo JText::_('On');
																			}
																			else
																			{
																				echo JText::_('Off');
																			}
																			?>
																		</td>
																	</tr>
																<?php endif; ?>

															</tbody>
														</table>
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
														<span style="font-size: 0.85em; color: #666; -webkit-text-size-adjust: none;"><?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you are a group manager for this group. Visit our <a href="<?php echo rtrim($base, DS); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($base, DS); ?>/support">Support Center</a> if you have any questions.</span>
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