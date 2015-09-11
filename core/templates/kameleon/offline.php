<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);

$this->setTitle(Config::get('sitename') . ' - ' . Lang::txt('Down for maintenance'));
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $this->direction; ?> ie ie9"> <![endif]-->
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
							<li>Powered by the <a href="http://hubzero.org" rel="external">HUBzero<sup>&reg;</sup> platform</a></li>
						</ul><!-- / footer #legalese -->
					</div>
				</div><!-- / #footer -->
			</div><!-- / #wrap -->
		</div><!-- / #container -->
	</body>
</html>
