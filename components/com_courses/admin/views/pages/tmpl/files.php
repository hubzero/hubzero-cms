<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JHTML::_('behavior.framework');

$this->css();
$this->js('jquery.fileuploader.js', 'system');

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
		<legend class="upload-path">
			<span>
				<?php echo Lang::txt('Path') . ': ' . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . ($this->course_id ? $this->course_id . DS : '') . 'pagefiles' . ($this->listdir ? DS . $this->listdir : ''); ?>
			</span>
		</legend>
		<div id="ajax-uploader-before">&nbsp;</div>
		<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=upload&course=' . $this->course_id . '&listdir=' . $this->listdir . '&no_html=1&' . JUtility::getToken() . '=1'); ?>">
			<table>
				<tbody>
					<tr>
						<td>
							<input type="file" name="upload" id="upload" />
						</td>
						<td>
							<input type="submit" value="<?php echo Lang::txt('Upload'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($){
			if ($("#ajax-uploader").length) {
				var uploader = new qq.FileUploader({
					element: $("#ajax-uploader")[0],
					action: $("#ajax-uploader").attr("data-action"),
					multiple: true,
					debug: true,
					template: '<div class="qq-uploader">' +
								'<div class="qq-upload-button"><span>Click or drop file</span></div>' +
								'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
								'<ul class="qq-upload-list"></ul>' +
							   '</div>',
					onComplete: function(id, file, response) {
						$('#imgManager').attr('src', $('#imgManager').attr('src'));
					}
				});
			}
		});
		</script>

		<div id="themanager" class="manager">
			<iframe src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&listdir=' . $this->listdir . '&course=' . $this->course_id); ?>" name="imgManager" id="imgManager" width="98%" height="150"></iframe>
		</div>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="course" value="<?php echo $this->course_id; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
