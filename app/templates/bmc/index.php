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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No Direct Access
defined('_HZEXEC_') or die();

$menu = App::get('menu');
Html::behavior('framework', true);
Html::behavior('modal');

$this->addStylesheet($this->baseurl . '/templates/' . $this->template . '/css/site.css');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/hammer.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$menu = App::get('menu');
$isFrontPage = ($menu->getActive() == $menu->getDefault());

// Index page files only
$pageClass = 'page-' . Request::getVar('option', '');
if ($isFrontPage)
{
	$pageClass = 'page-home';
}

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);

// Some crazy stuff to match the current page to the subnav that is supposed to be shown. Basically, a lookup table mapping the component name to a main navigation item
// Then the javascript will get the corresponting subnav and will display it in the template

// Get the URL
$subnavUrl = trim(explode('?', $_SERVER['REQUEST_URI'], 2)[0], '/');
// Get the component
$subnavComponnent = Request::getVar('option', '');

// the lookup table (load it from plugin)
$subnavMap = Event::trigger('system.onSubnavRequest');

$showSubnav = 'none';
if (!empty($subnavMap))
{
	$subnavMap = $subnavMap[0];

	$pages = $subnavMap['url'];
	$components = $subnavMap['com'];

	// Find the page
	// first priority is URL
	if (array_key_exists($subnavUrl, $pages))
	{
		$showSubnav = $pages[$subnavUrl];
	}
	elseif (array_key_exists($subnavComponnent, $components))
	{
		$showSubnav = $components[$subnavComponnent];
	}
}

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />

		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/TweenMax.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/ScrollMagic.js"></script>
		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->
	</head>
	<body class="<?php echo $pageClass; ?>" data-component="<?php echo Request::getVar('option', ''); ?>" data-subnav="<?php echo $showSubnav; ?>">
		<div class="wrap">
			<div class="content-panel <?php echo $subnavComponnent; ?>">
				<?php
				$headerClass = '';
				if ($menu->getActive() != $menu->getDefault()) {
					$headerClass = 'with-sub';
				}
				?>
				<header class="page <?php echo $headerClass; ?>">
					<div class="wrap-main">
						<div class="main cf">
							<div class="brand">
								<a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
									<div class="logo">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 158.019 45.662"><g fill="#8DC63F"><path d="M30.165 4.78c-.04.137-.07.266-.117.405-.75 2.227-2.55 5.343-6.835 8.09l7.316 4.23v10.658l-7.32 4.225c4.283 2.75 6.085 5.864 6.83 8.09.068.202.115.39.17.584 6.693-3.295 11.31-10.164 11.31-18.13 0-7.983-4.638-14.865-11.355-18.15zM14.434 4.675c1.006 2.89 3.378 5.474 6.87 7.497 3.49-2.023 5.86-4.606 6.867-7.495.08-.23.143-.45.202-.667-2.204-.824-4.58-1.296-7.07-1.296s-4.866.472-7.07 1.295c.06.216.122.435.202.665z"/><path d="M14.004 27.044l7.295 4.21 7.29-4.21v-8.42L21.3 14.41l-7.296 4.213"/><path d="M12.553 40.48c.748-2.228 2.55-5.34 6.83-8.092l-7.317-4.225v-10.66l7.325-4.23c-4.282-2.75-6.084-5.865-6.83-8.09-.048-.14-.078-.27-.118-.404-6.72 3.285-11.36 10.168-11.36 18.153 0 7.962 4.614 14.83 11.305 18.127.052-.193.1-.38.166-.58zM28.165 40.984c-1.007-2.884-3.377-5.465-6.865-7.49h-.005c-3.488 2.025-5.86 4.606-6.866 7.49-.103.295-.186.578-.255.85 2.22.838 4.614 1.317 7.125 1.317 2.51 0 4.9-.478 7.118-1.313-.067-.273-.15-.557-.253-.853z"/></g><path fill="#7FB539" d="M21.3 14.366L13.966 18.6l7.334 4.234 7.335-4.235"/><path fill="#8DC63F" d="M13.967 27.066l7.333 4.236v-8.468L13.966 18.6"/><path fill="#6D9D31" d="M28.633 27.066l.002-8.467-7.335 4.234v8.468"/><g fill="#424143"><path d="M48.902 22.414c0-6.662 4.72-11.55 11.416-11.55 6.66 0 11.415 4.888 11.415 11.55 0 3.212-1.105 5.993-2.98 8.03l1.84 2.075-2.712 2.278-1.94-2.177c-1.64.872-3.547 1.342-5.62 1.342-6.7 0-11.418-4.887-11.418-11.548zm14.562 7.397l-2.778-3.145 2.744-2.276 2.78 3.145c.97-1.37 1.506-3.146 1.506-5.12 0-4.62-2.91-8.07-7.397-8.07-4.52 0-7.4 3.45-7.4 8.07 0 4.584 2.88 8.065 7.4 8.065 1.17 0 2.205-.235 3.144-.67zM75.485 24.723v-13.49h3.984v13.39c0 3.548 1.975 5.856 5.69 5.856 3.714 0 5.69-2.31 5.69-5.858v-13.39h3.984v13.49c0 5.522-3.18 9.24-9.675 9.24-6.46 0-9.675-3.717-9.675-9.24zM99.59 33.56V11.233h10.98c4.116 0 6.36 2.543 6.36 5.69 0 2.778-1.807 4.688-3.882 5.123 2.41.37 4.316 2.744 4.316 5.458 0 3.48-2.272 6.06-6.492 6.06h-11.28v-.004zm13.324-16.002c0-1.64-1.14-2.88-3.11-2.88h-6.297v5.76h6.296c1.972 0 3.11-1.17 3.11-2.88zm.433 9.44c0-1.67-1.167-3.113-3.382-3.113h-6.46v6.226h6.46c2.113 0 3.382-1.17 3.382-3.11zM121.32 33.56V11.233h15.295v3.446h-11.38v5.757h11.146v3.447h-11.145v6.226h11.38v3.45H121.32zM139.197 30.412l2.208-3.045c1.51 1.64 3.952 3.112 7.067 3.112 3.212 0 4.45-1.574 4.45-3.082 0-4.685-12.987-1.772-12.987-9.973 0-3.717 3.215-6.562 8.133-6.562 3.45 0 6.293 1.138 8.334 3.145l-2.207 2.913c-1.775-1.772-4.15-2.576-6.495-2.576-2.273 0-3.75 1.14-3.75 2.78 0 4.183 12.988 1.606 12.988 9.907 0 3.718-2.642 6.93-8.637 6.93-4.115.002-7.094-1.47-9.103-3.548z"/></g></svg>
									</div>
								</a>
							</div>
							<nav class="nav-primary">
								<jdoc:include type="modules" name="mainmenu" />
							</nav>
							<button class="mobile-menu">
								<div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><style>.st2{fill:#999999;}</style><path class="st2" d="M46.7 25.3L32.3 39.8 17.8 25.3c-.8-.8-2-.8-2.8 0-.8.8-.8 2 0 2.8L30.8 44c.2.2.6.4.9.5.2 0 .3.1.5.1.5 0 1-.2 1.4-.6l15.9-15.9c.8-.8.8-2 0-2.8s-2-.8-2.8 0z"/><path class="st2" d="M32 0C14.4 0 0 14.4 0 32s14.4 32 32 32 32-14.4 32-32S49.6 0 32 0zm0 60C16.6 60 4 47.4 4 32S16.6 4 32 4s28 12.6 28 28-12.6 28-28 28z"/></svg></div>
								<span>&nbsp;</span>
							</button>

							<div class="aux">
								<nav class="nav-secondary">
									<jdoc:include type="modules" name="secondarymenu" />
									<ul>
									  <li id="help" class="helpme">
											<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Need help? Send a trouble report to our support team.'); ?>">
												<span><?php echo Lang::txt('Help'); ?></span>
											</a>
										</li>
									</ul>
								</nav>
								<nav class="buttons">
									<div class="search">
										<a href="#">
											<div class="icon">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 475.1 475.1"><path d="M464.5 412.8l-97.9-97.9c23.6-34.1 35.4-72 35.4-113.9 0-27.2-5.3-53.2-15.9-78.1-10.6-24.8-24.8-46.3-42.8-64.2s-39.4-32.3-64.2-42.8C254.2 5.3 228.2 0 201 0c-27.2 0-53.2 5.3-78.1 15.8-24.8 10.6-46.2 24.9-64.2 42.9s-32.3 39.4-42.8 64.2C5.3 147.8 0 173.8 0 201c0 27.2 5.3 53.2 15.8 78.1 10.6 24.8 24.8 46.2 42.8 64.2 18 18 39.4 32.3 64.2 42.8 24.8 10.6 50.9 15.8 78.1 15.8 41.9 0 79.9-11.8 113.9-35.4l97.9 97.6c6.9 7.2 15.4 10.8 25.7 10.8 9.9 0 18.5-3.6 25.7-10.8 7.2-7.2 10.8-15.8 10.8-25.7.2-9.9-3.3-18.5-10.4-25.6zM291.4 291.4c-25 25-55.1 37.5-90.4 37.5-35.2 0-65.3-12.5-90.4-37.5-25-25-37.5-55.1-37.5-90.4 0-35.2 12.5-65.3 37.5-90.4 25-25 55.1-37.5 90.4-37.5 35.2 0 65.3 12.5 90.4 37.5 25 25 37.5 55.1 37.5 90.4 0 35.2-12.5 65.3-37.5 90.4z"/></svg>
											</div>
											&nbsp;
										</a>
									</div>
									<?php if (!User::isGuest()) : ?>
										<div class="dashboard loggedin">
											<a href="/login">
												<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" class="profile-pic thumb" />
											</a>
										</div>
									<?php else : ?>
										<div class="dashboard">
											<a href="#">
												<div class="icon">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 438.5 438.5"><path d="M414.4 60.7c-16.1-16.1-35.4-24.1-58.1-24.1H265c-2.5 0-4.4.6-5.9 1.9-1.4 1.2-2.4 3.1-2.9 5.6-.5 2.5-.8 4.7-.9 6.7-.1 2-.1 4.5.1 7.6.2 3 .3 4.9.3 5.7.6 1.5.8 2.8.6 3.9-.2 1 .5 1.9 2 2.6 1.5.7 2.3 1.2 2.3 1.6s1.1.7 3.3.9l3.3.3h89c12.6 0 23.3 4.5 32.3 13.4 9 8.9 13.4 19.7 13.4 32.3v201c0 12.6-4.5 23.3-13.4 32.3-8.9 8.9-19.7 13.4-32.3 13.4H265c-2.5 0-4.4.6-5.9 1.9-1.4 1.2-2.4 3.1-2.9 5.6-.5 2.5-.8 4.7-.9 6.7-.1 2-.1 4.5.1 7.6.2 3 .3 4.9.3 5.7 0 2.5.9 4.6 2.7 6.4 1.8 1.8 3.9 2.7 6.4 2.7h91.4c22.6 0 42-8 58.1-24.1s24.1-35.4 24.1-58.1v-201c.1-23.1-7.9-42.4-24-58.5z"/><path d="M338 219.3c0-4.9-1.8-9.2-5.4-12.9L177.3 51.1c-3.6-3.6-7.9-5.4-12.8-5.4s-9.2 1.8-12.9 5.4c-3.6 3.6-5.4 7.9-5.4 12.9v82.2H18.3c-5 0-9.2 1.8-12.9 5.4-3.6 3.6-5.4 7.9-5.4 12.9v109.6c0 4.9 1.8 9.2 5.4 12.8 3.6 3.6 7.9 5.4 12.9 5.4h127.9v82.2c0 4.9 1.8 9.2 5.4 12.8 3.6 3.6 7.9 5.4 12.9 5.4 4.9 0 9.2-1.8 12.8-5.4L332.6 232c3.6-3.5 5.4-7.8 5.4-12.7z"/></svg>
												</div>
												<span>Login</span>
											</a>
										</div>
									<?php endif; ?>
								</nav>
							</div>
							<button class="search" title="Search">
								<div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 475.1 475.1"><path d="M464.5 412.8l-97.9-97.9c23.6-34.1 35.4-72 35.4-113.9 0-27.2-5.3-53.2-15.9-78.1-10.6-24.8-24.8-46.3-42.8-64.2s-39.4-32.3-64.2-42.8C254.2 5.3 228.2 0 201 0c-27.2 0-53.2 5.3-78.1 15.8-24.8 10.6-46.2 24.9-64.2 42.9s-32.3 39.4-42.8 64.2C5.3 147.8 0 173.8 0 201c0 27.2 5.3 53.2 15.8 78.1 10.6 24.8 24.8 46.2 42.8 64.2 18 18 39.4 32.3 64.2 42.8 24.8 10.6 50.9 15.8 78.1 15.8 41.9 0 79.9-11.8 113.9-35.4l97.9 97.6c6.9 7.2 15.4 10.8 25.7 10.8 9.9 0 18.5-3.6 25.7-10.8 7.2-7.2 10.8-15.8 10.8-25.7.2-9.9-3.3-18.5-10.4-25.6zM291.4 291.4c-25 25-55.1 37.5-90.4 37.5-35.2 0-65.3-12.5-90.4-37.5-25-25-37.5-55.1-37.5-90.4 0-35.2 12.5-65.3 37.5-90.4 25-25 55.1-37.5 90.4-37.5 35.2 0 65.3 12.5 90.4 37.5 25 25 37.5 55.1 37.5 90.4 0 35.2-12.5 65.3-37.5 90.4z"></path></svg></div>
								<span>&nbsp;</span>
							</button>
							<button class="dashboard <?php if(!User::isGuest()) { echo ' loggedin'; } ?>" title="Dashboard">
								<div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 438.5 438.5"><path d="M414.4 60.7c-16.1-16.1-35.4-24.1-58.1-24.1H265c-2.5 0-4.4.6-5.9 1.9-1.4 1.2-2.4 3.1-2.9 5.6-.5 2.5-.8 4.7-.9 6.7-.1 2-.1 4.5.1 7.6.2 3 .3 4.9.3 5.7.6 1.5.8 2.8.6 3.9-.2 1 .5 1.9 2 2.6 1.5.7 2.3 1.2 2.3 1.6s1.1.7 3.3.9l3.3.3h89c12.6 0 23.3 4.5 32.3 13.4 9 8.9 13.4 19.7 13.4 32.3v201c0 12.6-4.5 23.3-13.4 32.3-8.9 8.9-19.7 13.4-32.3 13.4H265c-2.5 0-4.4.6-5.9 1.9-1.4 1.2-2.4 3.1-2.9 5.6-.5 2.5-.8 4.7-.9 6.7-.1 2-.1 4.5.1 7.6.2 3 .3 4.9.3 5.7 0 2.5.9 4.6 2.7 6.4 1.8 1.8 3.9 2.7 6.4 2.7h91.4c22.6 0 42-8 58.1-24.1s24.1-35.4 24.1-58.1v-201c.1-23.1-7.9-42.4-24-58.5z"/><path d="M338 219.3c0-4.9-1.8-9.2-5.4-12.9L177.3 51.1c-3.6-3.6-7.9-5.4-12.8-5.4s-9.2 1.8-12.9 5.4c-3.6 3.6-5.4 7.9-5.4 12.9v82.2H18.3c-5 0-9.2 1.8-12.9 5.4-3.6 3.6-5.4 7.9-5.4 12.9v109.6c0 4.9 1.8 9.2 5.4 12.8 3.6 3.6 7.9 5.4 12.9 5.4h127.9v82.2c0 4.9 1.8 9.2 5.4 12.8 3.6 3.6 7.9 5.4 12.9 5.4 4.9 0 9.2-1.8 12.8-5.4L332.6 232c3.6-3.5 5.4-7.8 5.4-12.7z"/></svg></div>
								<span>&nbsp;</span>
							</button>
						</div>
						<jdoc:include type="modules" name="notices" />
						<jdoc:include type="modules" name="helppane" />
						<div class="search-panel">
							<div class="text-field">
								<jdoc:include type="modules" name="search" />
								<a href="#" class="close">Close</a>
							</div>
						</div>
					</div>
					<?php if ($menu->getActive() != $menu->getDefault()) : ?>
					<div class="sub">
						<nav></nav>
						<div class="breadcrumbs-wrap">
							<div class="breadcrumbs">
								<div class="wrap">
									<div class="icon">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 425.963 425.963"><path d="M213.285 0h-.608c-73.563 0-133.41 59.826-133.41 133.36 0 48.203 21.953 111.818 65.247 189.082 32.098 57.28 64.646 101.152 64.972 101.588.906 1.217 2.334 1.934 3.847 1.934.043 0 .087 0 .13-.002 1.56-.043 3.002-.842 3.868-2.143.322-.487 32.638-49.288 64.518-108.977 43.03-80.563 64.848-141.624 64.848-181.482C346.693 59.826 286.846 0 213.286 0zm61.58 136.62c0 34.124-27.76 61.884-61.885 61.884-34.123 0-61.884-27.76-61.884-61.884s27.76-61.884 61.884-61.884c34.124 0 61.885 27.76 61.885 61.884z"/></svg>
									</div>

									<div class="crumbs-wrap">
										<jdoc:include type="modules" name="breadcrumbs" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</header>

				<div class="page-body">

					<div class="content">

						<?php if ($this->getBuffer('message') && $this->getBuffer('message') != '<div id="system-message-container">
</div>') : ?>
						<section class="section">
							<jdoc:include type="message" />
						</section>
						<?php endif; ?>

						<?php if ($this->countModules('left or right')) : ?>
						<div class="inner withmenu">
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
						</div>
						<?php endif; ?>

					</div>
				</div>

				<footer class="global">
					<?php $errorPage = false; include "footer.php"; ?>
				</footer>
			</div>
			<div class="dashboard-panel" id="dashboard-panel">
				<div class="dashboard-panel-inner">
					<div class="dashboard-panel-content">
						<!-- <header><h2>Dashboard</h2></header> -->
						<div class="scroller">
							<?php if (!User::isGuest()) { ?>
								<section class="user">
									<div class="user-info">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
											<?php echo stripslashes(User::get('name')); ?>
											<span><?php echo User::get('email'); ?></span>
										</a>
									</div>
									<header><h2>All Categories</h2></header>
									<nav class="user-nav">
										<ul>
											<li id="account-profile">
												<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span class="nav-icon-user"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/user.svg") ?></span><span><?php echo Lang::txt('TPL_BMC_ACCOUNT_PROFILE'); ?></span></a>
											</li>
											<li id="account-dashboard">
												<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span class="nav-icon-dashboard"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/th-large.svg") ?></span><span><?php echo Lang::txt('TPL_BMC_ACCOUNT_DASHBOARD'); ?></span></a>
											</li>
											<jdoc:include type="modules" name="minidash" />
											<li id="account-logout">
												<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span class="nav-icon-logout"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/signout.svg") ?></span><span><?php echo Lang::txt('TPL_BMC_LOGOUT'); ?></span></a>
											</li>
										</ul>
									</nav>
								</section>
							<?php } ?>
						</div>
					</div>
					<a href="#" class="close">Close</a>
				</div>
			</div>
		</div>

		<div class="mobile-panel">
			<div class="inner scroller">
				<div class="subpanel menu">
					<div class="inner">
						<h4><a style="color: #8cc540;" href="/">Home</a></h4>
						<jdoc:include type="modules" name="mainmenu" />
						<jdoc:include type="modules" name="secondarymenu" />
					</div>
				</div>
				<div class="subpanel search">
					<div class="inner">
						<div class="label">Search</div>
						<jdoc:include type="modules" name="search" />
					</div>
				</div>
				<div class="background">
					<div class="panel1"></div>
					<div class="panel2"></div>
					<div class="panel3"></div>
					<div class="panel4"></div>
					<div class="panel5"></div>
					<div class="panel6"></div>
					<div class="panel7"></div>
					<div class="panel8"></div>
				</div>
			</div>
			<div class="close"></div>
		</div>

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
