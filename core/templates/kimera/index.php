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

defined('_HZEXEC_') or die();

Html::behavior('framework', true);
Html::behavior('modal');

// Include global scripts
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

// Load theme
$color1   = str_replace('#', '', $this->params->get('colorPrimary', '2f8dc9')); // 2f8dc9  171a1f
$opacity1 = $this->params->get('colorPrimaryOpacity', '');
$color2   = str_replace('#', '', $this->params->get('colorSecondary', '2f8dc9'));
$opacity2 = $this->params->get('colorSecondaryOpacity', '');
$bground  = $this->params->get('backgroundImage', $this->params->get('background', 'delauney'));

$hash = md5($color1 . $bground . $color2);
$p = substr(PATH_APP, strlen(PATH_ROOT));
$path = '/templates/' . $this->template . '/css/theme.php?path=' . urlencode($p) . '&c1=' . urlencode($color1) . '&c2=' . urlencode($color2) . '&bg=' . urlencode($bground) . ($opacity1 ? '&o1=' . $opacity1 : '') . ($opacity2 ? '&o2=' . $opacity2 : '');
if (file_exists(PATH_APP . '/cache/site/' . $hash . '.css'))
{
	$path = '/cache/site/' . $hash . '.css';
}

$this->addStyleSheet($this->baseurl . $path);

// Get browser info to set some classes
$menu = App::get('menu');
$browser = new \Hubzero\Browser\Detector();
$cls = array(
	'no-js',
	$browser->name(),
	$browser->name() . $browser->major(),
	$this->direction,
	$this->params->get('header', 'light'),
	($menu->getActive() == $menu->getDefault() ? 'home' : '')
);

// Prepend site name to document title
$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/index.css?v=<?php echo filemtime(__DIR__ . '/css/index.css'); ?>" />

		<jdoc:include type="head" />

		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
	</head>
	<body>
		<div id="outer-wrap">
			<jdoc:include type="modules" name="helppane" />

			<div id="top">
				<div id="splash">
					<div class="inner-wrap">

						<header id="masthead" role="banner">
							<jdoc:include type="modules" name="notices" />

							<h1>
								<a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
									<span><?php echo Config::get('sitename'); ?></span>
								</a>
							</h1>

							<nav id="account" class="account-navigation" role="navigation">
								<ul>
									<li>
										<a class="icon-search" href="<?php echo Route::url('index.php?option=com_search'); ?>" title="<?php echo Lang::txt('TPL_KIMERA_SEARCH'); ?>"><?php echo Lang::txt('Search'); ?></a>
										<jdoc:include type="modules" name="search" />
									</li>
								<?php if (!User::isGuest()) { ?>
									<li class="loggedin">
										<a href="<?php echo Route::url(User::link()); ?>">
											<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" width="30" height="30" />
											<span class="account-details">
												<?php echo stripslashes(User::get('name')); ?> 
												<span class="account-email"><?php echo User::get('email'); ?></span>
											</span>
										</a>
										<ul>
											<li id="account-dashboard">
												<a href="<?php echo Route::url(User::link() . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_KIMERA_ACCOUNT_DASHBOARD'); ?></span></a>
											</li>
											<li id="account-profile">
												<a href="<?php echo Route::url(User::link() . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_KIMERA_ACCOUNT_PROFILE'); ?></span></a>
											</li>
											<li id="account-logout">
												<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_KIMERA_LOGOUT'); ?></span></a>
											</li>
										</ul>
									</li>
								<?php } else { ?>
									<li>
										<a class="icon-login" href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_KIMERA_LOGIN'); ?>"><?php echo Lang::txt('TPL_KIMERA_LOGIN'); ?></a>
									</li>
									<?php if ($this->params->get('registerLink') && Component::params('com_users')->get('allowUserRegistration')) : ?>
										<li>
											<a class="icon-register" href="<?php echo Route::url('index.php?option=com_register'); ?>" title="<?php echo Lang::txt('TPL_KIMERA_SIGN_UP'); ?>"><?php echo Lang::txt('TPL_KIMERA_REGISTER'); ?></a>
										</li>
									<?php endif; ?>
								<?php } ?>
								</ul>
							</nav>

							<nav id="nav" class="main-navigation" role="main">
								<jdoc:include type="modules" name="user3" />
							</nav>
						</header>

						<div id="sub-masthead">
							<?php if ($this->countModules('helppane')) : ?>
								<p id="tab">
									<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('TPL_KIMERA_NEED_HELP'); ?>">
										<span><?php echo Lang::txt('TPL_KIMERA_HELP'); ?></span>
									</a>
								</p>
							<?php endif; ?>

							<div id="trail">
								<?php if ($menu->getActive() == $menu->getDefault()) : ?>
									<span class="pathway"><?php echo Lang::txt('TPL_KIMERA_TAGLINE'); ?></span>
								<?php else: ?>
									<jdoc:include type="modules" name="breadcrumbs" />
								<?php endif; ?>
							</div>
						</div><!-- / #sub-masthead -->

						<div class="inner">
							<div class="wrap">
							<?php if ($this->getBuffer('message')) : ?>
								<jdoc:include type="message" />
							<?php endif; ?>
								<jdoc:include type="modules" name="welcome" />
							</div>
						</div><!-- / .inner -->

					</div><!-- / .inner-wrap -->
				</div><!-- / #splash -->
			</div><!-- / #top -->

			<div id="wrap">
				<main id="content" class="<?php echo Request::getCmd('option', ''); ?>" role="main">
					<div class="inner<?php if ($this->countModules('left or right')) { echo ' withmenu'; } ?>">
					<?php if ($this->countModules('left or right')) : ?>
						<section class="main section">
							<div class="section-inner">
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
							</div>
						</section><!-- / .main section -->
					<?php endif; ?>
					</div><!-- / .inner -->
				</main>

				<footer id="footer">
					<jdoc:include type="modules" name="footer" />
				</footer>
			</div><!-- / #wrap -->
		</div>
		<jdoc:include type="modules" name="endpage" />
	</body>
</html>