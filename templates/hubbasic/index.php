<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$config =& JFactory::getConfig();

$juser =& JFactory::getUser();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
<jdoc:include type="head" />
<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/print.css" />
<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie8.css" />
<![endif]-->
<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie7.css" />
<![endif]-->
 </head>
 
<body <?php if ($this->countModules( 'banner or welcome' )) { echo "class=\"frontpage\""; } ?>>
<jdoc:include type="modules" name="notices" />
	<div id="top">
		<a name="top"></a>
		<p class="skip" id="to-content"><a href="#content">Skip to content</a></p>
<?php if ($this->countModules( 'helppane' )) : ?>
		<p id="tab">
			<a href="/support/" title="Need help? Send a trouble report to our support team.">
				<span>Need Help?</span>
			</a>
		</p>
<?php endif; ?>
		<div class="clear"></div>
	</div><!-- / #top -->
	
	<jdoc:include type="modules" name="helppane" />
	
	<div id="header">
		<div id="header-wrap">
			<a name="header"></a>
			<h1>
				<a href="<?php echo $this->baseurl ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
					<?php echo $config->getValue('config.sitename'); ?> 
					<span id="tagline">powered by HUBzero&reg;</span>
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
		
			<jdoc:include type="modules" name="search" />
		</div><!-- / #header-wrap -->
	</div><!-- / #header -->
	
	<div id="nav">
		<a name="nav"></a>
		<h2>Navigation</h2>
		<jdoc:include type="modules" name="user3" />
		<div class="clear"></div>
	</div><!-- / #nav -->

<?php if ($this->countModules( 'banner or welcome' ) && $option == 'com_content') : ?>
	<div id="home-splash">
		<div id="features">
<?php if ($this->countModules( 'banner' )) : ?>
			<jdoc:include type="modules" name="banner" />
<?php else : ?>
			<img src="/templates/hubbasic/html/mod_xflash/images/noflash.jpg" alt="" />
<?php endif; ?>
		</div><!-- / #features -->
<?php if ($this->countModules( 'welcome' )) : ?>
		<div id="welcome">
			<jdoc:include type="modules" name="welcome" />
		</div><!-- / #welcome -->
<?php endif; ?>
	</div><!-- / #home-splash -->
<?php endif; ?>

<?php if (!$this->countModules( 'banner or welcome' )) : ?>
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
<?php endif; ?>

	<div id="wrap">
		<div id="content" class="<?php echo $option; ?>">
			<div id="content-wrap">
			<a name="content"></a>
<?php if ($this->countModules( 'left' )) : ?>
				<div class="main section withleft">
					<div class="aside">
						<jdoc:include type="modules" name="left" />
					</div><!-- / .aside -->
					<div class="subject">
<?php endif; ?>
<?php if ($this->countModules('right')) : ?>
				<div class="main section">
					<div class="aside">
						<jdoc:include type="modules" name="right" />
					</div><!-- / .aside -->
					<div class="subject">
<?php endif; ?>
				<!-- Start component output -->
				<jdoc:include type="component" />
				<!-- End component output -->
<?php if ($this->countModules('left or right')) : ?>
					</div><!-- / .subject -->
					<div class="clear"></div>
				</div><!-- / .main section -->
<?php endif; ?>
			</div><!-- / #content-wrap -->
		</div><!-- / #content -->
	</div><!-- / #wrap -->
	
	<div id="footer">
		<a name="footer"></a>
		<!-- Start footer modules output -->
		<jdoc:include type="modules" name="footer" />
		<!-- End footer modules output -->
	</div><!-- / #footer -->
 </body>
</html>
<?php
$title = $this->getTitle();
$this->setTitle( $config->getValue('config.sitename').' - '.$title );
?>
