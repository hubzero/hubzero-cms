<?php
/**
 * Gallery view template (legacy PHP).
 *
 * Renders an image gallery with thumbnails, viewer, and toolbar.
 * This is a standalone page (not embedded in the site template).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$imageList   = $this->imageList;
$imageViewer = $this->imageViewer;
$htmlPath    = $this->htmlPath;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Dataview: Image gallery</title>
		<link rel="stylesheet" type="text/css"
			href="/core/assets/css/jquery.ui.min.css" />
		<link rel="stylesheet" type="text/css"
			href="<?php echo $this->escape($htmlPath); ?>/css/gallery.css" />
		<script src="/core/assets/js/jquery.js"></script>
		<script src="/core/assets/js/jquery.ui.min.js"></script>
		<script src="<?php echo $this->escape($htmlPath); ?>/js/gallery.js"></script>
	</head>
	<body>
	<div id="dv_wrapper" class="ui-widget ui-widget-content ui-corner-all">
		<div id="dv_gallery_list" class="ui-widget ui-widget-header ui-corner-top">
			<table style="padding:0; margin:0;">
				<tr>
					<?php foreach ($imageList as $img) : ?>
					<td><?php echo $img; ?></td>
					<?php endforeach; ?>
				</tr>
			</table>
		</div>

		<div id="dv_gallery_viewer">
			<br />
			<?php echo implode("\n", $imageViewer); ?>
			<br />
			<div id="dv_gallery_desc" class="ui-widget ui-widget-content ui-corner-all"
				style="display:none; margin: 0 20px; border-style: inset;">
				The description will be displayed here...
			</div>
			<br />
		</div>

		<div class="dv_gallery_toolbar ui-widget ui-widget-header ui-corner-bottom">
			<span id="dv_gallery_dl_image">
				<a href="" target="_blank">
					<img src="<?php echo $this->escape($htmlPath); ?>/img/download-l.png"
						alt="Click here to download the full size image."
						title="Download Original Image (Right click and save image)"
						style="border: 1px #DDD solid;" />
				</a>
			</span>
			&nbsp;
			<input type="checkbox" id="description" /><label for="description">Description</label>
			[ <span id="color">Background :
				<input type="radio" id="color1" name="color" value="#3C3C3C" checked="checked" />
				<label for="color1">Dark</label>
				<input type="radio" id="color2" name="color" value="#ECECEC" />
				<label for="color2">Light</label>
			</span> ]
			&nbsp;&nbsp;&nbsp;
			<button type="button" id="dv-gallery-close" style="color: red;">Close Window</button>
		</div>
	</div>
	<script>
		document.getElementById('dv-gallery-close').addEventListener('click', function() {
			window.close();
		});
	</script>
	</body>
</html>
