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

$canDo = ResourcesHelper::getActions('resource');

$text = ($this->task == 'edit' ? JText::_('Edit') . ' #' . $this->row->id : JText::_('New'));

JToolBarHelper::title(JText::_('Resource') . ': <small><small>[ ' . $text . ' ]</small></small>', 'resources.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::spacer();
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

if ($this->row->standalone == 1) {
	$database =& JFactory::getDBO();

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
jimport('joomla.html.pane');
$tabs =& JPane::getInstance('sliders');
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'resethits') {
		if (confirm('Are you sure you want to reset the Hits to Zero? \nAny unsaved changes to this content will be lost.')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'resetrating') {
		if (confirm('Are you sure you want to reset the Rating to Unrated? \nAny unsaved changes to this content will be lost.')){
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
		alert('Content item must have a title');
	} else if (form.type.value == "-1"){
		alert('You must select a Section.');
	} else {
		submitform(pressbutton);
	}
}

function doFileoptions()
{
	var fwindow = window.filer.window.imgManager;

	if(fwindow) {
		if(fwindow.document) {
			var fform = fwindow.document.forms['filelist'];

			if(fform) {
				//var filepath = fform.elements['listdir'];
				var slctdfiles = fform.slctdfile;
				if(slctdfiles.length > 1) {
					for(var i = 0; i < slctdfiles.length; i++) 
					{
						if(slctdfiles[i].checked) {
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

				if(act == '1') {
					document.forms['adminForm'].elements['params[series_banner]'].value = '<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath;
				} else if(act == '2') {
					//if(filepath) {
					//document.forms['adminForm'].elements['path'].value = '<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath;
					document.forms['adminForm'].elements['path'].value = filepath;
					//}
				} else if(act == '3') {
					text = '<img class="contentimg" src="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '" alt="image" />';
					document.forms['adminForm'].elements['fulltxt'].focus();
					document.forms['adminForm'].elements['fulltxt'].value  += text;
					document.forms['adminForm'].elements['fulltxt'].focus();
				} else if(act == '4') {
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

<form action="index.php" method="post" name="adminForm" id="resourceForm" class="editform">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Details'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<th class="key"><label for="title">Title:</label></th>
					<td colspan="3"><input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
				</tr>
				<tr>
					<th class="key"><label>Type:</label></th>
					<td><?php echo $this->lists['type']; ?></td>
<?php if ($this->row->standalone == 1) { ?>
					<th class="key"><label for="alias">Alias:</label></th>
					<td><input type="text" name="alias" id="alias" size="25" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /></td>
				</tr>
				<tr>
					<th class="key"><label for="attrib[location]">Location:</label></th>
					<td><input type="text" name="attrib[location]" id="attrib[location]" size="25" maxlength="250" value="<?php echo $this->attribs->get('location', ''); ?>" /></td>
					<th class="key"><label for="attrib[timeof]">Time:</label></th>
					<td><input type="text" name="attrib[timeof]" id="attrib[timeof]" size="25" maxlength="250" value="<?php echo $this->attribs->get('timeof', ''); ?>" /></td>
				</tr>
<?php } else { ?>
					<th class="key"><label>Logical Type:</label></th>
					<td><?php echo $this->lists['logical_type']; ?><input type="hidden" name="alias" value="" /></td>
				</tr>
				<tr>
					<th class="key"><label for="path">File/URL:</label></th>
					<td colspan="3"><input type="text" name="path" id="path" size="60" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->path)); ?>" /></td>
				</tr>
				<!-- <tr>
					<th class="key"><label for="attrib[exclude]">Exclude from menu:</label></th>
					<td><input type="checkbox" name="attrib[exclude]" id="attrib[exclude]" value="1"<?php if($this->attribs->get('exclude', '') == 1) { echo ' checked="checked"'; } ?> /></td>
				</tr> -->
				<tr>
					<th class="key"><label for="attrib[duration]">Duration:</label></th>
					<td colspan="3"><input type="text" name="attrib[duration]" id="attrib[duration]" size="60" maxlength="100" value="<?php echo $this->attribs->get('duration', ''); ?>" /></td>
				</tr>
				<tr>
					<th class="key"><label for="attrib[width]">Width:</label></th>
					<td><input type="text" name="attrib[width]" id="attrib[width]" size="5" maxlength="250" value="<?php echo $this->attribs->get('width', ''); ?>" /></td>
					<th class="key"><label for="attrib[height]">Height:</label></th>
					<td><input type="text" name="attrib[height]" id="attrib[height]" size="5" maxlength="250" value="<?php echo $this->attribs->get('height', ''); ?>" /></td>
				</tr>
				<tr>
					<th class="key"><label for="attrib[attributes]">Attributes:</label></th>
					<td colspan="3">
						<input type="text" name="attrib[attributes]" id="attrib[attributes]" size="60" maxlength="100" value="<?php echo $this->attribs->get('attributes', ''); ?>" /><br />
						<span class="hint">code:silicon, class:one two three, one:more</span>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td colspan="4">
						<label>Intro Text:</label><br />
						<?php
						$editor =& JFactory::getEditor();
						echo $editor->display('introtext', $this->escape(stripslashes($this->row->introtext)), '100%', '100px', '45', '10', false);
						?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<label>Main Text: (optional)</label><br />
						<?php
						echo $editor->display('fulltxt', $this->escape(stripslashes($this->row->fulltxt)), '100%', '300px', '45', '10', false);
						?>
					</td>
				</tr>
			</tbody>
		</table>
		</fieldset>
<?php if ($this->row->standalone == 1) { ?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Custom fields'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td id="resource-custom-fields">
<?php
			$elements = new ResourcesElements($data, $type->customFields);
			echo $elements->render();
?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
<?php } ?>
	</div>
	<div class="col width-40 fltrt">
<?php if ($this->row->id) { ?>
		<table class="meta" summary="<?php echo JText::_('Metadata for this entry'); ?>">
			<tr>
				<th><?php echo JText::_('ID:'); ?></th>
				<td><?php echo $this->row->id; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('Created:'); ?></th>
				<td>
					<?php echo $this->row->created; ?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('Created By:'); ?></th>
				<td>
					<?php echo $this->escape($this->row->created_by_name); ?>
					<input type="hidden" name="created_by_id" value="<?php echo $this->row->created_by; ?>" />
				</td>
			</tr>
<?php if ($this->row->modified != '0000-00-00 00:00:00') { ?>
			<tr>
				<th><?php echo JText::_('Modified:'); ?></th>
				<td>
					<?php echo $this->row->modified; ?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('Modified By:'); ?></th>
				<td>
					<?php echo $this->escape($this->row->modified_by_name); ?>
					<input type="hidden" name="modified_by_id" value="<?php echo $this->row->modified_by; ?>" />
				</td>
			</tr>
<?php } ?>
<?php if ($this->row->standalone == 1) { ?>
			<tr>
				<th>Ranking:</th>
				<td>
					<?php echo $this->row->ranking; ?>/10
					<?php if ($this->row->ranking != '0') { ?>
						<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" /> 
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>Rating:</th>
				<td>
					<?php echo $this->row->rating.'/5.0 ('.$this->row->times_rated.' reviews)'; ?>
					<?php if ($this->row->rating != '0.0') { ?>
						<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" /> 
						<a onclick="popratings();" href="#">View ratings</a>
					<?php } ?>
				</td>
			</tr>
<?php } ?>
		</table>
<?php } ?>

<?php if ($this->row->standalone == 1) { ?>
		<fieldset class="adminform">
			<legend><span>Contributors</span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td id="resource-authors">
							<?php echo $this->lists['authors']; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
<?php }

		echo $tabs->startPane("content-pane");
		echo $tabs->startPanel('Publishing','publish-page');
?>
		<table class="paramlist admintable">
			<tbody>
				<tr>
					<td class="paramlist_key"><label>Standalone:</label></th>
					<td><input type="checkbox" name="standalone" value="1" <?php echo ($this->row->standalone ==1) ? 'checked="checked"' : ''; ?> /> appears in searches, lists</td>
				</tr>
				<tr>
					<td class="paramlist_key"><label>Status:</label></th>
					<td>
						<select name="published">
							<option value="2"<?php echo ($this->row->published == 2) ? ' selected="selected"' : ''; ?>>Draft (user created)</option>
							<option value="5"<?php echo ($this->row->published == 5) ? ' selected="selected"' : ''; ?>>Draft (internal)</option>
							<option value="3"<?php echo ($this->row->published == 3) ? ' selected="selected"' : ''; ?>>Pending</option>
							<option value="0"<?php echo ($this->row->published == 0) ? ' selected="selected"' : ''; ?>>Unpublished</option>
							<option value="1"<?php echo ($this->row->published == 1) ? ' selected="selected"' : ''; ?>>Published</option>
							<option value="4"<?php echo ($this->row->published == 4) ? ' selected="selected"' : ''; ?>>Delete</option>
						</select>
					</td>
				</tr>
<?php if ($this->row->standalone == 1) { ?>
				<tr>
					<td class="paramlist_key"><label>Group:</label></th>
					<td><?php echo $this->lists['groups']; ?></td>
				</tr>
<?php } ?>
				<tr>
					<td class="paramlist_key"><label>Access Level:</label></th>
					<td><?php echo $this->lists['access']; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key"><label>Change Creator:</label></th>
					<td><?php echo $this->lists['created_by']; ?></td>
				</tr>
<?php if ($this->row->standalone == 1) { ?>
				<tr>
					<td class="paramlist_key"><label for="publish_up">Start Publishing:</label></th>
					<td>
						<?php echo JHTML::_('calendar', $this->row->publish_up, 'publish_up', 'publish_up', "%Y-%m-%d", array('class' => 'inputbox')); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><label for="publish_down">Finish Publishing:</label></th>
					<td>
						<?php echo JHTML::_('calendar', $this->row->publish_down, 'publish_down', 'publish_down', "%Y-%m-%d", array('class' => 'inputbox')); ?>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td class="paramlist_key"><strong>Hits:</strong></td>
					<td>
						<?php echo $this->row->hits; ?>
						<?php if ($this->row->hits) { ?>
							<input type="button" name="reset_hits" id="reset_hits" value="Reset Hit Count" onclick="submitbutton('resethits');" />
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel('Files','file-page');
?>
		<p>
			<label>
				<?php echo JText::_('With selected'); ?>:
				<select name="fileoptions" id="fileoptions">
					<option value="2">Set as main file</option>
					<option value="3">Insert HTML: image</option>
					<option value="4">Insert HTML: linked file</option>
				</select>
			</label>
			<input type="button" value="<?php echo JText::_('Apply'); ?>" onclick="doFileoptions();" />
		</p>
		<iframe width="100%" height="400" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;listdir=<?php echo $path . DS . $dir_id; ?>"></iframe>
		<input type="hidden" name="tmpid" value="<?php echo $dir_id; ?>" />
<?php
		echo $tabs->endPanel();

		if ($this->row->standalone == 1) {
			echo $tabs->startPanel('Tags','tags-page');
			?>
			<textarea name="tags" id="tags" cols="35" rows="6"><?php echo $this->lists['tags']; ?></textarea>
			<?php
			echo $tabs->endPanel();

			echo $tabs->startPanel('Parameters','params-page');
			echo '<fieldset class="paramlist">' . $this->params->render() . '</fieldset>';
			echo $tabs->endPanel();
		} else {
			echo $tabs->startPanel('Parameters','params-page');
			?>
			<table width="100%" class="paramlist admintable" cellspacing="1">
				<tr>
					<td width="40%" class="paramlist_key">
						<span class="editlinktip">
							<label id="paramslink_action-lbl" for="paramslink_action" class="hasTip" title="Link action::Set link action of primary file">Link action</label>
						</span>
					</td>
					<td class="paramlist_value">
						<select name="params[link_action]" id="link_action">
							<option value="0"<?php if (!$this->params->get('link_action')) { echo ' selected="selected"'; } ?>>Default action</option>
							<option value="1"<?php if ($this->params->get('link_action') == 1) { echo ' selected="selected"'; } ?>>New window</option>
							<option value="2"<?php if ($this->params->get('link_action') == 2) { echo ' selected="selected"'; } ?>>Lightbox</option>
							<option value="3"<?php if ($this->params->get('link_action') == 3) { echo ' selected="selected"'; } ?>>Download</option>
						</select>
					</td>
				</tr>
			</table>
			<?php
			echo $tabs->endPanel();
		}

		echo $tabs->endPane();
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
