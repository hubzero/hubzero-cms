<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dataurl = Route::url('index.php?option='.$this->option.'&task='.$this->task.'&type='.$this->type.'&no_html=1&data=markers');
$dataurl = str_replace('&amp;', '&', $dataurl);
?>
<!DOCTYPE html>
<html dir="<?php echo Document::getDirection(); ?>" lang="<?php echo Document::getLanguage(); ?>" class="no-js">
	<head>
		<script type="text/javascript" src="https://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $this->key; ?>"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/Clusterer2.js"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/animation.js"></script>
		<link rel="stylesheet" href="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/css/maps.css" type="text/css" />
	</head>
	<body>
		<div id="div_map"
			data-url="<?php echo $dataurl; ?>"
			data-path="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps"
			data-lat="<?php echo $this->lat; ?>"
			data-long="<?php echo $this->long; ?>"
			data-zoom="<?php echo $this->zoom; ?>">
		</div>
	</body>
</html>
