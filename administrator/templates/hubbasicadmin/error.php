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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

ximport('Hubzero_Browser');
$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

$juser =& JFactory::getUser();

$jv = 'j15';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$jv = 'j25';
}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $jv . ' ' . $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<link href="templates/<?php echo $this->template; ?>/css/template.css" rel="stylesheet" type="text/css" />
		<link href="templates/<?php echo $this->template; ?>/css/common/icons.css" rel="stylesheet" type="text/css" />
		<link href="templates/<?php echo $this->template; ?>/css/error.css" rel="stylesheet" type="text/css" />
<?php if ($this->direction == 'rtl') : ?>
		<link href="templates/<?php echo $this->template; ?>/css/common/rtl.css" rel="stylesheet" type="text/css" />
<?php endif; ?>
<?php if (JPluginHelper::isEnabled('system', 'debug')) { ?>
		<link href="templates/<?php echo $this->template; ?>/css/common/debug.css" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if ($b == 'firefox' && intval($v) < 4 && $browser->getBrowserMinorVersion() < 5) { ?>
		<link href="templates/<?php echo $this->template; ?>/css/browser/firefox.css" rel="stylesheet" type="text/css" />
<?php } ?>
		<!--[if IE 7]>
			<link href="templates/<?php echo $this->template; ?>/css/browser/ie7.css" rel="stylesheet" type="text/css" />
			<script src="templates/<?php echo $this->template; ?>/js/html5.js" type="text/javascript"></script>
		<![endif]-->
		<!--[if IE 8]>
			<link href="templates/<?php echo $this->template; ?>/css/browser/ie8.css" rel="stylesheet" type="text/css" />
			<script src="templates/<?php echo $this->template; ?>/js/html5.js" type="text/javascript"></script>
		<![endif]-->
	</head>
	<body id="error-body">
		<header id="header" role="banner">
			<h1><a href="<?php echo JURI::root(); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>
			
			<ul class="user-options">
				<li>
					<?php
						//Display an harcoded logout
						$task = JRequest::getCmd('task');
						if ($task == 'edit' || $task == 'editA' || JRequest::getInt('hidemainmenu')) {
							$logoutLink = '';
						} else {
							$logoutLink = JRoute::_('index.php?option=com_login&task=logout&'. JUtility::getToken() .'=1');
						}
						$output = array();
						// Print the Preview link to Main site.
						//$output[] = '<span class="viewsite"><a href="'.JURI::root().'" rel="external">'.JText::_('JGLOBAL_VIEW_SITE').'</a></span>';
						//$output[] = '<span>' . $juser->get('name') .' (' . $juser->get('username') . ')</span>';
						// Print the logout link.
						$output[] = '<a class="logout" href="'.$logoutLink.'">'.JText::_('Log out').'</a>';
						// Reverse rendering order for rtl display.
						if ($this->direction == "rtl") :
							$output = array_reverse($output);
						endif;
						// Output the items.
						foreach ($output as $item) :
						echo $item;
						endforeach;
					?>
				</li>
  			</ul>
			
			<div class="clear"></div>
		</header><!-- / header -->
		
		<div id="wrap">
			<nav role="navigation" class="main-navigation">
				<div class="inner-wrap">
					<ul id="menu">
						<li><a href="index.php"><?php echo JText::_('Site') ?></a></li>
						<li><a href="index.php?option=com_admin&amp;view=help"><?php echo JText::_('Help'); ?></a></li>
					</ul>
				</div>
				<div class="clr"><!-- We need this for the drop downs --></div>
			</nav><!-- / .navigation -->
						
			<section id="component-content">
				<div id="toolbar-box" class="toolbar-box">
					<div class="header icon-48-alert">
						<?php echo JText::_('An error has occurred'); ?>
					</div>
				</div><!-- / #toolbar-box -->
				
				<div id="errorbox">
					<div class="col width-50 fltlft">
						<h3><?php echo $this->error->getCode() ?></h3>
					</div>
					<div class="col width-50 fltrt">
						<p class="error"><?php echo $this->error->getMessage(); ?></p>
					</div>
					<div class="clr"></div>
				</div>
				
				<?php if ($this->debug) :
					echo $this->renderBacktrace();
				endif; ?>
				
				<noscript>
					<?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
				</noscript>
			</section>
		</div>
		
		<footer id="footer">
			<section class="basement">
				<p class="copyright">
					<?php echo $app->getCfg('sitename'); ?></a> &copy; <?php echo date("Y"); ?>. All Rights Reserved.
				</p>
				<p class="promotion">
					<a rel="external" href="http://hubzero.org">Powered by <a href="http://hubzero.org">HUBzero&reg; CMS</a>.</a>
				</p>
			</section><!-- / .basement -->
		</footer><!-- / #footer -->
	</body>
</html>