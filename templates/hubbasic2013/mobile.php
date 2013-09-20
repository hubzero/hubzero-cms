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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//import need HUBzero libraries
ximport('Hubzero_Document');
ximport('Hubzero_Device');

$config =& JFactory::getConfig();

//define tempate
$this->template = 'hubbasic2013';

//get device info
$hd = new Hubzero_Device();

//get joomla version
$joomlaVersion = new JVersion();
$joomlaRelease = 'joomla' . $joomlaVersion->RELEASE;
?>
<!DOCTYPE html>
<html class="<?php echo strtolower($hd->getDeviceFamily() . ' ' . $hd->getDeviceOS() . ' ' . $hd->getDeviceOSVersion()); ?> <?php echo $joomlaRelease; ?>">
	<head>
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'tooltip', 'comments', 'voting', 'icons', 'buttons', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/mobile.css" />

		<jdoc:include type="head" />

		<script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/mobile.js"></script>
	</head>
	<body>
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />
		
		<div id="top" class="mobile-top">
			<div id="masthead" role="banner">
				<div class="inner">
					<h1>
						<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
							<span><?php echo $config->getValue('config.sitename'); ?></span>
						</a>
					</h1>
					<div class="mobile-search">
						<jdoc:include type="modules" name="search" />
					</div>
					<div id="nav" role="main navigation">
						<a name="nav"></a>
						<jdoc:include type="modules" name="user3" />
					</div><!-- / #nav -->
					<select name="menu" id="mobile-nav">
					</select>
				</div><!-- / .inner -->
			</div><!-- / #masthead -->
		</div><!-- / #top -->
		
		<div id="wrap" class="mobile-wrap">
			<div id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
				<div class="inner">
					<a name="content" id="content-anchor"></a>
				<?php if ($this->countModules('left')) : ?>
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
							<!-- start component output -->
							<jdoc:include type="component" />
							<!-- end component output -->
				<?php if ($this->countModules('left or right')) : ?>
						</div><!-- / .subject -->
						<div class="clear"></div>
					</div><!-- / .main section -->
				<?php endif; ?>
				</div><!-- / .inner -->
			</div><!-- / #content -->

			<div id="footer" class="mobile-footer">
				<a name="footer" id="footer-anchor"></a>
				<a href="?tmpl=fullsite">View Full Site</a>
			</div><!-- / #footer -->
		</div><!-- / #wrap -->

		<jdoc:include type="modules" name="endpage" />
		
	</body>
</html>
	