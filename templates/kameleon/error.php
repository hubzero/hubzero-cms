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

$config = JFactory::getConfig();

$this->template = 'kameleon';

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$lang = JFactory::getLanguage();
$lang->load('tpl_' . $this->template);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title><?php echo $config->getValue('config.sitename') . ' - ' . (in_array($this->error->getCode(), array(404, 403, 500)) ? $this->error->getCode() : 500); ?></title>

		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css?v=<?php echo filemtime(JPATH_ROOT . '/templates/' . $this->template . '/css/error.css'); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/media/cms/css/debug.css" />

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
	</head>
	<body id="error-page">

		<div id="wrap">
			<header id="masthead" role="banner">
				<h1>
					<a href="<?php echo empty($this->baseurl) ? '/' : $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
						<span><?php echo $config->getValue('config.sitename'); ?></span>
					</a>
				</h1>
			</header>

			<main id="content" class="<?php echo 'code' . $this->error->getCode(); ?>" role="main">
				<div class="inner">

					<section class="main section">
						<div class="grid">
							<div class="col span6">
								<div id="errormessage">
									<h2 class="error-code">
										<?php echo (in_array($this->error->getCode(), array(404, 403, 500))) ? $this->error->getCode() : 500; ?>
									</h2>
								</div><!-- / #errormessage -->
							</div><!-- / .two columns first -->
							<div class="col span6 omega">
								<div id="errorbox">
									<div class="wrap">
									<?php 
									switch ($this->error->getCode())
									{
										case 404: ?>
										<h3><?php echo JText::_('TPL_KAMELEON_404_HEADER'); ?></h3>
										<blockquote>
											<p><?php echo JText::_('TPL_KAMELEON_404_MESSAGE'); ?></p>
										</blockquote>
										<p class="signature"><?php echo JText::_('TPL_KAMELEON_404_SIGNATURE'); ?></p>
										<?php 
										break;
										case 403: ?>
										<h3><?php echo JText::_('TPL_KAMELEON_HEADER_403'); ?></h3>
										<blockquote>
											<p><?php echo JText::_('TPL_KAMELEON_403_MESSAGE'); ?></p>
										</blockquote>
										<p class="signature"><?php echo JText::_('TPL_KAMELEON_403_SIGNATURE'); ?></p>
										<?php 
										break;
										case 500: 
										default: ?>
										<h3><?php echo JText::_('TPL_KAMELEON_HEADER_500'); ?></h3>
										<blockquote>
											<p><?php echo JText::_('TPL_KAMELEON_500_MESSAGE'); ?></p>
										</blockquote>
										<p class="signature"><?php echo JText::_('TPL_KAMELEON_500_SIGNATURE'); ?></p>
										<?php 
										break;
									} ?>
									</div><!-- / .wrap -->
								</div><!-- / #errorbox -->
							</div><!-- / .two columns second -->
						</div>
					</section><!-- / .main section -->

				<?php if ($this->debug) { ?>
					<footer id="footer">
						<p class="error">
							<?php echo $this->error->getMessage(); ?>
						</p>
						<div class="backtrace-wrap">
							<?php echo $this->renderBacktrace(); ?>
						</div>
					</footer><!-- / #footer -->
				<?php } ?>
				</div><!-- / .inner -->
			</main><!-- / #content -->
		</div><!-- / #wrap -->

	</body>
</html>