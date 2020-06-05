<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dataurl = Route::url('index.php?option='.$this->option.'&task='.$this->task.'&type='.$this->type.'&no_html=1&data=locations');
$dataurl = str_replace('&amp;', '&', $dataurl);
?>
<!DOCTYPE html>
<html dir="<?php echo Document::getDirection(); ?>" lang="<?php echo Document::getLanguage(); ?>" class="no-js">
	<head>
		<script type="text/javascript" src="https://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $this->key; ?>"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/Clusterer2.js"></script>
		<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/js/web_gradient.js"></script>
		<link rel="stylesheet" href="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/assets/css/maps.css" type="text/css" />
	</head>
	<body>
		<div id="div_map"
			data-url="<?php echo $dataurl; ?>"
			data-map="<?php echo $this->mappath; ?>"
			data-path="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps"
			data-lat="<?php echo $this->lat; ?>"
			data-long="<?php echo $this->long; ?>"
			data-zoom="<?php echo $this->zoom; ?>">
		</div>
		<table>
			<tbody>
				<tr>
					<th>Usage:</th>
					<td>&nbsp;&nbsp;&nbsp;<img src="1.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="2.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="3.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="4.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="5.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="6.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="7.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="8.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="9.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="10.png" width="12" height="20" alt="" />&nbsp;&nbsp;&nbsp;></td>
					<td>&nbsp;&nbsp;&nbsp;<img src="11.png" width="12" height="20" alt="" /></td>
				</tr>
			</tbody>
		</table>
	</body>
</html>
