<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/index.css?v=' . filemtime(__DIR__ . '/css/index.css'));

// Load theme
$theme = $this->params->get('theme');
if ($theme == 'custom')
{
	$color = $this->params->get('color');
	$this->addStyleDeclaration(include_once __DIR__ . '/css/themes/custom.php');
}

$htheme = $this->params->get('header', 'light');
$browser = new \Hubzero\Browser\Detector();

$cls = array(
	'nojs',
	$this->direction,
	$theme,
	$htheme,
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

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/index.js?v=<?php echo filemtime(__DIR__ . '/js/index.js'); ?>"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js?v=<?php echo filemtime(__DIR__ . '/js/html5.js'); ?>"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css?v=<?php echo filemtime(__DIR__ . '/css/browser/ie9.css'); ?>" />
		<![endif]-->
	</head>
	<body id="minwidth-body">
		<jdoc:include type="modules" name="notices" />

		<header id="header" role="banner">
			<h1><a href="<?php echo Request::root(); ?>"><?php echo Config::get('sitename'); ?></a></h1>

			<ul class="user-options">
				<?php
				//Display an harcoded logout
				$task = Request::getCmd('task');
				$hideLinks = Request::getBool('hidemainmenu');

				$logoutLink = Route::url('index.php?option=com_login&task=logout&'. Session::getFormToken() .'=1');
				if ($task == 'edit' || $task == 'editA' || $hideLinks) :
					$logoutLink = '';
				endif;

				$output = array();
				$output[] = ($hideLinks
								? '<li class="disabled" data-title="' . Lang::txt('TPL_KAMELEON_LOG_OUT') . '"><span class="logout">'
								: '<li data-title="' . Lang::txt('TPL_KAMELEON_LOG_OUT') . '"><a class="logout" href="' . $logoutLink . '">') . Lang::txt('TPL_KAMELEON_LOG_OUT') . ($hideLinks ? '</span></li>' : '</a></li>');

				// Reverse rendering order for rtl display.
				if ($this->direction == 'rtl') :
					$output = array_reverse($output);
				endif;

				// Output the items.
				foreach ($output as $item) :
					echo $item;
				endforeach;
				?>
			</ul>
		</header><!-- / #header -->

		<main id="wrap">
			<nav role="navigation" class="main-navigation" aria-label="<?php echo Lang::txt('TPL_KAMELEON_MAIN_NAV'); ?>">
				<div class="inner-wrap">
					<jdoc:include type="modules" name="menu" />
				</div>
			</nav><!-- / .main-navigation -->

			<section id="component-content">
				<div id="toolbar-box" class="toolbar-box">
					<jdoc:include type="modules" name="title" />
					<jdoc:include type="modules" name="toolbar" />
				</div><!-- / #toolbar-box -->

				<!-- Notifications begins -->
				<jdoc:include type="message" />
				<!-- Notifications ends -->

				<?php if (!$hideLinks && $this->countModules('submenu')): ?>
					<nav role="navigation" class="sub-navigation" aria-label="<?php echo Lang::txt('TPL_KAMELEON_COMPONENT_NAV'); ?>">
						<jdoc:include type="modules" name="submenu" />
					</nav><!-- / .sub-navigation -->
				<?php endif; ?>

				<section id="main" class="<?php echo Request::getCmd('option', ''); ?>">
					<!-- Content begins -->
					<jdoc:include type="component" />
					<!-- Content ends -->

					<noscript>
						<?php echo Lang::txt('JGLOBAL_WARNJAVASCRIPT') ?>
					</noscript>
				</section><!-- / #main -->
			</section><!-- / #component-content -->
		</main><!-- / #wrap -->

		<footer id="footer">
			<section class="basement">
				<p class="copyright">
					<?php echo Lang::txt('TPL_KAMELEON_COPYRIGHT', Request::root(), Config::get('sitename'), Date::of('now')->format("Y")); ?>
				</p>
				<p class="promotion">
					<?php echo Lang::txt('TPL_KAMELEON_POWERED_BY', HVERSION); ?>
				</p>
			</section><!-- / .basement -->
		</footer><!-- / #footer -->
	</body>
</html>