<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->template = 'hubbasic';

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" type="text/css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />

		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.ui.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.fancybox.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.js"></script>

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body>
		<?php echo Module::position('notices'); ?>
		<div id="top">
			<p class="skip" id="to-content"><a href="#content"><?php echo Lang::txt('Skip to content'); ?></a></p>
			<p id="tab">
				<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Need help? Send a trouble report to our support team.'); ?>">
					<span><?php echo Lang::txt('Need Help?'); ?></span>
				</a>
			</p>
			<div class="clear"></div>
		</div><!-- / #top -->

		<?php echo Module::position('helppane'); ?>

		<header id="header">
			<div id="header-wrap">
				<h1>
					<a href="<?php echo Request::base(); ?>" title="<?php echo Config::get('sitename'); ?>">
						<?php echo Config::get('sitename'); ?>
						<span id="tagline"><?php echo Lang::txt('A HUBzero site'); ?></span>
					</a>
				</h1>

				<ul id="toolbar" class="<?php if (!User::get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
				<?php
					if (!User::isGuest()) {
						// Find the user's most recent support tickets
						$database = JFactory::getDBO();
						$recipient = new \Hubzero\Message\Recipient($database);
						$rows = $recipient->getUnreadMessages(User::get('id'), 0);
				?>
					<li id="logout"><a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('Logout'); ?></span></a></li>
					<li id="myaccount"><a href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>"><span><?php echo Lang::txt('My Account'); ?></span></a></li>
					<li id="username"><a href="<?php echo Route::url('index.php?option=com_members&id='.User::get('id').'&active=profile'); ?>"><?php echo User::get('name'); ?> (<?php echo User::get('username'); ?>)</a></li>
					<li id="usermessages"><a href="<?php echo Route::url('index.php?option=com_members&id='.User::get('id').'&active=messages&task=inbox'); ?>"><?php echo Lang::txt('%s New Messages', count($rows)); ?></a></li>
				<?php } else { ?>
					<li id="login"><a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('Login'); ?>"><?php echo Lang::txt('Sign In'); ?></a></li>
				<?php } ?>
				</ul>

				<?php echo Module::position('search'); ?>
			</div><!-- / #header-wrap -->
		</header><!-- / #header -->

		<nav id="nav">
			<h2>Navigation</h2>
			<?php echo Module::position('user3'); ?>
			<div class="clear"></div>
		</nav><!-- / #nav -->

		<div id="trail">
			<?php echo Module::position('breadcrumbs'); ?>
		</div><!-- / #trail -->

		<div id="wrap">
			<main id="content" class="<?php echo Request::getCmd('option', ''); ?>">
				<div id="content-wrap">

					<div id="outline">
						<div id="errorbox" class="code-<?php echo $this->error->getCode(); ?>">
							<h2><?php echo $this->error->getMessage(); ?></h2>

							<p><?php echo Lang::txt('You may not be able to visit this page because of:'); ?></p>

							<ol>
								<?php if ($this->error->getCode() != 403) { ?>
									<li><?php echo Lang::txt('An out-of-date bookmark/favourite.'); ?></li>
									<li><?php echo Lang::txt('A search engine that has an out-of-date listing for this site.'); ?></li>
									<li><?php echo Lang::txt('A mis-typed address.'); ?></li>
									<li><?php echo Lang::txt('The requested resource was not found.'); ?></li>
								<?php } ?>
								<li><?php echo Lang::txt('This page may belong to a group with restricted access.  Only members of the group can view the contents.'); ?></li>
								<li><?php echo Lang::txt('An error has occurred while processing your request.'); ?></li>
							</ol>
							<?php if ($this->error->getCode() != 403) { ?>
								<p><?php echo Lang::txt('If difficulties persist, please contact the system administrator of this site.'); ?></p>
							<?php } else { ?>
								<p><?php echo Lang::txt('If difficulties persist and you feel that you should have access to the page, please file a trouble report by clicking on the Help! option on the menu above.'); ?></p>
							<?php } ?>
						</div><!-- / #errorbox -->

						<form method="get" action="<?php echo Route::url('index.php?option=com_search'); ?>">
							<fieldset>
								<?php echo Lang::txt('Please try the'); ?> <a href="<?php echo JURI::base(true); ?>" title="<?php echo Lang::txt('Go to the home page'); ?>"><?php echo Lang::txt('Home Page'); ?></a> <span><?php echo Lang::txt('or'); ?></span>
								<label>
									<?php echo Lang::txt('Search:'); ?>
									<input type="text" name="searchword" value="" />
								</label>
								<input type="submit" value="<?php echo Lang::txt('Go'); ?>" />
							</fieldset>
						</form>
					</div><!-- / #outline -->
					<?php if ($this->debug) { ?>
						<div id="techinfo">
							<?php echo $this->renderBacktrace(); ?>
						</div>
					<?php } ?>
				</div><!-- / #content-wrap -->
			</main><!-- / #content -->
		</div><!-- / #wrap -->

		<footer id="footer">
			<!-- Start footer modules output -->
			<?php echo Module::position('footer'); ?>
			<!-- End footer modules output -->
		</footer><!-- / #footer -->

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
