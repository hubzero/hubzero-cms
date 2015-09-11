<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// Get browser info to set some classes
$browser = new \Hubzero\Browser\Detector();
$cls = array(
	'no-js',
	$browser->name(),
	$browser->name() . $browser->major(),
	$this->direction
);

$this->setTitle(Config::get('sitename') . ' - ' . Lang::txt('Down for maintenance'));
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<jdoc:include type="head" />

		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/offline.css?v=<?php echo filemtime(__DIR__ . '/css/offline.css'); ?>" />
	</head>
	<body id="offline-page">

		<div id="wrap">
			<header id="masthead" role="banner">
				<h1>
					<a href="<?php echo empty($this->baseurl) ? '/' : $this->baseurl; ?>" title="<?php echo Config::get('sitename'); ?>">
						<span><?php echo Config::get('sitename'); ?></span>
					</a>
				</h1>
				<p class="tagline"><?php echo Lang::txt('TPL_KIMERA_TAGLINE'); ?></p>
			</header>

			<main id="content" role="main">
				<div class="inner">
					<jdoc:include type="message" />
					<div id="offline-message">
						<?php if ($msg = Config::get('offline_message', Lang::txt('TPL_KIMERA_OFFLINE'))) : ?>
							<p>
								<?php echo $msg; ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			</main>

			<footer id="footer">
				<p class="copyright">
					<?php echo Lang::txt('TPL_KIMERA_COPYRIGHT', Request::root(), Config::get('sitename'), date("Y")); ?>
				</p>
			</footer>
		</div>

	</body>
</html>