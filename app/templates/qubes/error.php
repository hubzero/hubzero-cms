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

defined('_HZEXEC_') or die();

$this->template = 'qubes';

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?> ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width" />
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<title><?php echo Config::get('sitename') . ' - ' . (in_array($this->error->getCode(), array(404, 403, 500)) ? $this->error->getCode() : 500); ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" />
		<?php if (Config::get('debug')) { ?>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/media/cms/css/debug.css" />
		<?php } ?>
		<?php if (Config::get('application_env', 'production') != 'production') { ?>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/modules/mod_application_env/mod_application_env.css" />
		<?php } ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<script type="text/javascript" src="<?php echo Request::root(); ?>/core/assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo Request::root(); ?>/core/assets/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo Request::root(); ?>/core/assets/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo Request::root(); ?>/core/modules/mod_reportproblems/assets/js/mod_reportproblems.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function(jq) { HUB.Modules.ReportProblems.initialize(".helpme"); });
		</script>

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 10]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie10.css" /><![endif]-->
		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body>
		<?php \Hubzero\Module\Helper::displayModules('notices'); ?>
		<?php \Hubzero\Module\Helper::displayModules('helppane'); ?>

		<div class="rel-wrap">

			<div id="header" class="cf on-top">

				<div id="mobile-nav" class="show-m">
					<a id="mobile-menu" href="#">
						<p id="nav-icon"><span></span></p>
						<em><?php echo Lang::txt('TPL_QUBES_MENU'); ?></em>
					</a>
				</div>

				<nav id="hubLogo">
					<a href="<?php echo Request::base(); ?>" title="<?php echo Config::get('sitename'); ?>">
						<?php echo file_get_contents(__DIR__ . '/svg/logo.svg'); ?>
					</a>
				</nav>

				<div class="mobile-wrapper">
					<div id="toolbar" class="cf <?php if (!User::get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">

						<?php \Hubzero\Module\Helper::displayModules('search'); ?>

						<?php if (!User::get('guest')) { ?>
							<a id="usersname" class="item" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
								<img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>">
								<?php  //echo User::get('name'); ?>
								DASHBOARD
							</a>
							<a id="logout" class="item" title="log out" href="<?php echo Route::url('index.php?option=com_logout'); ?>"><span><?php echo Lang::txt('Logout'); ?></span></a>
						<?php } else { ?>
							<a id="login" class="item" href="<?php echo Route::url('index.php?option=com_login'); ?>" title="<?php echo Lang::txt('Login'); ?>"><?php echo Lang::txt('Login'); ?></a>
							<a id="register" class="item" href="<?php echo Route::url('index.php?option=com_register'); ?>" title="<?php echo Lang::txt('Sign up for a free account'); ?>"><?php echo Lang::txt('Sign Up'); ?></a>
						<?php } ?>
					</div>

					<div class="tr">
						<?php if (\Hubzero\Module\Helper::countModules('helppane')) : ?>
							<p id="tab" class="helpme">
								<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Need help? Send a trouble report to our support team.'); ?>">
									<span><?php echo Lang::txt('Need Help?'); ?></span>
								</a>
							</p>
						<?php endif; ?>
						<div class="social" class="cf">
							<ul class="cf">
								<li><a href="https://www.facebook.com/qubeshub" target="_blank" class="fb">&nbsp;<span>Facebook</span></a></li>
								<li><a href="https://twitter.com/qubeshub" target="_blank" class="tw">&nbsp;<span>Twitter</span></a></li>
								<li><a href="https://www.pinterest.com/qubeshub/" target="_blank" class="pn">&nbsp;<span>Pintrest</span></a></li>
							</ul>
						</div>
					</div>

					<div id="nav" class="cf">
						<?php \Hubzero\Module\Helper::displayModules('user3'); ?>
					</div><!-- / #nav -->
				</div>
			</div><!-- / #header -->

			<div id="splash">
				<?php if ($this->getBuffer('message')) : ?>
					<jdoc:include type="message" />
				<?php endif; ?>
			</div><!-- / #splash -->

			<div id="wrap" class="transit">
				<main id="content" class="<?php echo Request::getCmd('option', ''); ?>" role="main">

					<?php
					$err = '500';
					if (in_array($this->error->getCode(), array(404, 403)))
					{
						$err = $this->error->getCode();
					}

					switch ($err)
					{
						case 404:
							$h2 = 'Error ' . $err . Lang::txt(': Not found');
							break;
						case 403:
							$h2 = 'Error ' . $err . Lang::txt(': Access denied');
							break;
						case 500:
						default:
							$h2 = 'Error ' . $err;
							break;
					}
					?>

					<div id="content-header" class="full">
						<h2><?php echo $h2; ?></h2>
					</div>
					<div class="inner">
						<section class="main section">
							<?php
							switch ($err)
							{
								case 404:
									$message = Lang::txt("The requested resource was not found.");
									if ($this->error->getMessage())
									{
										$message = $this->error->getMessage();
									}
									break;
								case 403:
									$message = Lang::txt('It appears you do not have access to this page. This page may belong to a group with restricted access. Only members of the group can view the contents.');
									break;
								case 500:
								default:
									$message = 'An error has occurred while processing your request. If difficulties persist, please contact the system administrator of this site.';
									break;
							}
							echo '<p>' . $message . '</p>';
							?>
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

				<footer id="footer" class="cf">
					<svg xmlns="http://www.w3.org/2000/svg" width="158.019" height="45.662" viewBox="0 0 158.019 45.662"><g fill="#fff"><path d="M12.553 40.479c.749-2.227 2.55-5.34 6.831-8.091l-7.318-4.225v-10.659l7.325-4.231c-4.283-2.75-6.085-5.865-6.832-8.09l-.117-.404c-6.72 3.286-11.36 10.169-11.36 18.154 0 7.962 4.614 14.83 11.305 18.126l.166-.58zM14.434 4.675c1.006 2.89 3.378 5.474 6.87 7.497 3.489-2.023 5.861-4.606 6.867-7.495.08-.23.142-.45.201-.667-2.204-.824-4.58-1.296-7.071-1.296-2.49 0-4.865.472-7.068 1.295.059.217.121.436.201.666zM14.004 27.044l7.295 4.209 7.29-4.209v-8.421l-7.29-4.213-7.295 4.213zM30.165 4.781l-.117.404c-.749 2.227-2.551 5.343-6.835 8.091l7.316 4.228v10.659l-7.318 4.225c4.282 2.751 6.084 5.864 6.83 8.091l.168.583c6.693-3.295 11.311-10.164 11.311-18.129 0-7.984-4.638-14.866-11.355-18.152zM28.166 40.984c-1.007-2.884-3.377-5.465-6.865-7.491h-.004c-3.488 2.026-5.86 4.607-6.866 7.491-.103.295-.185.578-.254.851 2.219.837 4.614 1.316 7.125 1.316 2.509 0 4.901-.479 7.118-1.313-.069-.274-.152-.558-.254-.854z"/></g><g><path fill="#E5E5E5" d="M21.3 14.366l-7.334 4.233 7.334 4.235 7.335-4.235z"/><path fill="#fff" d="M13.967 27.066l7.333 4.236v-8.468l-7.334-4.235z"/><path fill="#C7C8CA" d="M28.633 27.066l.002-8.467-7.335 4.235v8.468z"/></g><g fill="#fff"><path d="M48.902 22.414c0-6.662 4.719-11.551 11.416-11.551 6.661 0 11.416 4.889 11.416 11.551 0 3.212-1.105 5.993-2.979 8.031l1.839 2.074-2.712 2.279-1.941-2.177c-1.64.871-3.547 1.341-5.622 1.341-6.699 0-11.417-4.887-11.417-11.548zm14.562 7.397l-2.778-3.146 2.744-2.276 2.78 3.146c.97-1.37 1.506-3.146 1.506-5.12 0-4.62-2.911-8.069-7.397-8.069-4.52 0-7.398 3.449-7.398 8.069 0 4.584 2.878 8.065 7.398 8.065 1.17-.001 2.206-.235 3.145-.669zM75.485 24.723v-13.49h3.984v13.389c0 3.549 1.976 5.857 5.69 5.857 3.715 0 5.691-2.309 5.691-5.857v-13.389h3.984v13.49c0 5.521-3.181 9.239-9.675 9.239-6.46 0-9.674-3.717-9.674-9.239zM99.591 33.56v-22.327h10.978c4.117 0 6.361 2.543 6.361 5.69 0 2.778-1.807 4.688-3.882 5.123 2.411.369 4.316 2.744 4.316 5.458 0 3.48-2.272 6.059-6.492 6.059h-11.281v-.003zm13.323-16.002c0-1.64-1.139-2.879-3.111-2.879h-6.296v5.758h6.296c1.972.001 3.111-1.17 3.111-2.879zm.433 9.441c0-1.672-1.167-3.114-3.382-3.114h-6.459v6.226h6.459c2.113 0 3.382-1.171 3.382-3.112zM121.32 33.56v-22.327h15.295v3.446h-11.38v5.758h11.146v3.447h-11.146v6.226h11.38v3.449h-15.295zM139.197 30.412l2.208-3.045c1.509 1.641 3.952 3.112 7.067 3.112 3.212 0 4.449-1.573 4.449-3.081 0-4.685-12.986-1.771-12.986-9.973 0-3.717 3.215-6.562 8.133-6.562 3.449 0 6.293 1.138 8.334 3.145l-2.207 2.913c-1.775-1.773-4.151-2.577-6.495-2.577-2.273 0-3.751 1.139-3.751 2.779 0 4.184 12.989 1.607 12.989 9.908 0 3.717-2.642 6.928-8.637 6.928-4.116.003-7.095-1.47-9.104-3.547z"/></g></svg>
					<p>Copyright <?php echo date('Y'); ?> QUBES <span>Powered by HUBzero, a <a href="http://www.purdue.edu" target="_blank">Purdue</a> project</span>
				</footer><!-- / #footer -->
			</div><!-- / #wrap -->

		</div>

		<?php \Hubzero\Module\Helper::displayModules('endpage'); ?>
	</body>
</html>