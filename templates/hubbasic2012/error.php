<?php
defined('_JEXEC') or die('Restricted access');

$config = JFactory::getConfig();
$juser = JFactory::getUser();

$this->template = 'hubbasic2012';

$lang = JFactory::getLanguage();
$lang->load('tpl_hubbasic2012');

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title><?php echo $config->getValue('config.sitename') . ' - ' . $this->error->getCode(); ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" />
<?php if ($config->getValue('config.application_env', 'production') != 'production') { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/modules/mod_application_env/mod_application_env.css" />
<?php } ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.js"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 10]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie10.css" />
		<![endif]-->
		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" />
		<![endif]-->
	</head>
	<body>
		<?php \Hubzero\Module\Helper::displayModules('notices'); ?>
		<?php \Hubzero\Module\Helper::displayModules('helppane'); ?>
		<div id="top">
			<div class="inner-wrap">
				<div class="inner">
					<div id="topbar">
						<ul>
							<li><a href="#content"><?php echo JText::_('TPL_HUBBASIC_SKIP'); ?></a></li>
							<li><a href="<?php echo $this->baseurl; ?>/about/contact"><?php echo JText::_('TPL_HUBBASIC_CONTACT'); ?></a></li>
						</ul>
						<?php \Hubzero\Module\Helper::displayModules('search'); ?>
					<?php if (\Hubzero\Module\Helper::countModules('helppane')) : ?>
						<p id="tab">
							<a href="<?php echo JRoute::_('index.php?option=com_support'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_NEED_HELP'); ?>">
								<span><?php echo JText::_('TPL_HUBBASIC_HELP'); ?></span>
							</a>
						</p>
					<?php endif; ?>
					</div><!-- / #topbar -->
					<header id="masthead">
						<div class="inner">
							<h1>
								<a href="<?php echo empty($this->baseurl) ? "/" : $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
									<span><?php echo $config->getValue('config.sitename'); ?></span>
								</a>
								<span class="tagline"><?php echo JText::_('TPL_HUBBASIC_TAGLINE'); ?></span>
							</h1>
							<ul id="account" class="<?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<?php if (!$juser->get('guest')) {
									$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
							?>
								<li id="account-info">
									<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo $juser->get('name'); ?>" width="30" height="30" />
									<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
										<?php echo stripslashes($juser->get('name')); ?> 
										<span class="account-username"><?php echo $juser->get('username'); ?></span>
									</a>
									<span class="account-sep"></span>
									<ul>
										<li id="account-dashboard">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
										</li>
										<li id="account-profile">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
										</li>
										<li id="account-messages">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
										</li>
										<li id="account-logout">
											<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_LOGOUT'); ?></span></a>
										</li>
									</ul>
								</li>
							<?php } else { ?>
								<li id="account-login">
									<a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?>"><?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?></a>
								</li>
								<li id="account-register">
									<a href="<?php echo JRoute::_('index.php?option=com_members&controller=register'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo JText::_('TPL_HUBBASIC_REGISTER'); ?></a>
								</li>
							<?php } ?>
							</ul><!-- / #account -->
							<nav id="nav" role="menu">
								<?php \Hubzero\Module\Helper::displayModules('user3'); ?>
							</nav><!-- / #nav -->
						</div><!-- / .inner -->
					</header><!-- / #masthead -->

					<div id="sub-masthead">
						<div id="trail">
							<?php /* \Hubzero\Module\Helper::displayModules('breadcrumbs'); */ ?>
						</div>
					</div><!-- / #sub-masthead -->
				</div><!-- / .inner -->
			</div><!-- / .inner-wrap -->
		</div><!-- / #top -->

		<div id="wrap">
			<main id="content" class="<?php echo JRequest::getCmd('option', ''); ?> <?php echo 'code' . $this->error->getCode(); ?>">
				<div class="inner">
					<section class="main section">
						<div class="grid">
							<div class="col span-half">
								<div id="errormessage">
									<h2 class="error-code">
										<?php echo $this->error->getCode(); ?>
									</h2>
								</div><!-- / #errormessage -->
							</div><!-- / .col span-half -->
							<div class="col span-half omega">
								<div id="errorbox">
									<div class="wrap">
									<?php if ($this->error->getCode() == 404) { ?>
										<h3><?php echo JText::_('RE: Your Missing Page'); ?></h3>
										<blockquote>
											<p><?php echo JText::_("We're sorry to report that we couldn't find your page. Search parties were unable to recover any remains. It is our current belief that Hubzilla ate it."); ?></p>
											<p><?php echo JText::_('In a difficult time like this we recommend seeking guidance from our <a href="/home">Home Page</a> or by <a href="/search">searching</a> for a new page. We understand that a new page may never fill the void left by your missing page but hope that you can find some consolation in the text of another.'); ?></p>
											<p><?php echo JText::_('With our deepest sympathies and condolences,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Mura, Science Special Search Party (SSSP)</p>
									<?php } else if ($this->error->getCode() == 403) { ?>
										<h3><?php echo JText::_('Access Denied!'); ?></h3>
										<blockquote>
											<p><?php echo JText::_('It appears you do not have access to this page. You may be detained for further questioning.'); ?></p>
											<p><?php echo JText::_('Please bear with us during this grueling process of rebuilding in the wake of Hubzilla\'s attack.'); ?></p>
											<p><?php echo JText::_('Please stay calm,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Showa, Security</p>
									<?php } else if ($this->error->getCode() == 500) { ?>
										<h3><?php echo JText::_('Will Hubzilla\'s reign of terror never cease?!'); ?></h3>
										<blockquote>
											<p><?php echo JText::_('It seems Hubzilla stomped on this page. Our disaster recovery teams are scouring the wreckage for survivors and our clean-up crews will take over shortly thereafter.'); ?></p>
											<p><?php echo JText::_('Please bear with us during this grueling process of rebuilding in the wake of Hubzilla\'s attack.'); ?></p>
											<p><?php echo JText::_('With our sincere apologies,'); ?></p>
										</blockquote>
										<p class="signature">&mdash;Cpt. Hayata, Disaster Recovery Team (DRT)</p>
									<?php } ?>
									</div><!-- / .wrap -->
								</div><!-- / #errorbox -->
							</div><!-- / .col span-half -->
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
		</div><!-- / #wrap -->

		<footer id="footer">
			<?php \Hubzero\Module\Helper::displayModules('footer'); ?>
		</footer><!-- / #footer -->

		<?php \Hubzero\Module\Helper::displayModules('endpage'); ?>
	</body>
</html>