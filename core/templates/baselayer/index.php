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
 * @author    Ilya Shunko
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include global scripts
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');

// Get browser info to set some classes
$browser = new Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

// Find out if this is a front page
$menu = App::get('menu');
$isFrontPage = false;
if ($menu->getActive() == $menu->getDefault() && $this->countModules('home-intro'))
{
	$isFrontPage = true;
}

// Prepend site name to document title
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

		<jdoc:include type="head" />
	</head>
	<body<?php if ($isFrontPage) : echo ' id="frontpage"'; endif; ?>>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<header id="page-header">
			<section class="top-wrapper cf">
				<div id="top" class="cf">
					<a href="<?php echo $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>" class="logo">
						<p><?php echo Config::get('sitename'); ?></p>
						<p class="tagline hide-m"><?php echo Lang::txt('TPL_BASELAYER_TAGLINE'); ?></p>
					</a>

					<div id="mobile-nav" class="show-m">
						<ul>
							<li><a id="mobile-menu"><span><?php echo Lang::txt('TPL_BASELAYER_MENU'); ?></span></a></li>
							<li><a id="mobile-search"><span><?php echo Lang::txt('TPL_BASELAYER_SEARCH'); ?></span></a></li>
						</ul>
					</div>
				</div>

				<div id="search-box">
					<jdoc:include type="modules" name="search" />
				</div>
			</section>

			<nav id="main-navigation" role="main">
				<div class="wrapper cf">
					<div id="account">
					<?php if (!User::isGuest()) { ?>
						<ul class="menu cf <?php echo (!User::isGuest()) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" />
									<a class="account-details" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
										<?php echo stripslashes(User::get('name')); ?>
										<span class="account-email"><?php echo User::get('email'); ?></span>
									</a>
									<p class="account-logout">
										<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_BASELAYER_LOGOUT'); ?></span></a>
									</p>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_BASELAYER_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_BASELAYER_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><span><?php echo Lang::txt('TPL_BASELAYER_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_BASELAYER_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu <?php echo (!User::isGuest()) ? 'loggedin' : 'loggedout'; ?>">
							<li id="account-login">
								<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_BASELAYER_LOGIN'); ?>"><?php echo Lang::txt('TPL_BASELAYER_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>" title="<?php echo Lang::txt('TPL_BASELAYER_SIGN_UP'); ?>"><?php echo Lang::txt('TPL_BASELAYER_REGISTER'); ?></a>
							</li>
						</ul>
					<?php } ?>
					</div><!-- / #account -->

					<div id="main-nav">
						<jdoc:include type="modules" name="user3" />
					</div>
				</div><!-- / #wrapper -->
			</nav>

			<?php if (!$isFrontPage) : ?>
				<div id="trail">
					<jdoc:include type="modules" name="breadcrumbs" />
				</div><!-- / #trail -->
			<?php endif; ?>
		</header>

		<?php if ($this->countModules('home-intro')) : ?>
			<div id="home-intro">
				<jdoc:include type="modules" name="home-intro" />
			</div>
		<?php endif; ?>

		<main id="content" class="<?php echo Request::getCmd('option', ''); ?>" role="main">
			<div class="inner">
				<?php if ($this->countModules('left or right')) : ?>
					<section class="main section cf">
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
						</section><!-- / .section-inner -->
					</section><!-- / .main section -->
				<?php endif; ?>
			</div>
		</main><!-- / #content -->

		<footer id="footer">
			<div class="wrapper">
				<jdoc:include type="modules" name="footer" />

				<div id="hubzero-proud-branding">
					<p><?php echo Lang::txt('TPL_BASELAYER_COPYRIGHT'); ?></p>
				</div>
			</div>
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>