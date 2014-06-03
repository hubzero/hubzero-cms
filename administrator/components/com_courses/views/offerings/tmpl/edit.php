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

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES').': ' . $text . ' ' . JText::_('Offering'), 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();


$base = str_replace('/administrator', '', JURI::base(true));

$this->css();
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
	if ($('field-alias').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('field-title').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
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
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->row->get('course_id'); ?>" />
			<input type="hidden" name="course" value="<?php echo $this->row->get('course_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('Alias'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint">Only numbers, letters, dashes, and underscores allowed. If no alias is provided, one will be generated form the title.</span>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('State'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unpublished'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Published'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Deleted'); ?></option>
				</select>
			</div>

			<p><span class="hint">The following values are optional. If no <strong>start date</strong> is set, the offering will be available immediately upon being published.</span></p>

			<div class="input-wrap">
				<label for="publish_up">Offering starts:</label><br />
				<?php echo JHTML::_('calendar', $this->row->get('publish_up'), 'fields[publish_up]', 'publish_up', "%Y-%m-%d", array('class' => 'inputbox calendar-field')); ?>
				<span class="hint">Format: YYYY-MM-DD hh:mm:ss</span>
			</div>
			<div class="input-wrap">
				<label for="publish_down">Offering ends:</label><br />
				<?php echo JHTML::_('calendar', $this->row->get('publish_down'), 'fields[publish_down]', 'publish_down', "%Y-%m-%d", array('class' => 'inputbox calendar-field')); ?>
				<span class="hint">Format: YYYY-MM-DD hh:mm:ss</span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('Course ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('course_id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Offering ID'); ?></th>
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
			<legend><span><?php echo JText::_('IMAGE'); ?></span></legend>

			<?php
			if ($this->row->exists()) 
			{
				$logo = $this->row->params('logo');
				?>
				<div style="padding-top: 2.5em">
					<div id="ajax-uploader" data-action="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;task=upload&amp;type=offering&amp;id=<?php echo $this->row->get('id'); ?>&amp;no_html=1&amp;<?php echo JUtility::getToken(); ?>=1">
						<noscript>
							<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;tmpl=component&amp;file=<?php echo $logo; ?>&amp;type=offering&amp;id=<?php echo $this->row->get('id'); ?>"></iframe>
						</noscript>
					</div>
				</div>
					<?php 
					$width  = 0;
					$height = 0;
					$this_size = 0;
					if ($logo) 
					{
						$path = $this->row->logo('path');

						$this_size = filesize(JPATH_ROOT . $path . DS . $logo);
						list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . $path . DS . $logo);
						$pic = $logo;
					}
					else
					{
						$pic  = 'blank.png';
						$path = '/administrator/components/com_courses/assets/img';
					}
					?>
					<div id="img-container">
						<img id="img-display" src="<?php echo '..' . $path . DS . $pic; ?>" alt="<?php echo JText::_('COM_COURSES_LOGO'); ?>" />
						<input type="hidden" name="currentfile" id="currentfile" value="<?php echo $this->escape($logo); ?>" />
					</div>
					<table class="formed">
						<tbody>
							<tr>
								<th><?php echo JText::_('File'); ?>:</th>
								<td>
									<span id="img-name"><?php echo $this->row->params('logo', '[ none ]'); ?></span>
								</td>
								<td>
									<a id="img-delete" <?php echo $logo ? '' : 'style="display: none;"'; ?> href="index.php?option=<?php echo $this->option; ?>&amp;controller=logo&amp;tmpl=component&amp;task=remove&amp;currentfile=<?php echo $logo; ?>&amp;type=offering&amp;id=<?php echo $this->row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Delete'); ?>">[ x ]</a>
								</td>
							</tr>
							<tr>
								<th><?php echo JText::_('Size'); ?>:</th>
								<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></span></td>
								<td></td>
							</tr>
							<tr>
								<th><?php echo JText::_('Width'); ?>:</th>
								<td><span id="img-width"><?php echo $width; ?></span> px</td>
								<td></td>
							</tr>
							<tr>
								<th><?php echo JText::_('Height'); ?>:</th>
								<td><span id="img-height"><?php echo $height; ?></span> px</td>
								<td></td>
							</tr>
						</tbody>
					</table>

					<script type="text/javascript" src="<?php echo $base; ?>/media/system/js/jquery.fileuploader.js"></script>
					<script type="text/javascript">
					String.prototype.nohtml = function () {
						if (this.indexOf('?') == -1) {
							return this + '?no_html=1';
						} else {
							return this + '&no_html=1';
						}
					};
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
									if (response.success) {
										$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
										$('#img-name').text(response.file);
										$('#img-size').text(response.size);
										$('#img-width').text(response.width);
										$('#img-height').text(response.height);

										$('#img-delete').show();
									}
								}
							});
						}
						$('#img-delete').on('click', function (e) {
							e.preventDefault();
							var el = $(this);
							$.getJSON(el.attr('href').nohtml(), {}, function(response) {
								if (response.success) {
									$('#img-display').attr('src', '../administrator/components/com_courses/assets/img/blank.png');
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
			<?php
			} else {
				echo '<p class="warning">'.JText::_('COM_COURSES_PICTURE_ADDED_LATER').'</p>';
			}
			?>
		</fieldset>

		<?php $params = new JRegistry($this->row->get('params')); ?>

		<fieldset class="adminform offeringparams">
			<legend><?php echo JText::_('Parameters'); ?></legend>
			<div class="input-wrap">
				<label for="params-progress-calculation"><?php echo JText::_('COM_COURSES_PROGRESS_CALCULATION'); ?>:</label><br />
				<select name="params[progress_calculation]" id="params-progress-calculation">
					<option value=""<?php echo ($params->get('progress_calculation', '') == '') ? 'selected="selected"' : '' ?>>Inherit from courses defaults</option>
					<option value="all"<?php echo ($params->get('progress_calculation', '') == 'all') ? 'selected="selected"' : '' ?>>All published assets</option>
					<option value="graded"<?php echo ($params->get('progress_calculation', '') == 'graded') ? 'selected="selected"' : '' ?>>All published, graded assets</option>
					<option value="videos"<?php echo ($params->get('progress_calculation', '') == 'videos') ? 'selected="selected"' : '' ?>>All published, video assets</option>
					<option value="manual"<?php echo ($params->get('progress_calculation', '') == 'manual') ? 'selected="selected"' : '' ?>>All published, manually selected assets</option>
				</select>
			</div>
		</fieldset>

		<?php
			JPluginHelper::importPlugin('courses');
			$dispatcher = JDispatcher::getInstance();

			if ($plugins = $dispatcher->trigger('onOfferingEdit'))
			{
				$data = $this->row->get('params');

				foreach ($plugins as $plugin)
				{
					$param = new JParameter(
						(is_object($data) ? $data->toString() : $data),
						JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
					);
					$out = $param->render('params', 'onOfferingEdit');
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
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
