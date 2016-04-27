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

Html::behavior('framework', true);
Html::behavior('modal');

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 10]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie10.css" /><![endif]-->
		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />
		<div id="top">
			<div class="inner-wrap">
				<div class="inner">
					<div id="topbar">
						<ul>
							<li><a href="#content"><?php echo Lang::txt('TPL_HUBBASIC_SKIP'); ?></a></li>
							<li><a href="<?php echo $this->baseurl; ?>/about/contact"><?php echo Lang::txt('TPL_HUBBASIC_CONTACT'); ?></a></li>
						</ul>
						<jdoc:include type="modules" name="search" />
					<?php if ($this->countModules('helppane')) : ?>
						<p id="tab">
							<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_NEED_HELP'); ?>">
								<span><?php echo Lang::txt('TPL_HUBBASIC_HELP'); ?></span>
							</a>
						</p>
					<?php endif; ?>
					</div><!-- / #topbar -->
					<header id="masthead" role="banner">
						<div class="inner">
							<h1>
								<a href="<?php echo $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
									<span><?php echo Config::get('sitename'); ?></span>
								</a>
								<span class="tagline"><?php echo Lang::txt('TPL_HUBBASIC_TAGLINE'); ?></span>
							</h1>

							<ul id="account" class="<?php echo (!User::isGuest()) ? 'loggedin' : 'loggedout'; ?>">
							<?php if (!User::isGuest()) { ?>
								<li id="account-info">
									<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" width="30" height="30" />
									<a class="account-details" href="<?php echo Route::url(User::link()); ?>">
										<?php echo stripslashes(User::get('name')); ?> 
										<span class="account-email"><?php echo User::get('email'); ?></span>
									</a>
									<span class="account-sep"></span>
									<ul>
										<li id="account-dashboard">
											<a href="<?php echo Route::url(User::link() . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
										</li>
										<li id="account-profile">
											<a href="<?php echo Route::url(User::link() . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
										</li>
										<li id="account-messages">
											<a href="<?php echo Route::url(User::link() . '&active=messages'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
										</li>
										<li id="account-logout">
											<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_LOGOUT'); ?></span></a>
										</li>
									</ul>
								</li>
							<?php } else { ?>
								<li id="account-login">
									<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?></a>
								</li>
								<li id="account-register">
									<a href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_REGISTER'); ?></a>
								</li>
							<?php } ?>
							</ul><!-- / #account -->

							<nav id="nav" role="menu">
								<jdoc:include type="modules" name="user3" />
							</nav><!-- / #nav -->
						</div><!-- / .inner -->
					</header><!-- / #header -->

					<div id="sub-masthead">
					<?php if (!$this->countModules('welcome')) : ?>
						<div id="trail">
							<jdoc:include type="modules" name="breadcrumbs" />
							<div class="clear"></div>
						</div>
					<?php else: ?>
						<div id="splash">
							<jdoc:include type="modules" name="welcome" />
						</div><!-- / #splash -->
					<?php endif; ?>
					<?php if ($this->getBuffer('message')) : ?>
						<jdoc:include type="message" />
					<?php endif; ?>
					</div><!-- / #sub-masthead -->
				</div><!-- / .inner -->
			</div><!-- / .inner-wrap -->
		</div><!-- / #top -->
		<div id="wrap">
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
		</div><!-- / #wrap -->

		<footer id="footer">
			<jdoc:include type="modules" name="footer" />
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>