<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$config = JFactory::getConfig();

//define tempate
$this->template = 'hubbasic2012';

//get device info
$hd = new \Hubzero\Browser\Detector();

//get joomla version
$joomlaVersion = new JVersion();
$joomlaRelease = 'joomla' . $joomlaVersion->RELEASE;
?>
<!DOCTYPE html>
<html class="<?php echo strtolower($hd->device() . ' ' . $hd->platform() . ' ' . $hd->platformVersion()); ?> <?php echo $joomlaRelease; ?>">
	<head>
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
			<div class="inner-wrap">
				<div class="inner">
					<header id="masthead" role="banner">
						<div class="inner">
							<h1>
								<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
									<span><?php echo $config->getValue('config.sitename'); ?></span>
								</a>
								<span class="tagline"><?php echo JText::_('TPL_HUBBASIC_TAGLINE'); ?></span>
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
			<main id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
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
			<a href="?tmpl=fullsite">View Full Site</a>
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
