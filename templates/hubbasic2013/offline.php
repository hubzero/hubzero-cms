<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$config =& JFactory::getConfig();

$this->template = 'hubbasic2013';

$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

$this->setTitle($config->getValue('config.sitename') . ' - ' . JText::_('Down for maintenance'));
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/offline.css" />
	</head>
	<body>

		<div id="container">
			<div id="top">
				<div id="masthead" role="banner">
					<div class="inner">
						<h1>
							<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
								<span><?php echo $config->getValue('config.sitename'); ?></span>
							</a>
						</h1>
					</div>
				</div>

				<div id="sub-masthead">
					<div class="inner">
						<div id="trail">
							<span class="pathway"><?php echo JText::_('TPL_HUBBASIC_TAGLINE'); ?></span>
						</div><!-- / #trail -->
					</div><!-- / .inner -->
				</div><!-- / #sub-masthead -->

				<div id="splash">
					<div class="inner-wrap">
						<div class="inner">
							<div class="wrap">
								<jdoc:include type="message" />
								<div id="offline-message">
									<h2><?php echo JText::_('TPL_HUBBASIC_OFFLINE'); ?></h2>
									<p>
										<?php echo $mainframe->getCfg('offline_message'); ?>
									</p>
								</div>
							</div><!-- / .wrap -->
						</div><!-- / .inner -->
					</div><!-- / .inner-wrap -->
				</div><!-- / #splash -->
			</div><!-- / #top -->

			<div id="wrap">
		 		<div id="footer">
					<div class="inner">
						<ul id="legalese">
							<li class="policy">Copyright &copy; <?php echo date("Y"); ?> <?php echo $config->getValue('config.sitename'); ?></li>
							<li>Powered by <a href="http://hubzero.org" rel="external">HUBzero<sup>&reg;</sup></a>, a <a href="http://www.purdue.edu" title="Purdue University" rel="external">Purdue</a> project</li>
						</ul><!-- / footer #legalese -->
					</div>
				</div><!-- / #footer -->
			</div><!-- / #wrap -->
		</div><!-- / #container -->
	</body>
</html>