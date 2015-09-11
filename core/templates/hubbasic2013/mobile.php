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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge" /><![endif]-->

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
			<header id="masthead" role="banner">
				<div class="inner">
					<h1>
						<a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
							<span><?php echo Config::get('sitename'); ?></span>
						</a>
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
			</header><!-- / #masthead -->
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

			<footer id="footer" class="mobile-footer">
				<a id="footer-anchor" href="<?php echo Request::base(true); ?>?tmpl=fullsite"><?php echo Lang::txt('View Full Site'); ?></a>
			</footer><!-- / #footer -->
		</div><!-- / #wrap -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>