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

ximport('Hubzero_Document');
ximport('Hubzero_Module_Helper');

$config =& JFactory::getConfig();
$juser =& JFactory::getUser();

$this->template = 'hubbasic';

ximport('Hubzero_Browser');
$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" type="text/css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />
<?php if (JPluginHelper::isEnabled('system', 'jquery')) { ?>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.tools.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.jquery.js"></script>
<?php } else { ?>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/mootools.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.js"></script>
<?php } ?>
		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" />
		<![endif]-->
	</head>
	<body>
		<?php Hubzero_Module_Helper::displayModules('notices'); ?>
		<div id="top">
			<a name="top"></a>
			<p class="skip" id="to-content"><a href="#content">Skip to content</a></p>
			<p id="tab">
				<a href="/support/" title="Need help? Send a trouble report to our support team.">
					<span>Need Help?</span>
				</a>
			</p>
			<div class="clear"></div>
		</div><!-- / #top -->
	
		<?php Hubzero_Module_Helper::displayModules('helppane'); ?>
	
		<div id="header">
			<div id="header-wrap">
				<a name="header"></a>
				<h1>
					<a href="." title="<?php echo $config->getValue('config.sitename'); ?>">
						<?php echo $config->getValue('config.sitename'); ?> 
						<span id="tagline">A HUBzero site</span>
					</a>
				</h1>
		
				<ul id="toolbar" class="<?php if (!$juser->get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
<?php
	if (!$juser->get('guest')) {
		// Find the user's most recent support tickets
		ximport('Hubzero_Message_Helper');
		$database =& JFactory::getDBO();
		$recipient = new Hubzero_Message_Recipient( $database );
		$rows = $recipient->getUnreadMessages( $juser->get('id'), 0 );
?>
					<li id="logout"><a href="<?php echo JRoute::_('index.php?option=com_logout'); ?>"><span><?php echo JText::_('Logout'); ?></span></a></li>
					<li id="myaccount"><a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>"><span><?php echo JText::_('My Account'); ?></span></a></li>
					<li id="username"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=profile'); ?>"><?php echo $juser->get('name'); ?> (<?php echo $juser->get('username'); ?>)</a></li>
					<li id="usermessages"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=inbox'); ?>"><?php echo count($rows); ?> New Messages</a></li>
<?php } else { ?>
					<li id="login"><a href="<?php echo JRoute::_('index.php?option=com_login'); ?>" title="<?php echo JText::_('Login'); ?>"><?php echo JText::_('Sign In'); ?></a></li>
<?php } ?>
				</ul>
		
				<?php Hubzero_Module_Helper::displayModules('search'); ?>
			</div><!-- / #header-wrap -->
		</div><!-- / #header -->
	
		<div id="nav">
			<a name="nav"></a>
			<h2>Navigation</h2>
			<?php Hubzero_Module_Helper::displayModules('user3'); ?>
			<div class="clear"></div>
		</div><!-- / #nav -->

		<div id="trail">
			<?php Hubzero_Module_Helper::displayModules('breadcrumbs'); ?>
		</div><!-- / #trail -->

		<div id="wrap">
			<div id="content" class="<?php echo JRequest::getCmd('option', ''); ?>">
				<div id="content-wrap">
					<a name="content"></a>

					<div id="outline">
						<div id="errorbox" class="code-<?php echo $this->error->getCode(); ?>">
							<h2><?php echo $this->error->getMessage(); ?></h2>

							<p><?php echo JText::_('You may not be able to visit this page because of:'); ?></p>

							<ol>
<?php if ($this->error->getCode() != 403) { ?>
								<li><?php echo JText::_('An out-of-date bookmark/favourite.'); ?></li>
								<li><?php echo JText::_('A search engine that has an out-of-date listing for this site.'); ?></li>
								<li><?php echo JText::_('A mis-typed address.'); ?></li>
								<li><?php echo JText::_('The requested resource was not found.'); ?></li>
<?php } ?>
								<li><?php echo JText::_('This page may belong to a group with restricted access.  Only members of the group can view the contents.'); ?></li>
								<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
							</ol>
<?php if ($this->error->getCode() != 403) { ?>
							<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
<?php } else { ?>
							<p><?php echo JText::_('If difficulties persist and you feel that you should have access to the page, please file a trouble report by clicking on the Help! option on the menu above.'); ?></p>
<?php } ?>
						</div><!-- / #errorbox -->

						<form method="get" action="/search">
							<fieldset>
								<?php echo JText::_('Please try the'); ?> <a href="/index.php" title="<?php echo JText::_('Go to the home page'); ?>"><?php echo JText::_('Home Page'); ?></a> <span><?php echo JText::_('or'); ?></span> 
								<label>
									<?php echo JText::_('Search:'); ?> 
									<input type="text" name="searchword" value="" />
								</label>
								<input type="submit" value="<?php echo JText::_('Go'); ?>" />
							</fieldset>
						</form>
					</div><!-- / #outline -->
<?php if ($this->debug) { ?>
					<div id="techinfo">
						<?php echo $this->renderBacktrace(); ?>
					</div>
<?php } ?>
				</div><!-- / #content-wrap -->
			</div><!-- / #content -->
		</div><!-- / #wrap -->
	
		<div id="footer">
			<a name="footer"></a>
			<!-- Start footer modules output -->
			<?php Hubzero_Module_Helper::displayModules('footer'); ?>
			<!-- End footer modules output -->
		</div><!-- / #footer -->
		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
