<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/login.css?v=' . filemtime(__DIR__ . '/css/login.css'));

// Load theme
$theme = $this->params->get('theme');
if ($theme == 'custom')
{
	$color = $this->params->get('color');
	$this->addStyleDeclaration(include_once __DIR__ . '/css/themes/custom.php');
}

$browser = new \Hubzero\Browser\Detector();

$cls = array(
	'nojs',
	$this->direction,
	$theme,
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width" />

		<jdoc:include type="head" />

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/login.js?v=<?php echo filemtime(__DIR__ . '/js/login.js'); ?>"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js?v=<?php echo filemtime(__DIR__ . '/js/html5.js'); ?>"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css?v=<?php echo filemtime(__DIR__ . '/css/browser/ie9.css'); ?>" />
		<![endif]-->
	</head>
	<body id="login-body">
		<jdoc:include type="modules" name="notices" />

		<header id="header" role="banner">
			<h1><a href="<?php echo Request::root(); ?>"><?php echo Config::get('sitename'); ?></a></h1>
		</header><!-- / #header -->

		<div id="wrap">
			<section id="component-content">
				<div id="toolbar-box">
					<h2><?php echo Lang::txt('TPL_KAMELEON_ADMIN_LOGIN'); ?></h2>
				</div>

				<section id="main" class="<?php echo Request::getCmd('option', ''); ?>">
					<!-- Notifications begins -->
					<jdoc:include type="message" />
					<!-- Notifications ends -->

					<!-- Content begins -->
					<jdoc:include type="component" />
					<!-- Content ends -->

					<noscript>
						<?php echo Lang::txt('JGLOBAL_WARNJAVASCRIPT') ?>
					</noscript>
				</section><!-- / #main -->
			</section><!-- / #component-content -->
		</div><!-- / #wrap -->
	</body>
</html>