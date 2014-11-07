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
defined('_JEXEC') or die('Restricted access');

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$template = 'hubbasic';
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />

		<?php if ($this->direction == 'rtl' && (!file_exists(JPATH_THEMES . DS . $template . DS . 'css/component_rtl.css') || !file_exists(JPATH_THEMES . DS . $template . DS . 'css/component.css'))) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template_rtl.css" type="text/css" />
		<?php elseif ($this->direction == 'rtl' ) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component.css" type="text/css" />
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component_rtl.css" type="text/css" />
		<?php elseif ($this->direction == 'ltr' && !file_exists(JPATH_THEMES . DS . $template . DS . 'css/component.css')) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template.css" type="text/css" />
		<?php elseif ($this->direction == 'ltr' ) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component.css" type="text/css" />
		<?php endif; ?>

		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body class="contentpane" id="component-body">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>