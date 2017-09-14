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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/login.css?v=' . filemtime(__DIR__ . DS . 'css' . DS . 'login.css'));
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

$browser = new \Hubzero\Browser\Detector();
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $browser->name() . ' ' . $browser->name() . $browser->major(); ?>">
	<head>
		<meta name="viewport" content="width=device-width" />

		<jdoc:include type="head" />

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/placeholder.js"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->

		<script type="text/javascript">
			jQuery(document).ready(function($){
				(function worker() {
					$.ajax({
						url: 'index.php',
						complete: function() {
							setTimeout(worker, 3540000);
						}
					});
				})();

				if (document.getElementById('form-login')) {
					document.getElementById('form-login').username.select();
					document.getElementById('form-login').username.focus();
				}

				$('input, textarea').placeholder();
			});
		</script>
	</head>
	<body id="login-body">
		<div id="bg-canvas-wrapper">
			<canvas id="bg-canvas"></canvas>
		</div>

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

		<?php
		if ($this->params->get('login'))
		{
			$m = 11;
			if ($this->params->get('login') == 2)
			{
				$m = intval(Date::format('m'));
			}
			if ($m < 4 || $m > 10)
			{
				?><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/snow.js"></script><?php
			}
			if ($m > 3 && $m < 6)
			{
				?><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/rain.js"></script><?php
			}
		}
		?>
	</body>
</html>