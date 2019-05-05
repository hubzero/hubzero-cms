<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

$this->setTitle(Config::get('sitename') . ' - ' . Lang::txt('TPL_KIMERA_OFFLINE'));
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