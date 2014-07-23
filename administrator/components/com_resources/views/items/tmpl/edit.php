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

$canDo = ResourcesHelperPermissions::getActions('resource');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') . ' #' . $this->row->id : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . $text, 'resources.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::spacer();
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

if ($this->row->standalone == 1)
{
	$database = JFactory::getDBO();

	$type = new ResourcesType($database);
	$type->load($this->row->type);

	$data = array();
	preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->fulltxt, $matches, PREG_SET_ORDER);
	if (count($matches) > 0)
	{
		foreach ($matches as $match)
		{
			$data[$match[1]] = stripslashes($match[2]);
		}
	}
	$this->row->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->row->fulltxt);
	$this->row->fulltxt = trim($this->row->fulltxt);
	$this->row->fulltxt = ($this->row->fulltxt) ? trim(stripslashes($this->row->fulltxt)): trim(stripslashes($this->row->introtext));

	include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
}

// Build the path for uploading files
$path = ResourcesHtml::dateToPath($this->row->created);
if ($this->row->id) {
	$dir_id = ResourcesHtml::niceidformat($this->row->id);
} else {
	$dir_id = time().rand(0,10000);
}

// Instantiate the sliders object
//jimport('joomla.html.pane');
//$tabs = JPane::getInstance('sliders');

