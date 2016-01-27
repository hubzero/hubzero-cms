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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

if ($this->getError())
{
	echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}
?>
<script type="text/javascript">
function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['imgManager'].location.href='<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&course=' . $this->course_id); ?>&listdir=' + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].subdir;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href='<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&course=' . $this->course_id); ?>&listdir=' + listdir.value +'&subdir='+ dir;
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>
			<span><?php echo Lang::txt('COM_COURSES_FILES'); ?> - <?php echo DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->course_id . DS . $this->listdir; ?> <?php echo $this->dirPath; ?></span>
		</legend>
		<div id="ajax-uploader-before">&nbsp;</div>
		<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=upload&course=' . $this->course_id . '&listdir=' . $this->listdir . '&no_html=1&' . Session::getFormToken() . '=1'); ?>">
			<table>
				<tbody>
					<tr>
						<td>
							<input type="file" name="upload" id="upload" />
						</td>
						<td style="white-space:nowrap;">
							<input type="checkbox" name="batch" id="batch" value="1" /> <label for="batch"><?php echo Lang::txt('Unpack (.zip, .tar, etc)'); ?></label>
						</td>
						<td>
							<input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<script type="text/javascript" src="../core/assets/js/jquery.fileuploader.js"></script>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			if ($("#ajax-uploader").length) {
				var uploader = new qq.FileUploader({
					element: $("#ajax-uploader")[0],
					action: $("#ajax-uploader").attr("data-action"),
					multiple: true,
					debug: true,
					template: '<div class="qq-uploader">' +
								'<div class="qq-upload-button"><span><?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
								'<div class="qq-upload-drop-area"><span><?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
								'<ul class="qq-upload-list"></ul>' +
							'</div>',
					onComplete: function(id, file, response) {
						$('#imgManager').attr('src', $('#imgManager').attr('src'));
					}
				});
			}
		});
		</script>
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
				top: 0;
				left: 0;
				right: 0;
			}
			.qq-upload-list {
				display: none;
			}
		</style>

		<div id="themanager" class="manager">
			<iframe src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&listdir=' . $this->listdir . '&subdir=' . $this->subdir . '&course=' . $this->course_id); ?>" name="imgManager" id="imgManager" width="98%" height="150"></iframe>
		</div>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="course" value="<?php echo $this->course_id; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>
