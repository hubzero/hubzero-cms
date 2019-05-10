<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No Direct Access
defined('_HZEXEC_') or die();

//define tempate
$this->template = 'bmc';

//get device info
$browser = new \Hubzero\Browser\Detector();

?>
<!DOCTYPE html>
<html class="<?php echo strtolower($browser->device() . ' ' . $browser->platform() . ' ' . $browser->platformVersion()); ?>">
	<head>
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'tooltip', 'comments', 'voting', 'icons', 'buttons', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
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
						<a href="<?php echo $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
							<span><?php echo Config::get('sitename'); ?></span>
						</a>
					</h1>
					<div class="mobile-search">
						<jdoc:include type="modules" name="search" />
					</div>
					<div id="nav" role="main navigation">
						<a name="nav"></a>
						<jdoc:include type="modules" name="mainmenu" />
						<jdoc:include type="modules" name="secondarymenu" />
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
				<a name="footer" id="footer-anchor"></a>
				<a href="<?php echo $_SERVER['SCRIPT_URI']; ?>?tmpl=fullsite">View Full Site</a>
			</footer><!-- / #footer -->
		</div><!-- / #wrap -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
