<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->lat = 20;
$this->long = 0;
$this->zoom = 2;

$dataurl = Route::url('index.php?option='.$this->option.'&task='.$this->task.'&type='.$this->type.'&no_html=1&data=online');
$dataurl = str_replace('&amp;', '&', $dataurl);
?>
<!DOCTYPE html>
<html dir="<?php echo Document::getDirection(); ?>" lang="<?php echo Document::getLanguage(); ?>" class="no-js">
	<head>
		<title><?php echo Lang::txt('COM_USAGE') . ': ' . Lang::txt('Maps'); ?></title>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->key; ?>&sensor=false"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/util.js"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/online.js"></script>
		<link rel="stylesheet" href="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/css/maps.css" type="text/css" />
	</head>
	<body>
		<main>
		<h1 class="sr-only"><?php echo Lang::txt('COM_USAGE') . ': ' . Lang::txt('Maps'); ?></h1>
		<div id="div_map"
			data-url="<?php echo ($this->mappath) ? $this->mappath . '/whoisonline.xml' : $dataurl; ?>"
			data-path="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps"
			data-lat="<?php echo $this->lat; ?>"
			data-long="<?php echo $this->long; ?>"
			data-zoom="<?php echo $this->zoom; ?>">
		</div>
		</main>
	</body>
</html>
