<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


function view($hash) {
	global $html_path, $com_name;

	if(!isset($_SESSION['dv'])) {
		$_SESSION['dv'] = array();
	}

	if (!isset($_SESSION['dv']['gallery']['list'][$hash])) {
		print "<h2>Invalid Gallery ID or Your session may have expired</h2>Please close this window and refresh the previous View/Page.";
		exit();
	}

	$http_path = $_SESSION['dv']['gallery']['list'][$hash];
	$real_path = explode('/site/', $http_path);
	$real_path = '/site/' . $real_path[1];

	$imagetypes = array('png', 'gif', 'jpg', 'jprg');
	$image_list = array();
	$image_viewer = array();

	if (!is_dir(JPATH_BASE . $real_path)) {
		print "<h2>Error: Missing images.</h2>";
		print "DEBUG: " . JPATH_BASE . $real_path;
		return;
	}

//	$file_list = scandir(JPATH_BASE . $real_path);
	$cmd = 'find ' . JPATH_BASE . $real_path . ' -type f|grep -v small|grep -v medium';
	$file_list = explode("\n", (`$cmd`));
	array_pop($file_list);

	foreach($file_list as $file) {

		$pi = pathinfo($file);
		$dir_name = str_replace(JPATH_BASE, '', $pi['dirname']);
		$ext = strtolower($pi['extension']);

		$desc_file = str_replace($pi['extension'], 'txt', JPATH_BASE . $real_path . '/' . $file);
		$desc = '';
		if(file_exists($desc_file)) {
			$desc = htmlentities(file_get_contents($desc_file), ENT_QUOTES, 'UTF-8');
		}

		if (in_array($ext, $imagetypes)) {
			$image_list[] = '<img title="' . $desc . '" alt="' . $pi['basename'] . '" src="' . $dir_name . '/small/' . $pi['basename'] . '" />';
			$image_viewer[] = '<a title="Click to view the original image." target="_blank" href="' . $dir_name . '/' . $pi['basename'] . '"><img alt="' . $pi['basename'] . '" src="' . $dir_name . '/medium/' . $pi['basename'] . '" style="display:none;" /></a>';
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Dataview: Image gallery</title>
		<link rel="stylesheet" href="<?=$html_path?>/ui/themes/smoothness/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" href="<?=$html_path?>/dv_gallery.css" type="text/css" />
		<script type="text/javascript" src="<?=$html_path?>/jquery.min.js"></script>
		<script type="text/javascript" src="<?=$html_path?>/ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?=$html_path?>/dv_gallery.js"></script>
		<style type="text/css">
			.dv_gallery_toolbar { font: 62.5% "Trebuchet MS", sans-serif;}
		</style>
	</head>
	<body>
	<div id="dv_wrapper" class="ui-widget ui-widget-content ui-corner-all">
		<div id="dv_gallery_list" class="ui-widget ui-widget-header ui-corner-top">
			<table style="padding:0px; margin:0px;">
				<tr>
					<td><?=implode("</td><td>", $image_list);?></td>
				</tr>
			</table>
		</div>

		<div id="dv_gallery_viewer">
			<br />
			<?=implode("\n", $image_viewer);?>
			<br style="line-haight: 5px;" />
			<div id="dv_gallery_desc" class="ui-widget ui-widget-content ui-corner-all" style="display:none; margin: 0px 20px 0px 20px; border-style: inset;">The description will be displayed here...</div>
			<br style="line-haight: 5px;" />
		</div>

		<div class="dv_gallery_toolbar ui-widget ui-widget-header ui-corner-bottom">
			<span id="dv_gallery_dl_image">
				<a href="" target="_blank"><img src="<?=$html_path?>/download-l.png" alt="Click here to download the full size image." title="Download Original Image (Right click and save image)" style="border: 1px #DDD solid;" /></a>
			</span>
			&nbsp;
			<input type="checkbox" id="description" /><label for="description">Description</label>
			[ <span id="color">Background :
				<input type="radio" id="color1" name="color" value="#3C3C3C" checked="checked" /><label for="color1">Dark</label>
				<input type="radio" id="color2" name="color" value="#ECECEC" /><label for="color2">Light</label>
			</span> ]
			&nbsp;&nbsp;&nbsp;
			<input type="button" value="Close Window" style="color: red;" onclick="window.close();">
		</div>
	</div>
	</body>
</html>
<?php
	exit(0);
}
?>
