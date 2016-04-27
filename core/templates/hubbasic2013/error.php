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

$this->template = 'hubbasic2013';

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width" />
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge" /><![endif]-->

		<title><?php echo Config::get('sitename') . ' - ' . (in_array($this->error->getCode(), array(404, 403, 500)) ? $this->error->getCode() : 500); ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/templates/<?php echo $this->template; ?>/css/error.css" />
		<?php if ($this->debug) { ?>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/plugins/system/debug/assets/css/debug.css" />
		<?php } ?>
		<?php if (Config::get('application_env', 'production') != 'production') { ?>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/modules/mod_application_env/assets/css/mod_application_env.css" />
		<?php } ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/templates/<?php echo $this->template; ?>/css/print.css" />

		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.ui.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.fancybox.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo str_replace('/core', '', $this->baseurl); ?>/core/modules/mod_reportproblems/assets/js/mod_reportproblems.js"></script>

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/core/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 10]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/core/templates/<?php echo $this->template; ?>/css/browser/ie10.css" /><![endif]-->
		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/core/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/core/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/core/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body>
		<?php echo Module::position('notices'); ?>
		<?php echo Module::position('helppane'); ?>

		<div id="top">
			<header id="masthead">
				<div class="inner">
					<h1>
						<a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
							<span><?php echo Config::get('sitename'); ?></span>
						</a>
					</h1>

					<div id="account" role="navigation">
					<?php if (!User::isGuest()) { ?>
						<ul class="menu loggedin">
							<li>
								<div id="account-info">
									<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" width="30" height="30" />
									<a class="account-details" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
										<?php echo stripslashes(User::get('name')); ?> 
										<span class="account-email"><?php echo User::get('email'); ?></span>
									</a>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu loggedout">
							<li id="account-login">
								<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_REGISTER'); ?></a>
							</li>
						</ul>
					<?php } ?>
					</div><!-- / #account -->

					<div id="nav" role="menu">
						<?php echo Module::position('user3'); ?>
					</div><!-- / #nav -->
				</div><!-- / .inner -->
			</header><!-- / #masthead -->

			<div id="sub-masthead">
				<div class="inner">
				<?php if (Module::count('helppane')) : ?>
					<p id="tab">
						<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_NEED_HELP'); ?>">
							<span><?php echo Lang::txt('TPL_HUBBASIC_HELP'); ?></span>
						</a>
					</p>
				<?php endif; ?>
					<?php echo Module::position('search'); ?>
					<div id="trail">
						<span class="pathway"><?php echo Lang::txt('TPL_HUBBASIC_TAGLINE'); ?></span>
					</div><!-- / #trail -->
				</div><!-- / .inner -->
			</div><!-- / #sub-masthead -->

			<div id="splash">
				<div class="inner-wrap">
					<div class="inner">
						<div class="wrap">
						</div><!-- / .wrap -->
					</div><!-- / .inner -->
				</div><!-- / .inner-wrap -->
			</div><!-- / #splash -->
		</div><!-- / #top -->

		<div id="wrap">
			<main id="content" class="<?php echo Request::getCmd('option', ''); ?> <?php echo 'code' . $this->error->getCode(); ?>" role="main">
				<div class="inner">

					<section class="main section">
						<div class="grid">
							<div class="col span-half">
								<div id="errormessage">
									<h2 class="error-code">
										<?php echo (in_array($this->error->getCode(), array(404, 403, 500))) ? $this->error->getCode() : 500; ?>
									</h2>
								</div><!-- / #errormessage -->
							</div><!-- / .col span-half -->
							<div class="col span-half omega">
								<div id="errorbox">
									<div class="wrap">
									<?php 
									switch ($this->error->getCode())
									{
										case 404: ?>
										<h3><?php echo Lang::txt('RE: Your Missing Page'); ?></h3>
										<blockquote>
											<p><?php echo Lang::txt("We're sorry to report that we couldn't find your page. Search parties were unable to recover any remains. It is our current belief that Hubzilla ate it."); ?></p>
											<p><?php echo Lang::txt('In a difficult time like this we recommend seeking guidance from our <a href="/home">Home Page</a> or by <a href="/search">searching</a> for a new page. We understand that a new page may never fill the void left by your missing page but hope that you can find some consolation in the text of another.'); ?></p>
											<p><?php echo Lang::txt('With our deepest sympathies and condolences,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Mura, Science Special Search Party (SSSP)</p>
										<?php 
										break;
										case 403: ?>
										<h3><?php echo Lang::txt('Access Denied!'); ?></h3>
										<blockquote>
											<p><?php echo Lang::txt('It appears you do not have access to this page. You may be detained for further questioning.'); ?></p>
											<p><?php echo Lang::txt('Please bear with us during this grueling process of rebuilding in the wake of Hubzilla\'s attack.'); ?></p>
											<p><?php echo Lang::txt('Please stay calm,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Showa, Security</p>
										<?php 
										break;
										case 500:
										default: ?>
										<h3><?php echo Lang::txt('Will Hubzilla\'s reign of terror never cease?!'); ?></h3>
										<blockquote>
											<p><?php echo Lang::txt('It seems Hubzilla stomped on this page. Our disaster recovery teams are scouring the wreckage for survivors and our clean-up crews will take over shortly thereafter.'); ?></p>
											<p><?php echo Lang::txt('Please bear with us during this grueling process of rebuilding in the wake of Hubzilla\'s attack.'); ?></p>
											<p><?php echo Lang::txt('With our sincere apologies,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Hayata, Disaster Recovery Team (DRT)</p>
										<?php 
										break;
									} ?>
									</div><!-- / .wrap -->
								</div><!-- / #errorbox -->
							</div><!-- / .col span-half omega -->
						</div><!-- / .grid -->
					<?php if ($this->debug) { ?>
						<p class="error">
							<?php echo $this->error->getMessage(); ?>
						</p>
					<?php } ?>
					</section><!-- / .main section -->

				<?php if ($this->debug) { ?>
					<section id="techinfo">
						<?php echo $this->renderBacktrace(); ?>
					</section><!-- / #techinfo -->
				<?php } ?>
				</div><!-- / .inner -->
			</main><!-- / #content -->

			<footer id="footer">
				<?php echo Module::position('footer'); ?>
			</footer><!-- / #footer -->
		</div><!-- / #wrap -->

		<?php echo Module::position('endpage'); ?>
	</body>
</html>