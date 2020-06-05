<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);

$code = (is_numeric($this->error->getCode()) && $this->error->getCode() > 100 ? $this->error->getCode() : 500);

$browser = new \Hubzero\Browser\Detector();

$cls = array(
	'nojs',
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width" />

		<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css?v=<?php echo filemtime(__DIR__ . '/css/error.css'); ?>" />

		<title><?php echo Lang::txt('TPL_KAMELEON_ERROR_OCCURRED') . ' - ' . $code; ?></title>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js?v=<?php echo filemtime(__DIR__ . '/js/html5.js'); ?>"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css?v=<?php echo filemtime(__DIR__ . '/css/browser/ie9.css'); ?>" />
		<![endif]-->
	</head>
	<body id="error-body">
		<header id="header" role="banner">
			<h1><a href="<?php echo Request::root(); ?>"><?php echo Config::get('sitename'); ?></a></h1>
		</header><!-- / #header -->

		<div id="wrap">
			<nav role="navigation" class="main-navigation">
				<a class="btn dashboard" href="<?php echo Route::url('index.php'); ?>"><?php echo Lang::txt('TPL_KAMELEON_CONTROL_PANEL') ?></a>
				<a class="btn help" href="<?php echo Route::url('index.php?option=com_help'); ?>"><?php echo Lang::txt('TPL_KAMELEON_HELP'); ?></a>
			</nav><!-- / .main-navigation -->

			<main id="component-content">
				<div id="errorbox">
					<h2 class="error-code"><?php echo $code; ?></h2>
					<p class="error"><?php echo $this->error->getMessage(); ?></p>

					<noscript>
						<?php echo Lang::txt('JGLOBAL_WARNJAVASCRIPT') ?>
					</noscript>
				</div>

				<?php if ($this->debug) { ?>
					<div class="backtrace-wrap">
						<?php echo $this->renderBacktrace(); ?>
					</div>
				<?php } ?>
			</main><!-- / #component-content -->
		</div><!-- / #wrap -->

		<footer id="footer">
			<section class="basement">
				<p class="copyright">
					<?php echo Lang::txt('TPL_KAMELEON_COPYRIGHT', Request::root(), Config::get('sitename'), date("Y")); ?>
				</p>
				<p class="promotion">
					<?php echo Lang::txt('TPL_KAMELEON_POWERED_BY', HVERSION); ?>
				</p>
			</section>
		</footer><!-- / #footer -->
	</body>
</html>