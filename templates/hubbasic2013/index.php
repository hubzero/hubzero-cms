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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();
$juser  =& JFactory::getUser();

// Set the generator statement
$this->setGenerator('HUBzero - The open source platform for scientific and educational collaboration');

//do we want to include jQuery
if (JPluginHelper::isEnabled('system', 'jquery')) 
{
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.jquery.js');
} 
else 
{
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');
}

$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

if (md5($juser->get('username')) == '579499ed6b89886e17fcf374c81b47a2' || md5(Hubzero_Environment::ipAddress()) == '77c399b44f89766bf48ecb61aa2762cf')
{
	$v .= ' rwun';
}

$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'tooltip', 'comments', 'voting', 'icons', 'buttons', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<jdoc:include type="head" />
<?php if (JPluginHelper::isEnabled('system', 'debug')) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/debug.css" />
<?php } ?>
		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" />
		<![endif]-->
	</head>
	<body>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<div id="top">
			<div id="masthead" role="banner">
				<div class="inner">
					<h1>
						<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
							<span><?php echo $config->getValue('config.sitename'); ?></span>
						</a>
					</h1>

					<div id="account" role="navigation">
					<?php if (!$juser->get('guest')) { 
							$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
					?>
						<ul class="menu <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($profile); ?>" alt="<?php echo $juser->get('name'); ?>" width="30" height="30" />
									<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
										<?php echo stripslashes($juser->get('name')); ?> 
										<span class="account-email"><?php echo $juser->get('email'); ?></span>
									</a>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo JRoute::_('index.php?option=com_logout'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li id="account-login">
								<?php $login_route = (version_compare(JVERSION, '2.5', 'ge')) ? 'index.php?option=com_users&view=login' : 'index.php?option=com_user&view=login'; ?>
								<a href="<?php echo JRoute::_($login_route); ?>" title="<?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?>"><?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo JRoute::_('index.php?option=com_register'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo JText::_('TPL_HUBBASIC_REGISTER'); ?></a>
							</li>
						</ul>
						<?php /* <jdoc:include type="modules" name="account" /> */ ?>
					<?php } ?>
					</div><!-- / #account -->

					<div id="nav" role="main navigation">
						<a name="nav"></a>
						<jdoc:include type="modules" name="user3" />
					</div><!-- / #nav -->
				</div><!-- / .inner -->
			</div><!-- / #masthead -->

			<div id="sub-masthead">
				<div class="inner">
				<?php if ($this->countModules('helppane')) : ?>
					<p id="tab">
						<a href="<?php echo JRoute::_('index.php?option=com_support'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_NEED_HELP'); ?>">
							<span><?php echo JText::_('TPL_HUBBASIC_HELP'); ?></span>
						</a>
					</p>
				<?php endif; ?>
					<jdoc:include type="modules" name="search" />
					<div id="trail">
						<?php if (!$this->countModules('welcome')) : ?>
						<jdoc:include type="modules" name="breadcrumbs" />
						<?php else: ?>
						<span class="pathway"><?php echo JText::_('TPL_HUBBASIC_TAGLINE'); ?></span>
						<?php endif; ?>
					</div><!-- / #trail -->
				</div><!-- / .inner -->
			</div><!-- / #sub-masthead -->

			<div id="splash">
				<div class="inner-wrap">
					<div class="inner">
						<div class="wrap">
						<?php if ($this->getBuffer('message')) : ?>
							<jdoc:include type="message" />
						<?php endif; ?>
							<jdoc:include type="modules" name="welcome" />
						</div><!-- / .wrap -->
					</div><!-- / .inner -->
				</div><!-- / .inner-wrap -->
			</div><!-- / #splash -->
		</div><!-- / #top -->

		<div id="wrap">
			<div id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
				<div class="inner">
					<a name="content" id="content-anchor"></a>
				<?php if ($this->countModules('left')) : ?>
					<div class="main section withleft">
						<div class="aside">
							<jdoc:include type="modules" name="left" />
						</div><!-- / #column-left -->
						<div class="subject">
				<?php endif; ?>
				<?php if ($this->countModules('right')) : ?>
					<div class="main section">
						<div class="aside">
							<jdoc:include type="modules" name="right" />
						</div><!-- / .aside -->
						<div class="subject">
				<?php endif; ?>
							<!-- start component output -->
							<jdoc:include type="component" />
							<!-- end component output -->
				<?php if ($this->countModules('left or right')) : ?>
						</div><!-- / .subject -->
						<div class="clear"></div>
					</div><!-- / .main section -->
				<?php endif; ?>
				</div><!-- / .inner -->
			</div><!-- / #content -->

			<div id="footer">
				<a name="footer" id="footer-anchor"></a>
				<jdoc:include type="modules" name="footer" />
			</div><!-- / #footer -->
		</div><!-- / #wrap -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>