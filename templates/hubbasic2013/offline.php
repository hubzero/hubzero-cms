<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

$this->template = 'hubbasic2013';

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);

$this->setTitle(Config::get('sitename') . ' - ' . Lang::txt('Down for maintenance'));
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<meta name="viewport" content="width=device-width" />
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge" /><![endif]-->

		<jdoc:include type="head" />

		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/offline.css" />
	</head>
	<body>

		<div id="container">
			<div id="top">
				<div id="masthead" role="banner">
					<div class="inner">
						<h1>
							<a href="<?php echo $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
								<span><?php echo Config::get('sitename'); ?></span>
							</a>
						</h1>
					</div>
				</div>

				<div id="sub-masthead">
					<div class="inner">
						<div id="trail">
							<span class="pathway"><?php echo Lang::txt('TPL_HUBBASIC_TAGLINE'); ?></span>
						</div><!-- / #trail -->
					</div><!-- / .inner -->
				</div><!-- / #sub-masthead -->

				<div id="splash">
					<div class="inner-wrap">
						<div class="inner">
							<div class="wrap">
								<jdoc:include type="message" />
								<div id="offline-message">
									<h2><?php echo Lang::txt('TPL_HUBBASIC_OFFLINE'); ?></h2>
									<p>
										<?php echo Config::get('offline_message'); ?>
									</p>
								</div>
							</div><!-- / .wrap -->
						</div><!-- / .inner -->
					</div><!-- / .inner-wrap -->
				</div><!-- / #splash -->
			</div><!-- / #top -->

			<div id="wrap">
		 		<div id="footer">
					<div class="inner">
						<ul id="legalese">
							<li class="policy">Copyright &copy; <?php echo gmdate("Y"); ?> <?php echo Config::get('sitename'); ?></li>
							<li>Powered by <a href="http://hubzero.org" rel="external">HUBzero<sup>&reg;</sup></a>, a <a href="http://www.purdue.edu" title="Purdue University" rel="external">Purdue</a> project</li>
						</ul><!-- / footer #legalese -->
					</div>
				</div><!-- / #footer -->
			</div><!-- / #wrap -->
		</div><!-- / #container -->
	</body>
</html>