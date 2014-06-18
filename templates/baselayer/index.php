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
 * @author    Ilya Shunko
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$config = JFactory::getConfig();
$juser  = JFactory::getUser();

// Include global scripts
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');

// Get browser info to set some classes
$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

// Find out if this is a front page
$app = JFactory::getApplication();
$menu = $app->getMenu();
$isFrontPage = false;
if ($menu->getActive() == $menu->getDefault() && $this->countModules('home-intro'))
{
	$isFrontPage = true;
}

// Prepend site name to document title
$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />

		<jdoc:include type="head" />
	</head>
	<body<?php if ($isFrontPage) : echo ' id="frontpage"'; endif; ?>>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<header id="page-header">
			<section class="top-wrapper cf">
				<div id="top" class="cf">
					<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>" class="logo">
						<p><?php echo $config->getValue('config.sitename'); ?></p>
						<p class="tagline hide-m"><?php echo JText::_('TPL_BASELAYER_TAGLINE'); ?></p>
					</a>

					<div id="mobile-nav" class="show-m">
						<ul>
							<li><a id="mobile-menu"><span><?php echo JText::_('TPL_BASELAYER_MENU'); ?></span></a></li>
							<li><a id="mobile-search"><span><?php echo JText::_('TPL_BASELAYER_SEARCH'); ?></span></a></li>
						</ul>
					</div>
				</div>

				<div id="search-box">
					<jdoc:include type="modules" name="search" />
				</div>
			</section>

			<nav id="main-navigation" role="main">
				<div class="wrapper cf">
					<div id="account">
					<?php if (!$juser->get('guest')) {
							$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
					?>
						<ul class="menu cf <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo $juser->get('name'); ?>" />
									<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
										<?php echo stripslashes($juser->get('name')); ?>
										<span class="account-email"><?php echo $juser->get('email'); ?></span>
									</a>
									<p class="account-logout">
										<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout'); ?>"><span><?php echo JText::_('TPL_BASELAYER_LOGOUT'); ?></span></a>
									</p>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>"><span><?php echo JText::_('TPL_BASELAYER_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>"><span><?php echo JText::_('TPL_BASELAYER_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>"><span><?php echo JText::_('TPL_BASELAYER_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout'); ?>"><span><?php echo JText::_('TPL_BASELAYER_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li id="account-login">
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>" title="<?php echo JText::_('TPL_BASELAYER_LOGIN'); ?>"><?php echo JText::_('TPL_BASELAYER_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo JRoute::_('index.php?option=com_members&controller=register'); ?>" title="<?php echo JText::_('TPL_BASELAYER_SIGN_UP'); ?>"><?php echo JText::_('TPL_BASELAYER_REGISTER'); ?></a>
							</li>
						</ul>
					<?php } ?>
					</div><!-- / #account -->

					<div id="main-nav">
						<jdoc:include type="modules" name="user3" />
					</div>
				</div><!-- / #wrapper -->
			</nav>

			<?php if (!$isFrontPage) : ?>
				<div id="trail">
					<jdoc:include type="modules" name="breadcrumbs" />
				</div><!-- / #trail -->
			<?php endif; ?>
		</header>

		<?php if ($this->countModules('home-intro')) : ?>
			<div id="home-intro">
				<jdoc:include type="modules" name="home-intro" />
			</div>
		<?php endif; ?>

		<main id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
			<div class="inner">
				<?php if ($this->countModules('left or right')) : ?>
					<section class="main section cf">
						<div class="section-inner">
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
						</section><!-- / .section-inner -->
					</section><!-- / .main section -->
				<?php endif; ?>
			</div>
		</main><!-- / #content -->

		<footer id="footer">
			<div class="wrapper">
				<jdoc:include type="modules" name="footer" />

				<div id="hubzero-proud-branding">
					<p><?php echo JText::_('TPL_BASELAYER_COPYRIGHT'); ?></p>
				</div>
			</div>
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>