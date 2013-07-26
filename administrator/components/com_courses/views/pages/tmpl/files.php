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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
if ($this->getError()) {
	echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}
?>
<script type="text/javascript">
function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['imgManager'].location.href='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&task=list&tmpl=component&course=<?php echo $this->course_id; ?>&listdir=' + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].subdir;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&task=list&tmpl=component&course=<?php echo $this->course_id; ?>&listdir=' + listdir.value +'&subdir='+ dir;
}
</script>

<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>
			<span><?php echo DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . ($this->course_id ? $this->course_id . DS : '') . 'pagefiles' . ($this->listdir ? DS . $this->listdir : ''); ?>
			<?php // Files- echo $this->dirPath; ?></span>
		</legend>
		<div id="ajax-uploader-before">&nbsp;</div>
		<div id="ajax-uploader" data-action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=upload&amp;course=<?php echo $this->course_id; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1&amp;<?php echo JUtility::getToken(); ?>=1">
		<table>
			<tbody>
				<tr>
					<td>
						<input type="file" name="upload" id="upload" />
					</td>
					<td>
						<input type="submit" value="<?php echo JText::_('Upload'); ?>" />
					</td>
				</tr>
				<!-- <tr>
					<td><label for="foldername"><?php echo JText::_('Create folder'); ?></label></td>
					<td colspan="2"><input type="text" name="foldername" id="foldername" /></td>
				</tr>
				<tr>
					<td> </td>
					<td colspan="2"><input type="submit" value="<?php echo JText::_('Create or Upload'); ?>" /></td>
				</tr> -->
			</tbody>
		</table>
		</div>
		<script type="text/javascript" src="/media/system/js/jquery.js"></script>
		<script type="text/javascript" src="/media/system/js/jquery.noconflict.js"></script>
		<script type="text/javascript" src="/media/system/js/jquery.fileuploader.js"></script>
		<script type="text/javascript">
		jQuery(document).ready(function(jq){
			var $ = jq;
			
			if ($("#ajax-uploader").length) {
				var uploader = new qq.FileUploader({
					element: $("#ajax-uploader")[0],
					action: $("#ajax-uploader").attr("data-action"), // + $('#field-dir').val()
					//params: {listdir: $('#listdir').val()},
					multiple: true,
					debug: true,
					template: '<div class="qq-uploader">' +
								'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
								'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
								'<ul class="qq-upload-list"></ul>' + 
							   '</div>',
					/*onSubmit: function(id, file) {
						//$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
					},*/
					onComplete: function(id, file, response) {
						$('#imgManager').attr('src', $('#imgManager').attr('src'));
					}
				});
			}
		});
		</script>
		<!-- <script type="text/javascript" src="components/com_courses/assets/js/fileupload.jquery.js"></script> -->
		<style>
		/* Drag and drop file upload */
			#ajax-uploader-before {
				height: 0;
				padding: 1.6em 0;
				overflow: hidden;
			}
			#adminForm fieldset legend:after {
				content: "";
				display: table;
				line-height: 0;
				clear: left;
			}
			.qq-uploading {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 107px;
				color: #fff;
				font-size: 18px;
				padding: 75px 0 0 0;
				text-align: center;
				background: rgba(0,0,0,0.75);
			}
			.qq-uploader {
				position: relative;
				margin: 0;
				padding: 0;
			}
			.qq-upload-button,
			.qq-upload-drop-area {
				background: #f7f7f7;
				border: 3px dashed #ddd;
				text-align: center;
				color: #bbb;
				text-shadow: 0 1px 0 #FFF;
				padding: 0;
				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				-ms-border-radius: 3px;
				-o-border-radius: 3px;
				border-radius: 3px;
				font-size: 1.1em;
				font-weight: bold;
			}
			/*.asset-uploader:hover {
				border: 3px solid #333;
			}*/
			.asset-uploader .columns {
				margin-top: 0;
				padding-top: 0;
			}
			.qq-upload-button,
			.qq-upload-drop-area {
				text-align: center;
				padding: 0.4em 0;
			}
			.qq-upload-button span,
			.qq-upload-drop-area span {
				position: relative;
				padding-left: 1.5em;
			}
			.qq-upload-button span:before,
			.qq-upload-drop-area span:before {
				display: block;
				position: absolute;
				top: 0em;
				left: -0.2em;
				font-family: "Fontcons";
				content: "\f08c"; /*"\f046";*/
				font-size: 1.1em;
				line-height: 1;
				content: "\f016";
				left: 0;
				font-weight: normal;
			}
			.qq-upload-button:hover,
			.qq-upload-drop-area:hover,
			.qq-upload-drop-area-active {
				/*background: #fdfce4;*/
				border: 3px solid #333;
				color: #333;
				cursor: pointer;
			}
			.qq-upload-drop-area {
				position: absolute;
				top: 1.5em;
				left: 0;
				right: 0;
			}
			.qq-upload-list {
				display: none;
			}
		</style>

		<div id="themanager" class="manager">
			<iframe src="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=list&amp;tmpl=component&amp;listdir=<?php echo $this->listdir; ?>&amp;course=<?php echo $this->course_id; ?>" name="imgManager" id="imgManager" width="98%" height="150"></iframe>
		</div>
	</fieldset>
	
	<fieldset>
		

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="course" value="<?php echo $this->course_id; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
