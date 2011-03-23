<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

ximport('Hubzero_Module_Helper');
$config =& JFactory::getConfig();
$juser =& JFactory::getUser();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
 <head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	
	<link rel="stylesheet" href="/templates/hubbasic/css/main.css" type="text/css" />
	<link rel="stylesheet" href="/templates/hubbasic/css/error.css" type="text/css" />
	<link rel="stylesheet" href="/templates/hubbasic/html/mod_reportproblems/mod_reportproblems.css" type="text/css" />
	<script type="text/javascript" src="/media/system/js/mootools.js"></script>
	<script type="text/javascript" src="/templates/hubbasic/js/hub.js"></script>
	<script type="text/javascript" src="/modules/mod_reportproblems/mod_reportproblems.js"></script>

	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/print.css" />
	<!--[if IE 8]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie8.css" />
	<![endif]-->
	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie7.css" />
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
				<a href="<?php echo $this->baseurl ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
					<?php echo $config->getValue('config.sitename'); ?> 
					<span id="tagline">A HUBzero site</span>
				</a>
			</h1>
		
			<ul id="toolbar" class="<?php if (!$juser->get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
<?php
if (!$juser->get('guest')) {
	// Find the user's most recent support tickets
	ximport('Hubzero_Message');
	$database =& JFactory::getDBO();
	$recipient = new Hubzero_Message_Recipient( $database );
	$rows = $recipient->getUnreadMessages( $juser->get('id'), 0 );
	
	echo "\t\t\t\t".'<li id="logout"><a href="/logout"><span>Logout</span></a></li>'."\n";
	echo "\t\t\t\t".'<li id="myaccount"><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id')).'"><span>My Account</span></a></li>'."\n";
	echo "\t\t\t\t".'<li id="username"><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id')).'"><span>'.$juser->get('name').' ('.$juser->get('username').')</span></a></li>'."\n";
	echo "\t\t\t\t".'<li id="usermessages"><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=inbox').'">'.count($rows).' New Message(s)</a></li>'."\n";
} else {
	echo "\t\t\t\t".'<li id="login"><a href="/login" title="Login">Login</a></li>'."\n";
	echo "\t\t\t\t".'<li id="register"><a href="/register" title="Sign up for a free account">Register</a></li>'."\n";
}
?>
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
		You are here: <?php
	$app =& JFactory::getApplication();
	$pathway =& $app->getPathway();
	
	$items = $pathway->getPathWay();
	$l = array();
	foreach ($items as $item) 
	{
		$text = trim(stripslashes($item->name));
		if (strlen($text) > 50) {
			$text = $text.' ';
			$text = substr($text,0,50);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		$url = JRoute::_($item->link);
		$url = str_replace('%20','+',$url);
		$l[] = '<a href="'.$url.'">'.$text.'</a>';
	}
	echo implode(' &rsaquo; ',$l);
	?>
	</div><!-- / #trail -->

	<div id="wrap">
		<div id="content" class="<?php echo $option; ?>">
			<div id="content-wrap">
			<a name="content"></a>

			<div id="outline">
				<div id="errorbox" class="code-<?php echo $this->error->code ?>">
					<h2><?php echo $this->error->message ?></h2>

					<p><?php echo JText::_('You may not be able to visit this page because of:'); ?></p>

					<ol>
<?php if ($this->error->code != 403) { ?>
						<li><?php echo JText::_('An out-of-date bookmark/favourite.'); ?></li>
						<li><?php echo JText::_('A search engine that has an out-of-date listing for this site.'); ?></li>
						<li><?php echo JText::_('A mis-typed address.'); ?></li>
						<li><?php echo JText::_('The requested resource was not found.'); ?></li>
<?php } ?>
						<li><?php echo JText::_('This page may belong to a group with restricted access.  Only members of the group can view the contents.'); ?></li>
						<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
					</ol>
<?php if ($this->error->code != 403) { ?>
					<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
<?php } else { ?>
					<p><?php echo JText::_('If difficulties persist and you feel that you should have access to the page, please file a trouble report by clicking on the Help! option on the menu above.'); ?></p>
<?php } ?>
				</div>

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
			</div>
<?php 
			if ($this->debug || $juser->get('username') == 'nkissebe' || $juser->get('username') == 'zooley') :
				echo "\t\t".'<div id="techinfo">'."\n";
				echo $this->renderBacktrace()."\n";
				echo "\t\t".'</div>'."\n";
			endif;
?>

			</div><!-- / #content-wrap -->
		</div><!-- / #content -->
	</div><!-- / #wrap -->
	
	<div id="footer">
		<a name="footer"></a>
		<!-- Start footer modules output -->
		<?php Hubzero_Module_Helper::displayModules('footer'); ?>
		<!-- End footer modules output -->
	</div><!-- / #footer -->
 </body>
</html>
<?php
$title = $this->getTitle();
$this->setTitle( $config->getValue('config.sitename').' - '.$title );
?>