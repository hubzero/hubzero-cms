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
 * @author    Ilya shunko
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$config = JFactory::getConfig();
$juser  = JFactory::getUser();

$this->template = 'baselayer';

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

$lang = JFactory::getLanguage();
$lang->load('tpl_' . $this->template);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo end($cls); ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width">

		<title><?php echo $config->getValue('config.sitename') . ' - ' . (in_array($this->error->getCode(), array(404, 403, 500)) ? $this->error->getCode() : 500); ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.js"></script>
	</head>
	<body>
		<?php \Hubzero\Module\Helper::displayModules('notices'); ?>
		<?php \Hubzero\Module\Helper::displayModules('helppane'); ?>

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
					<?php \Hubzero\Module\Helper::displayModules('search'); ?>
				</div>
			</section>

			<nav id="main-navigation" class="hide-m" role="main">
				<div class="wrapper cf">
					<div id="account">
					<?php if (!$juser->get('guest')) {
							$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
					?>
						<ul class="menu cf <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($profile); ?>" alt="<?php echo $juser->get('name'); ?>" />
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
						<?php \Hubzero\Module\Helper::displayModules('user3'); ?>
					</div>
				</div><!-- / #wrapper -->
			</nav>

			<div id="trail">
				<div class="breadcrumbs">
					<a href="/" title="<?php echo $config->getValue('config.sitename'); ?>">Home</a>

					<?php
						echo ' → <span>' . $this->error->getMessage() . '</span>';
					?>
				</div>
			</div>
		</header>

		<main id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
			<div class="section-inner">
				<div class="grid">
					<div class="col span6 error-code">
						<?php echo (in_array($this->error->getCode(), array(404, 403, 500))) ? $this->error->getCode() : 500; ?>
					</div>
					<div class="col span6 omega">
						<h2><?php echo $this->error->getMessage() ?></h2>

						<p><?php echo JText::_('TPL_BASELAYER_ERROR_BECAUSE'); ?></p>

						<ol>
						<?php if ($this->error->getCode() != 403) { ?>
							<li><?php echo JText::_('TPL_BASELAYER_404_REASON1'); ?></li>
							<li><?php echo JText::_('TPL_BASELAYER_404_REASON2'); ?></li>
							<li><?php echo JText::_('TPL_BASELAYER_404_REASON3'); ?></li>
							<li><?php echo JText::_('TPL_BASELAYER_404_REASON4'); ?></li>
						<?php } ?>
							<li><?php echo JText::_('TPL_BASELAYER_403_REASON1'); ?></li>
							<li><?php echo JText::_('TPL_BASELAYER_403_REASON2'); ?></li>
						</ol>
						<?php if ($this->error->getCode() != 403) { ?>
							<p><?php echo JText::_('TPL_BASELAYER_404_MESSAGE'); ?></p>
						<?php } else { ?>
							<p><?php echo JText::_('TPL_BASELAYER_403_MESSAGE'); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</main>

		<footer id="footer">
			<div class="wrapper">
				<?php \Hubzero\Module\Helper::displayModules('footer'); ?>

				<div id="hubzero-proud-branding">
					<p><?php echo JText::_('TPL_BASELAYER_COPYRIGHT'); ?></p>
				</div>
			</div>
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>