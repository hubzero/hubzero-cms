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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = JRequest::getCmd('option');
$config = JFactory::getConfig();
$juser  = JFactory::getUser();

JHTML::_('behavior.framework', true);
JHTML::_('behavior.modal');

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(JPATH_ROOT . '/templates/' . $this->template . '/js/hub.js'));

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body <?php if ($this->countModules('banner or welcome')) { echo 'class="frontpage"'; } ?>>
		<jdoc:include type="modules" name="notices" />

		<div id="top">
			<p class="skip" id="to-content"><a href="#content">Skip to content</a></p>
			<?php if ($this->countModules('helppane')) : ?>
				<p id="tab">
					<a href="<?php echo JRoute::_('index.php?option=com_support'); ?>" title="Need help? Send a trouble report to our support team.">
						<span>Need Help?</span>
					</a>
				</p>
			<?php endif; ?>
			<div class="clear"></div>
		</div><!-- / #top -->

		<jdoc:include type="modules" name="helppane" />

		<header id="header">
			<div id="header-wrap">
				<a name="header"></a>
				<h1>
					<a href="<?php echo $this->baseurl ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
						<?php echo $config->getValue('config.sitename'); ?>
						<span id="tagline">powered by HUBzero&reg;</span>
					</a>
				</h1>

				<ul id="toolbar" class="<?php if (!$juser->get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
					<?php
						if (!$juser->get('guest')) {
							// Find the user's most recent support tickets
							$database = JFactory::getDBO();
							$recipient = new \Hubzero\Message\Recipient($database);
							$rows = $recipient->getUnreadMessages($juser->get('id'), 0);
							?>
							<li id="logout">
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout'); ?>"><span><?php echo JText::_('Logout'); ?></span></a>
							</li>
							<li id="myaccount">
								<a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>"><span><?php echo JText::_('My Dashboard'); ?></span></a>
							</li>
							<?php if (is_numeric($juser->get('username')) && $juser->get('username') < 0) { ?>
								<li id="username">
									<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=profile'); ?>"><?php echo $juser->get('name'); ?></a>
								</li>
							<?php } else { ?>
								<li id="username">
									<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=profile'); ?>"><?php echo $juser->get('name'); ?> (<?php echo $juser->get('username'); ?>)</a>
								</li>
							<?php } ?>
							<li id="usermessages">
								<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=inbox'); ?>"><?php echo count($rows); ?> New Messages</a>
							</li>
					<?php } else { ?>
						<li id="login">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>" title="<?php echo JText::_('Login'); ?>"><?php echo JText::_('Sign In'); ?></a>
						</li>
					<?php } ?>
				</ul>

				<jdoc:include type="modules" name="search" />
			</div><!-- / #header-wrap -->
		</header><!-- / #header -->

		<nav id="nav">
			<h2>Navigation</h2>
			<jdoc:include type="modules" name="user3" />
			<div class="clear"></div>
		</nav><!-- / #nav -->

		<?php if ($this->countModules('banner or welcome') && $option == 'com_content') : ?>
			<div id="home-splash">
				<div id="features">
					<?php if ($this->countModules('banner')) : ?>
						<jdoc:include type="modules" name="banner" />
					<?php endif; ?>
				</div><!-- / #features -->
				<?php if ($this->countModules('welcome')) : ?>
					<div id="welcome">
						<jdoc:include type="modules" name="welcome" />
					</div><!-- / #welcome -->
				<?php endif; ?>
			</div><!-- / #home-splash -->
		<?php endif; ?>

		<?php if (!$this->countModules('banner or welcome')) : ?>
			<div id="trail">
				<jdoc:include type="modules" name="breadcrumbs" />
			</div><!-- / #trail -->
		<?php endif; ?>

		<div id="wrap">
			<main id="content" class="<?php echo $option; ?>">
				<div id="content-wrap">
					<?php if ($this->getBuffer('message')) : ?>
						<jdoc:include type="message" />
					<?php endif; ?>
					<?php if ($this->countModules('left or right')) : ?>
						<section class="main section">
					<?php endif; ?>

					<?php if ($this->countModules('left')) : ?>
							<aside class="aside">
								<jdoc:include type="modules" name="left" />
							</aside><!-- / .aside -->
					<?php endif; ?>
					<?php if ($this->countModules('left or right')) : ?>
							<div class="subject">
					<?php endif; ?>

								<!-- start component output -->
								<jdoc:include type="component" />
								<!-- end component output -->

					<?php if ($this->countModules('left or right')) : ?>
							</div><!-- / .subject -->
					<?php endif; ?>
					<?php if ($this->countModules('right')) : ?>
							<aside class="aside">
								<jdoc:include type="modules" name="right" />
							</aside><!-- / .aside -->
					<?php endif; ?>

					<?php if ($this->countModules('left or right')) : ?>
						</section><!-- / .main section -->
					<?php endif; ?>
				</div><!-- / #content-wrap -->
			</main><!-- / #content -->
		</div><!-- / #wrap -->

		<footer id="footer">
			<!-- Start footer modules output -->
			<jdoc:include type="modules" name="footer" />
			<!-- End footer modules output -->
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
