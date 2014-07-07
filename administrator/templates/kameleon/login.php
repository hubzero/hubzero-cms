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

// Load base styles
$this->addStyleSheet('templates/' . $this->template . '/css/login.css?v=' . filemtime(JPATH_ROOT . '/administrator/templates/' . $this->template . '/css/login.css'));
// Load theme
if ($this->params->get('theme') && $this->params->get('theme') != 'gray')
{
	$this->addStyleSheet('templates/' . $this->template . '/css/themes/' . $this->params->get('theme') . '.css');
}
// Load language direction CSS
if ($this->direction == 'rtl')
{
	$this->addStyleSheet('templates/' . $this->template . '/css/common/rtl.css');
}

$browser = new \Hubzero\Browser\Detector();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $browser->name() . ' ' . $browser->name() . $browser->major(); ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />

		<script type="text/javascript" src="templates/<?php echo $this->template; ?>/js/placeholder.js"></script>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="templates/<?php echo $this->template; ?>/js/html5.js"></script>
		<![endif]-->

		<!--[if IE 9]>
			<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link type="text/css" rel="stylesheet" href="templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->

		<script type="text/javascript">
			jQuery(document).ready(function($){
				(function worker() {
					$.ajax({
						url: 'index.php',
						complete: function() {
							setTimeout(worker, 3540000);
						}
					});
				})();
				document.getElementById('form-login').username.select();
				document.getElementById('form-login').username.focus();

				$('input, textarea').placeholder();
			});
		</script>
	</head>
	<body id="login-body">
		<jdoc:include type="modules" name="notices" />

		<header id="header" role="banner">
			<h1><a href="<?php echo JURI::root(); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>
		</header><!-- / header -->

		<div id="wrap">
			<section id="component-content">
				<div id="toolbar-box">
					<h2><?php echo JText::_('TPL_KAMELEON_ADMIN_LOGIN'); ?></h2>
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
				</section><!-- / #main -->
			</section><!-- / #component-content -->
		</div><!-- / #wrap -->
	</body>
</html>