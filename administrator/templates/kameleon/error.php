<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();

$browser = new \Hubzero\Browser\Detector();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $browser->name() . ' ' . $browser->name() . $browser->major(); ?>"> <!--<![endif]-->
	<head>
		<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/error.css?v=<?php echo filemtime(JPATH_ROOT . '/administrator/templates/' . $this->template . '/css/error.css'); ?>" />

	<?php if ($this->direction == 'rtl') : ?>
		<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/common/rtl.css" />
	<?php endif; ?>

	<?php if (JDEBUG) : ?>
		<link type="text/css" rel="stylesheet" href="../media/cms/css/debug.css" />
	<?php endif; ?>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
	</head>
	<body id="error-body">
		<header id="header" role="banner">
			<h1><a href="<?php echo JURI::root(); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>

			<ul class="user-options">
				<li data-title="<?php echo JText::_('TPL_KAMELEON_LOG_OUT'); ?>">
					<?php
						//Display an harcoded logout
						$task = JRequest::getCmd('task');
						if ($task == 'edit' || $task == 'editA' || JRequest::getInt('hidemainmenu')) {
							$logoutLink = '';
						} else {
							$logoutLink = JRoute::_('index.php?option=com_login&task=logout&' . JUtility::getToken() . '=1');
						}
						$output = array();
						// Print the Preview link to Main site.
						//$juser = JFactory::getUser();
						//$output[] = '<span class="viewsite"><a href="'.JURI::root().'" rel="external">'.JText::_('JGLOBAL_VIEW_SITE').'</a></span>';
						//$output[] = '<span>' . $juser->get('name') .' (' . $juser->get('username') . ')</span>';
						// Print the logout link.
						$output[] = '<a class="logout" href="' . $logoutLink . '">' . JText::_('TPL_KAMELEON_LOG_OUT') . '</a>';
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
		</header><!-- / header -->

		<div id="wrap">
			<nav role="navigation" class="main-navigation">
				<div class="inner-wrap">
					<ul id="menu">
						<li><a href="index.php"><?php echo JText::_('TPL_KAMELEON_CONTROL_PANEL') ?></a></li>
						<li><a href="index.php?option=com_admin&amp;view=help"><?php echo JText::_('TPL_KAMELEON_HELP'); ?></a></li>
					</ul>
				</div>
			</nav><!-- / .navigation -->

			<section id="component-content">
				<div id="toolbar-box" class="toolbar-box">
					<div class="pagetitle icon-48-alert">
						<h2><?php echo JText::_('TPL_KAMELEON_ERROR_OCCURRED'); ?></h2>
					</div>
				</div><!-- / #toolbar-box -->

				<div id="errorbox">
					<div class="col width-50 fltlft">
						<h3 class="error-code"><?php echo $this->error->getCode() ?></h3>
					</div>
					<div class="col width-50 fltrt">
						<p class="error"><?php echo $this->error->getMessage(); ?></p>
					</div>
					<div class="clr"></div>
				</div>

			<?php if ($this->debug) { ?>
				<div class="backtrace-wrap">
					<?php echo $this->renderBacktrace(); ?>
				</div>
			<?php } ?>

				<noscript>
					<?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
				</noscript>
			</section>
		</div>

		<footer id="footer">
			<section class="basement">
				<p class="copyright">
					<?php echo JText::sprintf('TPL_KAMELEON_COPYRIGHT', JURI::root(), $app->getCfg('sitename'), date("Y")); ?>
				</p>
				<p class="promotion">
					<?php echo JText::sprintf('TPL_KAMELEON_POWERED_BY', \Hubzero\Version\Version::VERSION); ?>
				</p>
			</section><!-- / .basement -->
		</footer><!-- / #footer -->
	</body>
</html>