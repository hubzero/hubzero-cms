<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

//define tempate
$this->template = 'hubbasic2012';

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/mobile.css" />
		<jdoc:include type="head" />
		<script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/mobile.js"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />
		<div id="top" class="mobile-top">
			<div class="inner-wrap">
				<div class="inner">
					<header id="masthead" role="banner">
						<div class="inner">
							<h1>
								<a href="<?php echo $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
									<span><?php echo Config::get('sitename'); ?></span>
								</a>
								<span class="tagline"><?php echo Lang::txt('TPL_HUBBASIC_TAGLINE'); ?></span>
							</h1>

							<div class="mobile-search">
								<jdoc:include type="modules" name="search" />
							</div>

							<div id="nav" role="main navigation">
								<a name="nav"></a>
								<jdoc:include type="modules" name="user3" />
							</div><!-- / #nav -->
							<select name="menu" id="mobile-nav">
							</select>
						</div><!-- / .inner -->
					</header><!-- / #header -->
				</div><!-- / .inner -->
			</div><!-- / .inner-wrap -->
		</div><!-- / #top -->
		<div id="wrap" class="mobile-wrap">
			<main id="content" class="<?php echo Request::getVar('option', ''); ?>" role="main">
				<div class="inner">
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
				</div><!-- / .inner -->
			</main><!-- / #content -->
		</div><!-- / #wrap -->

		<footer id="footer" class="mobile-footer">
			<a id="footer-anchor" href="<?php echo Request::base(true); ?>?tmpl=fullsite"><?php echo Lang::txt('View Full Site'); ?></a>
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
