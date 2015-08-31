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

// Get browser info to set some classes
$browser = new \Hubzero\Browser\Detector();
$cls = array(
	'nojs',
	$browser->name(),
	$browser->name() . $browser->major(),
	$this->direction
);

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<title><?php echo Config::get('sitename') . ' - ' . $this->error->getCode(); ?></title>

		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css?v=<?php echo filemtime(__DIR__ . '/css/error.css'); ?>" />

		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
	</head>
	<body id="error-page">

		<div id="errorbox">
			<header id="masthead" role="banner">
				<h1>
					<a href="<?php echo empty($this->baseurl) ? '/' : $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
						<span><?php echo Config::get('sitename'); ?></span>
					</a>
				</h1>
				<p class="tagline"><?php echo Lang::txt('TPL_KIMERA_TAGLINE'); ?></p>
			</header>

			<main id="content" class="<?php echo 'code' . $this->error->getCode(); ?>" role="main">
				<div class="inner">
					<h2 class="error-code">
						<?php echo $this->error->getCode(); ?>
					</h2>

					<p class="error"><?php 
						if ($this->debug)
						{
							$message = $this->error->getMessage();
						}
						else
						{
							switch ($this->error->getCode())
							{
								case 404:
									$message = Lang::txt('TPL_KIMERA_404_HEADER');
									break;
								case 403:
									$message = Lang::txt('TPL_KIMERA_403_HEADER');
									break;
								case 500:
								default:
									$message = Lang::txt('TPL_KIMERA_500_HEADER');
									break;
							}
						}
						echo $message;
					?></p>
				</div><!-- / .inner -->
			</main><!-- / #content -->

			<footer id="footer">
				<p class="copyright">
					<?php echo Lang::txt('TPL_KIMERA_COPYRIGHT', Request::root(), Config::get('sitename'), date("Y")); ?>
				</p>
			</footer><!-- / #footer -->
		</div><!-- / #wrap -->

		<?php if ($this->debug) { ?>
			<div class="backtrace-wrap">
				<?php echo $this->renderBacktrace(); ?>
			</div>
		<?php } ?>
	</body>
</html>