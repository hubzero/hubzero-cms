<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie7.css" />
<![endif]-->
<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/ie6.css" />
<![endif]-->
 </head>
 
 <body>
<jdoc:include type="modules" name="notices" />
	<div id="header">
		<h1><a href="<?php echo $this->baseurl ?>" title="<?php echo $config->getValue('config.sitename'); ?>"><?php echo $config->getValue('config.sitename'); ?></a></h1>
		
		<ul id="toolbar" class="<?php if (!$juser->get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
<?php
if (!$juser->get('guest')) {
	// Find the user's most recent support tickets
	ximport('xmessage');
	$database =& JFactory::getDBO();
	$recipient = new XMessageRecipient( $database );
	$rows = $recipient->getUnreadMessages( $juser->get('id'), 0 );
	
	echo "\t\t\t".'<li id="logout"><a href="/logout"><span>Logout</span></a></li>'."\n";
	echo "\t\t\t".'<li id="myaccount"><a href="/members/'.$juser->get('id').'"><span>My Account</span></a></li>'."\n";
	echo "\t\t\t".'<li id="username"><a href="/members/'.$juser->get('id').'"><span>'.$juser->get('name').' ('.$juser->get('username').')</span></a></li>'."\n";
	echo "\t\t\t".'<li id="usermessages"><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=inbox').'">'.count($rows).' New Message(s)</a></li>'."\n";
} else {
	echo "\t\t\t".'<li id="login"><a href="/login" title="Login">Login</a></li>'."\n";
	echo "\t\t\t".'<li id="register"><a href="/register" title="Sign up for a free account">Register</a></li>'."\n";
}
?>
		</ul>
		
		<jdoc:include type="modules" name="search" />
<?php if ($this->countModules( 'helppane' )) : ?>
		<p id="tab"><a href="/support/" title="Need help? Send a trouble report to our support team."><span>Help!</span></a></p>
<?php endif; ?>
	</div><!-- / #header -->
	
	<div id="nav">
		<h2>Navigation</h2>
		<jdoc:include type="modules" name="user3" />
		<jdoc:include type="modules" name="introblock" />
		<div class="clear"></div>
	</div><!-- / #nav -->

	<jdoc:include type="modules" name="helppane" />
	<div id="afterclear">&nbsp;</div>

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
	</div>
<?php endif; ?>
  <div id="wrap">
	<div id="content" class="<?php echo $option; ?>">
<?php if ($this->countModules( 'left' )) : ?>
		<div class="main section withleft">
		<div class="aside">
			<jdoc:include type="modules" name="left" />
		</div><!-- / #column-left -->
		<div class="subject">
<?php endif; ?>
<?php if ($this->countModules('right')) : ?>
		<div class="main section">
		<div class="aside">
			<jdoc:include type="modules" name="right" />
		</div><!-- / .aside -->
		<div class="subject">
<?php endif; ?>
			<!-- innerwrap is used to fix some IE 6 display bugs -->
			<div class="innerwrap<?php if ($this->countModules('banner or welcome')) : echo ' frontpage'; endif; ?>">
				<jdoc:include type="component" />
			</div><!-- / .innerwrap -->
<?php if ($this->countModules('left or right')) : ?>
		</div><!-- / .subject -->
		<div class="clear"></div>
		</div><!-- / .main section -->
<?php endif; ?>
	</div><!-- / #content -->
	
	<div id="footer">
	    <jdoc:include type="modules" name="footer" />
	</div><!-- / #footer -->
  </div><!-- / #wrap -->
 </body>
</html>
<?php
$title = $this->getTitle();
$this->setTitle( $config->getValue('config.sitename').' - '.$title );
?>