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
 * @author    HUBzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

if (Request::getInt('getstarted', 0) && ($tpl = $this->params->get('template', '')))
{
	$fallback = 'kimera';

	$database = App::get('db');

	// Make the desired template exists
	$database->setQuery("SELECT id FROM `#__template_styles` WHERE `client_id`=0 AND `template`=" . $database->quote($tpl));
	if (!($found = $database->loadResult()) && $tpl != $fallback)
	{
		$tpl = $fallback;
		$database->setQuery("SELECT id FROM `#__template_styles` WHERE `client_id`=0 AND `template`=" . $database->quote($tpl));
		$found = $database->loadResult();
	}
	if ($found)
	{
		// Unset this template
		$database->setQuery("UPDATE `#__template_styles` SET `home`=0 WHERE `client_id`=0 AND `home`=1;");
		$database->query();

		// Set the desired template
		$database->setQuery("UPDATE `#__template_styles` SET `home`=1 WHERE `client_id`=0 AND `template`=" . $database->quote($tpl));
		$database->query();

		App::redirect(Route::url('/gettingstarted'));
	}
}

// Get template flavor (ex: amazon)
$flavor = $this->params->get('flavor', false);
?>
<!DOCTYPE html>
<!--[if IE]><html class="no-js"><head><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->
<!--[if !(IE)]><!--><html class="no-js"><head><!--<![endif]-->
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<title><?php echo Config::get('sitename') . ' - ' . $this->getTitle(); ?></title>

		<!-- Styles -->
		<link rel="stylesheet" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/normalize.min.css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/main.css" />

		<!-- Scripts -->
		<script type="text/javascript" src="<?php echo Html::asset('script', 'jquery.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/vendor/modernizr-2.6.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/vendor/skrollr.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/main.js"></script>

		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="57x57"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon" sizes="72x72"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon" sizes="60x60"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-60x60.png" />
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-120x120.png" />
		<link rel="apple-touch-icon" sizes="76x76"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-76x76.png" />
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-152x152.png" />
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/apple-touch-icon-180x180.png" />

		<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon.ico" />

		<link rel="icon" type="image/png" sizes="192x192" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon-192x192.png" />
		<link rel="icon" type="image/png" sizes="160x160" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon-160x160.png" />
		<link rel="icon" type="image/png" sizes="96x96"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon-96x96.png" />
		<link rel="icon" type="image/png" sizes="16x16"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon-16x16.png" />
		<link rel="icon" type="image/png" sizes="32x32"   href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/favicon-32x32.png" />

		<meta name="msapplication-TileColor" content="#f1f1f1" />
		<meta name="msapplication-TileImage" content="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/mstile-144x144.png" />
	</head>
	<body>

		<div class="wrap skrollr" id="skrollr-body">

			<div class="logos">
				<div class="inner">
					<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/logo.svg" id="hubzero" alt="HUBzero" />
					<?php if ($flavor = $this->params->get('flavor')) { ?>
						<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/<?php echo $flavor; ?>.svg" id="<?php echo $flavor; ?>" alt="<?php echo ucfirst($flavor); ?>" />
					<?php } ?>
				</div>
			</div>

			<header>
				<div class="inner">
					<h1><?php echo Lang::txt('TPL_WELCOME_CONGRATS' . ($this->params->get('flavor') ? '_' . strtoupper($this->params->get('flavor')) : ''), HVERSION); ?></h1>

					<div class="displays">
						<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/screen-x2.png" class="display" alt="" />
						<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/hubzero-web.png" class="web" data-0="left: 0%" data-900="left: -20%" alt="" />
						<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/hubzero-tool.png" class="tool" data-0="right: 0%" data-900="right: -20%" alt="" />
					</div>
				</div>
			</header>

			<main class="content">
				<div class="inner">
					<div class="intro">
						<h4><?php echo Lang::txt('TPL_WELCOME_ABOUT_HEADER'); ?></h4>
						<p><?php echo Lang::txt('TPL_WELCOME_ABOUT_BODY'); ?></p>
					</div>


					<?php if ($flavor == 'amazon') : ?>
						<h2><span><?php echo Lang::txt('TPL_WELCOME_FIRST_THING'); ?></span></h2>

						<div class="section">
							<div class="cols clearfix">
								<div class="col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/admin.svg" alt="" />
								</div>
								<div class="col spacer">&nbsp;</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_ADMIN_PASS_HEADER_' . strtoupper($flavor)); ?></h3>

									<p><?php echo Lang::txt('TPL_WELCOME_ADMIN_PASS_BODY_' . strtoupper($flavor)); ?></p>

									<?php echo Lang::txt('TPL_WELCOME_ADMIN_PASS_SITE_URL', $_SERVER['SERVER_NAME'] ); ?>
									<br/><br/>
									<?php echo Lang::txt('TPL_WELCOME_ADMIN_PASS_ADMIN_URL', $_SERVER['SERVER_NAME'] ); ?>

								</div>
							</div>
						</div>

						<div class="section inverted">
							<div class="cols clearfix">
								<div class="mobile col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/ssh.svg" alt="" />
								</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_SSH_HEADER_' . strtoupper($flavor)); ?></h3>

									<p><?php echo Lang::txt('TPL_WELCOME_SSH_BODY_' . strtoupper($flavor), $_SERVER['SERVER_NAME']); ?></p>
								</div>
								<div class="spacer col">&nbsp;</div>
								<div class="nomobile col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/ssh.svg" alt="" />
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($flavor != 'amazon') : ?>

						<h2 class="info"><span><?php echo Lang::txt('TPL_WELCOME_GETTING_TO_KNOW'); ?></span></h2>

						<div class="section">
							<div class="cols clearfix">
								<div class="col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/use.svg" alt="" />
								</div>
								<div class="col spacer">&nbsp;</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_UTILIZE_HEADER'); ?></h3>

									<p><?php echo Lang::txt('TPL_WELCOME_UTILIZE_BODY'); ?></p>
									<a href="http://hubzero.org/documentation/current/users" class="b" rel="external" target="_blank"><?php echo Lang::txt('TPL_WELCOME_UTILIZE_LINK'); ?></a>
								</div>
							</div>
						</div>

						<div class="section inverted">
							<div class="cols clearfix">
								<div class="mobile col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/manage.svg" alt="" />
								</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_MANAGE_HEADER'); ?></h3>

									<p><?php echo Lang::txt('TPL_WELCOME_MANAGE_BODY'); ?></p>
									<a href="http://hubzero.org/documentation/current/managers" class="b" rel="external" target="_blank"><?php echo Lang::txt('TPL_WELCOME_MANAGE_LINK'); ?></a>
								</div>
								<div class="col spacer">&nbsp;</div>
								<div class="col icon nomobile">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/manage.svg" alt="" />
								</div>
							</div>
						</div>

						<div class="section">
							<div class="cols clearfix">
								<div class="col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/extend.svg" alt="" />
								</div>
								<div class="col spacer">&nbsp;</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_EXTEND_HEADER'); ?></h3>

									<p><?php echo Lang::txt('TPL_WELCOME_EXTEND_BODY'); ?></p>
									<a href="http://hubzero.org/documentation/current/webdevs" class="b" rel="external" target="_blank"><?php echo Lang::txt('TPL_WELCOME_EXTEND_LINK'); ?></a>
								</div>
							</div>
						</div>
					</div>

					<div class="bam">
						<div class="inner">	
							<h2 class="info"><span><?php echo Lang::txt('TPL_WELCOME_SETTING_UP'); ?></span></h2>
							<p class="intro"><?php echo Lang::txt('TPL_WELCOME_SETTING_UP_BODY'); ?></p>

							<div class="section">
								<div class="cols clearfix">
									<div class="col">
										<h3><?php echo Lang::txt('TPL_WELCOME_TODO_HEADER'); ?></h3>
										<ul>
											<li class="about">
												<h4><?php echo Lang::txt('TPL_WELCOME_TODO_ABOUT_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_TODO_ABOUT_BODY'); ?></p>
											</li>
											<li class="contact">
												<h4><?php echo Lang::txt('TPL_WELCOME_TODO_CONTACT_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_TODO_CONTACT_BODY'); ?></p>
											</li>
											<li class="terms">
												<h4><?php echo Lang::txt('TPL_WELCOME_TODO_TERMS_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_TODO_TERMS_BODY'); ?></p>
											</li>
										</ul>
									</div>
									<div class="col spacer">&nbsp;</div>
									<div class="col">
										<h3><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_HEADER'); ?></h3>
										<ul>
											<li class="logins">
												<h4><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_AUTH_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_AUTH_BODY'); ?></p>
											</li>
											<li class="analytics">
												<h4><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_ANALYTICS_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_ANALYTICS_BODY'); ?></p>
											</li>
											<li class="captcha">
												<h4><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_RECAPTCHA_HEADER'); ?></h4>
												<p><?php echo Lang::txt('TPL_WELCOME_RECOMMEND_RECAPTCHA_BODY'); ?></p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

				<?php endif; ?>


					<div class="inner">
						<h2><span><?php echo Lang::txt('TPL_WELCOME_READY'); ?></span></h2>

						<div class="section">
							<div class="cols clearfix">
								<div class="col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/home.svg" alt="" />
								</div>
								<div class="spacer col">&nbsp;</div>
								<div class="col txt">
									<p><?php echo Lang::txt('TPL_WELCOME_CHANGE_PAGE_BODY', rtrim(Request::base(), '/') . '/gettingstarted'); ?></p>

									<a href="<?php echo rtrim(Request::base(), '/'); ?>/?getstarted=1" class="b"><?php echo Lang::txt('TPL_WELCOME_READY'); ?></a>
								</div>
							</div>
						</div>
						<!--div class="section inverted">
							<div class="cols clearfix">
								<div class="mobile col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/administration.svg" alt="" />
								</div>
								<div class="col txt">
									<h3><?php echo Lang::txt('TPL_WELCOME_GO_TO_ADMIN_HEADER'); ?></h3>
	
									<p><?php echo Lang::txt('TPL_WELCOME_GO_TO_ADMIN_BODY'); ?></p>
	
									<a href="<?php echo $this->baseurl; ?>/administrator" class="b"><?php echo Lang::txt('TPL_WELCOME_GO_TO_ADMIN_LINK'); ?></a>
								</div>
								<div class="spacer col">&nbsp;</div>
								<div class="nomobile col icon">
									<img src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/administration.svg" alt="" />
								</div>
							</div>
						</div-->
					</div>

					<!--div class="bam babam">
						<div class="inner">	
							<h2 class="info"><span><?php echo Lang::txt('TPL_WELCOME_HELP'); ?></span></h2>
							<p class="intro"><?php echo Lang::txt('TPL_WELCOME_HELP_BODY'); ?></p>
	
							<div class="section">
								<div class="cols cols3 clearfix">
									<div class="col">
										<h3><?php echo Lang::txt('TPL_WELCOME_HELP_QUESTION_HEADER'); ?></h3>
										<p><?php echo Lang::txt('TPL_WELCOME_HELP_QUESTION_BODY'); ?></p>
									</div>
									<div class="col idea">
										<h3><?php echo Lang::txt('TPL_WELCOME_HELP_IDEA_HEADER'); ?></h3>
										<p><?php echo Lang::txt('TPL_WELCOME_HELP_IDEA_BODY'); ?></p>
									</div>
									<div class="col error">
										<h3><?php echo Lang::txt('TPL_WELCOME_HELP_ERROR_HEADER'); ?></h3>
										<p><?php echo Lang::txt('TPL_WELCOME_HELP_ERROR_BODY'); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div-->

				<div class="inner">
					<div class="fun">
						<p><?php echo Lang::txt('TPL_WELCOME_HAVE_FUN'); ?></p>
					</div>
				</div>
			</main>
		</div><!-- // .wrap -->

	</body>
</html>