$time = $this->attribs->get('timeof', '');
$time = strtotime($time) === false ? NULL : $time;
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'resethits') {
		if (confirm('<?php echo JText::_('COM_RESOURCES_CONFIRM_HITS_RESET'); ?>'){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'resetrating') {
		if (confirm('<?php echo JText::_('COM_RESOURCES_CONFIRM_RATINGS_RESET'); ?>'){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert('<?php echo JText::_('COM_RESOURCES_ERROR_MISSING_TITLE'); ?>');
	} else if (form.type.value == "-1"){
		alert('<?php echo JText::_('COM_RESOURCES_ERROR_MISSING_TYPE'); ?>');
	} else {
		<?php echo JFactory::getEditor()->save('text'); ?>

		submitform(pressbutton);
	}
}

function doFileoptions()
{
	var fwindow = window.filer.window.imgManager;

	if (fwindow) {
		if (fwindow.document) {
			var fform = fwindow.document.forms['filelist'];

			if (fform) {
				//var filepath = fform.elements['listdir'];
				var slctdfiles = fform.slctdfile;
				if (slctdfiles.length > 1) {
					for(var i = 0; i < slctdfiles.length; i++)
					{
						if (slctdfiles[i].checked) {
							var filepath = slctdfiles[i].value;
						}
					}
				} else {
					var filepath = slctdfiles.value;
				}

				box = document.adminForm.fileoptions;
				act = box.options[box.selectedIndex].value;

				//var selection = window.filer.document.forms[0].dirPath;
				//var dir = selection.options[selection.selectedIndex].value;

				if (act == '1') {
					document.forms['adminForm'].elements['params[series_banner]'].value = '<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath;
				} else if (act == '2') {
					//if (filepath) {
					//document.forms['adminForm'].elements['path'].value = '<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath;
					document.forms['adminForm'].elements['path'].value = filepath;
					//}
				} else if (act == '3') {
					text = '<img class="contentimg" src="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '" alt="image" />';
					document.forms['adminForm'].elements['fulltxt'].focus();
					document.forms['adminForm'].elements['fulltxt'].value  += text;
					document.forms['adminForm'].elements['fulltxt'].focus();
				} else if (act == '4') {
					text = '<a href="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '">' + filepath + '</a>';
					document.forms['adminForm'].elements['fulltxt'].focus();
					document.forms['adminForm'].elements['fulltxt'].value  += text;
					document.forms['adminForm'].elements['fulltxt'].focus();
				}
			}
		}
	}
}
function popratings()
{
	window.open('index.php?option=<?php echo $this->option; ?>&task=ratings&id=<?php echo $this->row->id; ?>&no_html=1', 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
	return false;
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form" class="editform">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('COM_RESOURCES_FIELD_TYPE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo $this->lists['type']; ?>
			</div>

		<?php if ($this->row->standalone == 1) { ?>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="alias" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="attrib-location"><?php echo JText::_('COM_RESOURCES_FIELD_LOCATION'); ?>:</label><br />
					<input type="text" name="attrib[location]" id="attrib-location" maxlength="250" value="<?php echo $this->attribs->get('location', ''); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="attrib-timeof"><?php echo JText::_('COM_RESOURCES_FIELD_TIME'); ?>:</label><br />
					<input type="text" name="attrib[timeof]" id="attrib-timeof" maxlength="250" value="<?php echo $time ? JHTML::_('date', $time, 'Y-m-d H:i:s') : ''; ?>" placeholder="<?php echo JHTML::_('date', time(), 'Y-m-d H:i:s'); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?>">
				<label for="attrib-canonical"><?php echo JText::_('COM_RESOURCES_FIELD_CANONICAL'); ?>:</label><br />
				<input type="text" name="attrib[canonical]" id="attrib-canonical" maxlength="250" value="<?php echo $this->attribs->get('canonical', ''); ?>" />
				<span class="hint"><?php echo JText::_('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?></span>
			</div>
		<?php } else { ?>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_RESOURCES_FIELD_LOGICAL_TYPE'); ?>:</label><br />
				<?php echo $this->lists['logical_type']; ?>
				<input type="hidden" name="alias" value="" />
			</div>

			<div class="input-wrap">
				<label for="field-path"><?php echo JText::_('COM_RESOURCES_FIELD_PATH'); ?>:</label><br />
				<input type="text" name="path" id="field-path" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->path)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="attrib[duration]"><?php echo JText::_('COM_RESOURCES_FIELD_DURATION'); ?>:</label><br />
				<input type="text" name="attrib[duration]" id="attrib[duration]" maxlength="100" value="<?php echo $this->attribs->get('duration', ''); ?>" />
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="attrib[width]"><?php echo JText::_('COM_RESOURCES_FIELD_WIDTH'); ?>:</label><br />
					<input type="text" name="attrib[width]" id="attrib[width]" maxlength="250" value="<?php echo $this->attribs->get('width', ''); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="attrib[height]"><?php echo JText::_('COM_RESOURCES_FIELD_HEIGHT'); ?>:</label><br />
					<input type="text" name="attrib[height]" id="attrib[height]" maxlength="250" value="<?php echo $this->attribs->get('height', ''); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_RESOURCES_FIELD_ATTRIBUTES_HINT'); ?>">
				<label for="attrib[attributes]"><?php echo JText::_('COM_RESOURCES_FIELD_ATTRIBUTES'); ?>:</label><br />
				<input type="text" name="attrib[attributes]" id="attrib[attributes]" maxlength="100" value="<?php echo $this->attribs->get('attributes', ''); ?>" /><br />
				<span class="hint"><?php echo JText::_('COM_RESOURCES_FIELD_ATTRIBUTES_HINT'); ?></span>
			</div>
		<?php } ?>
			<div class="input-wrap">
				<label for="field-introtext"><?php echo JText::_('COM_RESOURCES_FIELD_INTRO_TEXT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<?php
				$editor = JFactory::getEditor();
				echo $editor->display('introtext', $this->escape(stripslashes($this->row->introtext)), '', '', '45', '5', false, 'field-introtext');
				?>
			</div>
			<div class="input-wrap">
				<label for="field-fulltxt"><?php echo JText::_('COM_RESOURCES_FIELD_MAIN_TEXT'); ?>:</label><br />
				<?php
				echo $editor->display('fulltxt', $this->escape(stripslashes($this->row->fulltxt)), '', '', '45', '15', false, 'field-fulltxt');
				?>
			</div>
		</fieldset>

	<?php if ($this->row->standalone == 1) { ?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Custom fields'); ?></span></legend>

			<div class="input-wrap" id="resource-custom-fields">
				<?php
				$elements = new ResourcesElements($data, $type->customFields);
				echo $elements->render();
				?>
			</div>
		</fieldset>
	<?php } ?>
	</div>
	<div class="col width-40 fltrt">
	<?php if ($this->row->id) { ?>
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_CREATED'); ?></th>
					<td>
						<?php echo JHTML::_('date', $this->row->created, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_CREATOR'); ?></th>
					<td>
						<?php echo $this->escape($this->row->created_by_name); ?>
						<input type="hidden" name="created_by_id" value="<?php echo $this->row->created_by; ?>" />
					</td>
				</tr>
			<?php if ($this->row->modified != '0000-00-00 00:00:00') { ?>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_MODIFIED'); ?></th>
					<td>
						<?php echo JHTML::_('date', $this->row->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_MODIFIER'); ?></th>
					<td>
						<?php echo $this->escape($this->row->modified_by_name); ?>
						<input type="hidden" name="modified_by_id" value="<?php echo $this->row->modified_by; ?>" />
					</td>
				</tr>
			<?php } ?>
			<?php if ($this->row->standalone == 1) { ?>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_RANKING'); ?></th>
					<td>
						<?php echo $this->row->ranking; ?>/10
						<?php if ($this->row->ranking != '0') { ?>
							<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" />
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_RATING'); ?></th>
					<td>
						<?php echo $this->row->rating . '/5.0 (' . $this->row->times_rated . ')'; ?>
						<?php if ($this->row->rating != '0.0') { ?>
							<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" />
							<a onclick="popratings();" href="#"><?php echo JText::_('COM_RESOURCES_VIEW'); ?></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_HITS'); ?></th>
					<td>
						<?php echo $this->row->hits; ?>
						<?php if ($this->row->hits) { ?>
							<input type="button" name="reset_hits" id="field-reset_hits" value="Reset Hit Count" onclick="submitbutton('resethits');" />
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php } ?>

	<?php if ($this->row->standalone == 1) { ?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_RESOURCES_FIELDSET_CONTRIBUTORS'); ?></span></legend>

			<div class="input-wrap" id="resource-authors">
				<?php echo $this->lists['authors']; ?>
			</div>
		</fieldset>
	<?php } ?>

	<?php
		echo JHtml::_('sliders.start', 'content-pane');
		echo JHtml::_('sliders.panel', JText::_('COM_RESOURCES_FIELDSET_PUBLISHING'), 'publish-page');
	?>
		<div class="paramlist">
			<div class="input-wrap">
				<input type="checkbox" name="standalone" id="field-standalone" value="1" <?php echo ($this->row->standalone ==1) ? 'checked="checked"' : ''; ?> />
				<label for="field-standalone"><?php echo JText::_('COM_RESOURCES_FIELD_STANDALONE'); ?></label>
			</div>
			<div class="input-wrap">
				<label for="field-published"><?php echo JText::_('COM_RESOURCES_FIELD_STATUS'); ?>:</label><br />
				<select name="published" id="field-published">
					<option value="2"<?php echo ($this->row->published == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
					<option value="5"<?php echo ($this->row->published == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
					<option value="3"<?php echo ($this->row->published == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_PENDING'); ?></option>
					<option value="0"<?php echo ($this->row->published == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->row->published == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="4"<?php echo ($this->row->published == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>
			<?php if ($this->row->standalone == 1) { ?>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_RESOURCES_FIELD_GROUP'); ?>:</label><br />
					<?php echo $this->lists['groups']; ?>
				</div>
			<?php } ?>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_RESOURCES_FIELD_ACCESS'); ?>:</label><br />
					<?php echo $this->lists['access']; ?>
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_RESOURCES_FIELD_CREATOR'); ?>:</label><br />
					<?php echo $this->lists['created_by']; ?>
				</div>
			<?php // if ($this->row->standalone == 1) { ?>
				<div class="input-wrap">
					<label for="publish_up"><?php echo JText::_('COM_RESOURCES_FIELD_PUBLISH_UP'); ?>:</label><br />
					<?php $up = JHTML::_('date', $this->row->publish_up, 'Y-m-d H:i:s'); ?>
					<?php echo JHTML::_('calendar', $up, 'publish_up', 'publish_up', "%Y-%m-%d", array('class' => 'inputbox')); ?>
				</div>
				<div class="input-wrap">
					<label for="publish_down"><?php echo JText::_('COM_RESOURCES_FIELD_PUBLISH_DOWN'); ?>:</label><br />
					<?php
						$down = 'Never';
						if (strtolower($this->row->publish_down) != 'never')
						{
							$down = JHTML::_('date', $this->row->publish_down, 'Y-m-d H:i:s');
						}
					?>
					<?php echo JHTML::_('calendar', $down, 'publish_down', 'publish_down', "%Y-%m-%d", array('class' => 'inputbox')); ?>
				</div>
			<?php // } ?>
		</div>
	<?php
		echo JHtml::_('sliders.panel', JText::_('COM_RESOURCES_FIELDSET_FILES'), 'file-page');
	?>
		<p>
			<label>
				<?php echo JText::_('COM_RESOURCES_FIELD_WITH_SELECTED'); ?>:
				<select name="fileoptions" id="fileoptions">
					<option value="2"><?php echo JText::_('COM_RESOURCES_FIELD_WITH_SELECTED_MAIN'); ?></option>
					<option value="3"><?php echo JText::_('COM_RESOURCES_FIELD_WITH_SELECTED_IMG'); ?></option>
					<option value="4"><?php echo JText::_('COM_RESOURCES_FIELD_WITH_SELECTED_LINKED'); ?></option>
				</select>
			</label>
			<input type="button" value="<?php echo JText::_('COM_RESOURCES_APPLY'); ?>" onclick="doFileoptions();" />
		</p>
		<iframe width="100%" height="400" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;listdir=<?php echo $path . DS . $dir_id; ?>"></iframe>
		<input type="hidden" name="tmpid" value="<?php echo $dir_id; ?>" />
	<?php
		if ($this->row->standalone == 1) {
			echo JHtml::_('sliders.panel', JText::_('COM_RESOURCES_FIELDSET_TAGS'), 'tags-page');
			?>
			<textarea name="tags" id="tags" cols="35" rows="6"><?php echo $this->lists['tags']; ?></textarea>
			<?php
			echo JHtml::_('sliders.panel', JText::_('COM_RESOURCES_FIELDSET_PARAMETERS'), 'params-page');
			echo '<fieldset class="paramlist">' . $this->params->render() . '</fieldset>';
		} else {
			echo JHtml::_('sliders.panel', JText::_('COM_RESOURCES_FIELDSET_PARAMETERS'), 'params-page');
			?>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_RESOURCES_FIELD_LINK_ACTION_HINT'); ?>">
				<label for="param-link_action"><?php echo JText::_('COM_RESOURCES_FIELD_LINK_ACTION'); ?></label><br />
				<select name="params[link_action]" id="param-link_action">
					<option value="0"<?php if (!$this->params->get('link_action')) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_RESOURCES_FIELD_LINKED_ACTION_DEFAULT'); ?></option>
					<option value="1"<?php if ($this->params->get('link_action') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_RESOURCES_FIELD_LINKED_ACTION_NEW_WINDOW'); ?></option>
					<option value="2"<?php if ($this->params->get('link_action') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_RESOURCES_FIELD_LINKED_ACTION_LIGHTBOX'); ?></option>
					<option value="3"<?php if ($this->params->get('link_action') == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_RESOURCES_FIELD_LINKED_ACTION_DOWNLOAD'); ?></option>
				</select>
				<span class="hint"><?php echo JText::_('COM_RESOURCES_FIELD_LINK_ACTION_HINT'); ?></span>
			</div>
			<?php
		}

		echo JHtml::_('sliders.end');
	?>

	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" id="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>
