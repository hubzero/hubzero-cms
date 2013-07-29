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
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

$canDo = CoursesHelper::getActions('course');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . $text . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');

$editor =& JEditor::getInstance();

/*$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}
$gparams = new $paramsClass($this->row->get('params'));
*/
//$membership_control = $gparams->get('membership_control', 1);

//$display_system_users = $gparams->get('display_system_users', 'global');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// form field validation
	if (document.getElementById('field-title').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	//} else if (form.getElementById('field-alias').value == '') {
	//	alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_DETAILS'); ?></span></legend>
			
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
						<td>
							<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" size="50" />
							<span class="hint"><?php echo JText::_('Alpha-numeric characters only. If left blank, alias will be generated from the title.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>:</label></td>
						<td><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="field-blurb"><?php echo JText::_('Blurb'); ?>:</label></td>
						<td>
							<textarea name="fields[blurb]" id="field-blurb" cols="40" rows="3"><?php echo $this->escape(stripslashes($this->row->get('blurb'))); ?></textarea>
							<span class="hint"><?php echo JText::_('This is a short sentence or two for the catalog.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="field-description"><?php echo JText::_('Description'); ?>:</label></td>
						<td>
							<textarea name="fields[description]" id="field-description" cols="40" rows="15"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
							<span class="hint"><?php echo JText::_('This is a longer, detailed description of the course.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="field-tags"><?php echo JText::_('Tags'); ?>:</label></td>
						<td>
							<textarea name="tags" id="field-tags" cols="40" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Managers'); ?></span></legend>
<?php if ($this->row->get('id')) { ?>
			<iframe width="100%" height="400" name="managers" id="managers" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=managers&amp;tmpl=component&amp;id=<?php echo $this->row->get('id'); ?>"></iframe>
<?php } else { ?>
			<p><?php echo JText::_('Course must be saved before managers can be added.'); ?></p>
<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('COM_COURSES_META_SUMMARY'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('Group ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('group_id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo JText::_('Created'); ?></th>
					<td><?php echo $this->escape($this->row->get('created')); ?></td>
				</tr>
<?php } ?>
<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo JText::_('Creator'); ?></th>
					<td><?php 
					$creator = JUser::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" valign="top"><label for="field-state"><?php echo JText::_('State'); ?>:</label></td>
						<td>
							<select name="fields[state]" id="field-state">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Published'); ?></option>
								<option value="3"<?php if ($this->row->get('state') == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('Draft'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Deleted'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<?php

			JPluginHelper::importPlugin('courses');
			$dispatcher =& JDispatcher::getInstance();

			if ($plugins = $dispatcher->trigger('onCourseEdit'))
			{
				$pth = false;
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$pth = true;
					$paramsClass = 'JRegistry';
				}

				$data = $this->row->get('params');

				foreach ($plugins as $plugin)
				{
					$param = new $paramsClass(
						(is_object($data) ? $data->toString() : $data),
						JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . ($pth ? DS . $plugin['name'] : '') . '.xml'
					);
					$out = $param->render('params', 'onCourseEdit');
					if (!$out) 
					{
						continue;
					}
					?>
					<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
						<legend><?php echo JText::sprintf('%s Parameters', $plugin['title']); ?></legend>
						<?php echo $out; ?>
					</fieldset>
					<?php
				}
			}
		?>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('IMAGE'); ?></span></legend>
			
			<?php
			if ($this->row->exists()) {
				$pics = stripslashes($this->row->get('logo'));
				$pics = explode(DS, $pics);
				$file = end($pics);
			?>
			<div style="padding-top: 2.5em">
				<div id="ajax-uploader" data-action="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;task=upload&amp;id=<?php echo $this->row->get('id'); ?>&amp;no_html=1&amp;<?php echo JUtility::getToken(); ?>=1">
					<noscript>
						<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;tmpl=component&amp;file=<?php echo $file; ?>&amp;id=<?php echo $this->row->get('id'); ?>"></iframe>
					</noscript>
				</div>
			</div>
				<?php 
				$width = 0;
				$height = 0;
				$this_size = 0;
				if ($this->row->get('logo')) {
					$path = DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->row->get('id');

					$this_size = filesize(JPATH_ROOT . $path . DS . $file);
					list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . $path . DS . $file);
					$pic = $this->row->get('logo');
				}
				else
				{
					$pic = 'blank.png';
					$path = '/administrator/images';
				}
				?>
				<table class="formed">
					<tbody>
						<tr>
							<td rowspan="6">
								<img id="img-display" src="<?php echo '..' . $path . DS . $pic; ?>" alt="<?php echo JText::_('COM_COURSES_LOGO'); ?>" />
							</td>
							<td><?php echo JText::_('FILE'); ?>:</td>
							<td><span id="img-name"><?php echo $this->row->get('logo', '[ none ]'); ?></span></td>
						</tr>
						<tr>
							<td><?php echo JText::_('SIZE'); ?>:</td>
							<td><span id="img-size"><?php echo Hubzero_View_Helper_Html::formatsize($this_size); ?></span></td>
						</tr>
						<tr>
							<td><?php echo JText::_('WIDTH'); ?>:</td>
							<td><span id="img-width"><?php echo $width; ?></span> px</td>
						</tr>
						<tr>
							<td><?php echo JText::_('HEIGHT'); ?>:</td>
							<td><span id="img-height"><?php echo $height; ?></span> px</td>
						</tr>
						<tr>
							<td><input type="hidden" name="currentfile" id="currentfile" value="<?php echo $file; ?>" /></td>
							<td><a id="img-delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;tmpl=component&amp;task=remove&amp;currentfile=<?php echo $this->row->get('logo'); ?>&amp;id=<?php echo $this->row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
						</tr>
					</tbody>
				</table>

				<script type="text/javascript" src="/media/system/js/jquery.js"></script>
				<script type="text/javascript" src="/media/system/js/jquery.noconflict.js"></script>
				<script type="text/javascript" src="/media/system/js/jquery.fileuploader.js"></script>
				<script type="text/javascript">
				String.prototype.nohtml = function () {
					if (this.indexOf('?') == -1) {
						return this + '?no_html=1';
					} else {
						return this + '&no_html=1';
					}
				};
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
								if (response.success) {
									$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
									$('#img-name').text(response.file);
									$('#img-size').text(response.size);
									$('#img-width').text(response.width);
									$('#img-height').text(response.height);

									$('#img-delete').show();
								//$('#imgManager').attr('src', $('#imgManager').attr('src'));
								}
							}
						});
					}
					$('#img-delete').on('click', function (e) {
						e.preventDefault();
						var el = $(this);
						$.getJSON(el.attr('href').nohtml(), {}, function(response) {
							if (response.success) {
								$('#img-display').attr('src', '../administrator/images/blank.png');
								$('#img-name').text('[ none ]');
								$('#img-size').text('0');
								$('#img-width').text('0');
								$('#img-height').text('0');
							}
							el.hide();
						});
					});
				});
				</script>
				<style>
				/* Drag and drop file upload */
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
						margin: 1em;
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
			<?php
			} else {
				echo '<p class="warning">'.JText::_('COM_COURSES_PICTURE_ADDED_LATER').'</p>';
			}
			?>
		</fieldset>
	</div>
	<div class="clr"></div>

<?php /*if (version_compare(JVERSION, '1.6', 'ge')) { ?>
	<?php if ($canDo->get('core.admin')): ?>
	<div class="col width-100 fltlft">
		<fieldset class="panelform">
			<legend><span><?php echo JText::_('COM_COURSES_FIELDSET_RULES'); ?></span></legend>
			<?php echo $this->form->getLabel('rules'); ?>
			<?php echo $this->form->getInput('rules'); ?>
		</fieldset>
	</div>
	<div class="clr"></div>
	<?php endif; ?>
<?php }*/ ?>

	<?php echo JHTML::_('form.token'); ?>
</form>
