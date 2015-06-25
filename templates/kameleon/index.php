<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/index.css?v=' . filemtime(__DIR__ . DS . 'css' . DS . 'index.css'));
// Load theme
if ($theme = $this->params->get('theme'))
{
	if ($theme == 'custom')
	{
		$color = $this->params->get('color');
		$this->addStyleDeclaration(include_once(__DIR__ . DS . 'css' . DS . 'themes' . DS . 'custom.php'));
	}
	else if ($theme != 'gray')
	{
		$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/themes/' . $theme . '.css');
	}
}
// Load language direction CSS
if ($this->direction == 'rtl')
{
	$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/common/rtl.css');
}

$htheme = $this->params->get('header', 'light');
$browser = new \Hubzero\Browser\Detector();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $htheme; ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $htheme; ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $htheme; ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $htheme; ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $htheme . ' ' . $browser->name() . ' ' . $browser->name() . $browser->major(); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />

		<jdoc:include type="head" />

		<script type="text/javascript" src="<?php echo str_replace('/administrator', '', $this->baseurl); ?>/core/assets/js/jquery.uniform.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/placeholder.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/index.js"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
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
				if ($this->direction == "rtl") :
					$output = array_reverse($output);
				endif;

				// Output the items.
				foreach ($output as $item) :
					echo $item;
				endforeach;
				?>
			</ul>
		</header><!-- / #header -->

		<div id="wrap">
			<nav role="navigation" class="main-navigation">
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
					<nav role="navigation" class="sub-navigation">
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
		</div><!-- / #wrap -->

		<footer id="footer">
			<section class="basement">
				<p class="copyright">
					<?php echo Lang::txt('TPL_KAMELEON_COPYRIGHT', Request::root(), Config::get('sitename'), date("Y")); ?>
				</p>
				<p class="promotion">
					<?php echo Lang::txt('TPL_KAMELEON_POWERED_BY', App::version()); ?>
				</p>
			</section><!-- / .basement -->
		</footer><!-- / #footer -->
	</body>
</html>