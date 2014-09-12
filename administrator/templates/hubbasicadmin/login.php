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

jimport('joomla.filesystem.file');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// Load CSS
$doc->addStyleSheet('templates/'.$this->template.'/css/login.css');
if ($this->params->get('theme') && $this->params->get('theme') != 'gray') {
	$doc->addStyleSheet('templates/' . $this->template . '/css/themes/' . $this->params->get('theme') . '.css');
}

// Load language direction CSS
if ($this->direction == 'rtl') {
	$doc->addStyleSheet('templates/'.$this->template.'/css/common/rtl.css');
}

// Load debug CSS if enabled
if (JPluginHelper::isEnabled('system', 'debug')) {
	$doc->addStyleSheet('templates/' . $this->template . '/css/common/debug.css');
}

ximport('Hubzero_Browser');
$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

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
		<jdoc:include type="head" />
<?php if ($b == 'firefox' && intval($v) < 4) { ?>
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
		<script type="text/javascript">
			function keepAlive() {
				if (MooTools.version == '1.11') {
					var myAjax = new Ajax('index.php', {method: 'get'}).request();
				} else {
					var myAjax = new Request({method: "get", url: "index.php"}).send();
				}
			}
			window.addEvent('domready', function () {
				keepAlive.periodical(3540000);
				document.getElementById('form-login').username.select();
				document.getElementById('form-login').username.focus();
			});
		</script>
	</head>
	<body id="login-body">
		<jdoc:include type="modules" name="notices" />
		<header id="header" role="banner">
			<h1><a href="<?php echo JURI::root(); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>
			<div class="clr"></div>
		</header><!-- / header -->
		
		<div id="wrap">
			<section id="component-content">
				<div id="toolbar-box">
					<h2><?php echo JText::_('Administration Login') ?></h2>
				</div>

				<section id="main" class="<?php echo JRequest::getCmd('option', ''); ?>">
					<!-- Notifications begins -->
					<jdoc:include type="message" />
					<!-- Notifications ends -->
					<!-- Content begins -->
					<jdoc:include type="component" />
					<!-- Content ends -->
					<noscript>
						<?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
					</noscript>
					<div class="clr"></div>
				</section><!-- / #main -->
			</section><!-- / #content -->
		</div><!-- / #wrap -->
	</body>
</html>