<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Resource' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::cancel();


if ($this->row->standalone == 1) {
	$database =& JFactory::getDBO();
	
	$type = new ResourcesType( $database );
	$type->load( $this->row->type );

	$fields = array();
	if (trim($type->customFields) != '') {
		$fs = explode("\n", trim($type->customFields));
		foreach ($fs as $f) 
		{
			$fields[] = explode('=', $f);
		}
	} else {
		if ($this->row->type == 7) {
			$flds = $this->rconfig->get('tagstool');
		} else {
			$flds = $this->rconfig->get('tagsothr');
		}
		$flds = explode(',',$flds);
		foreach ($flds as $fld) 
		{
			$fields[] = array($fld, $fld, 'textarea', 0);
		}
	}

	if (!empty($fields)) {
		for ($i=0, $n=count( $fields ); $i < $n; $i++) 
		{
			// Explore the text and pull out all matches
			array_push($fields[$i], ResourcesHtml::parseTag($this->row->fulltext, $fields[$i][0]));

			// Clean the original text of any matches
			$this->row->fulltext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$this->row->fulltext);
		}
		$this->row->fulltext = trim($this->row->fulltext);
	}
}

// Build the path for uploading files
$path = ResourcesHtml::dateToPath( $this->row->created );
if ($this->row->id) {
	$dir_id = ResourcesHtml::niceidformat( $this->row->id );
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
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'resetrating') {
		if (confirm('Are you sure you want to reset the Rating to Unrated? \nAny unsaved changes to this content will be lost.')){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert( 'Content item must have a title' );
	} else if (form.type.value == "-1"){
		alert( 'You must select a Section.' );
	} else {
		submitform( pressbutton );
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
					document.forms['adminForm'].elements['fulltext'].focus();
					document.forms['adminForm'].elements['fulltext'].value  += text;
					document.forms['adminForm'].elements['fulltext'].focus();
				} else if(act == '4') {
					text = '<a href="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '">' + filepath + '</a>';
					document.forms['adminForm'].elements['fulltext'].focus();
					document.forms['adminForm'].elements['fulltext'].value  += text;
					document.forms['adminForm'].elements['fulltext'].focus();
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
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<!-- <fieldset>
					<legend>Resource #<?php echo $this->row->id; ?></legend> -->
		<table class="adminform">
			<caption style="text-align: left; font-weight: bold;"><?php echo JText::sprintf('Resource #%s', $this->row->id); ?></caption>
			<tbody>
				<tr>
					<td class="key"><label for="title">Title:</label></td>
					<td colspan="3"><input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo htmlentities(stripslashes($this->row->title), ENT_COMPAT, 'UTF-8', ENT_QUOTES); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label>Type:</label></td>
					<td><?php echo $this->lists['type']; ?></td>
<?php if ($this->row->standalone == 1) { ?>
					<td class="key"><label for="alias">Alias:</label></td>
					<td><input type="text" name="alias" id="alias" size="25" maxlength="250" value="<?php echo stripslashes($this->row->alias); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="attrib[location]">Location:</label></td>
					<td><input type="text" name="attrib[location]" id="attrib[location]" size="25" maxlength="250" value="<?php echo $this->attribs->get( 'location', '' ); ?>" /></td>
					<td class="key"><label for="attrib[timeof]">Time:</label></td>
					<td><input type="text" name="attrib[timeof]" id="attrib[timeof]" size="25" maxlength="250" value="<?php echo $this->attribs->get( 'timeof', '' ); ?>" /></td>
				</tr>
<?php } else { ?>
					<td class="key"><label>Logical Type:</label></td>
					<td><?php echo $this->lists['logical_type']; ?><input type="hidden" name="alias" value="" /></td>
				</tr>
				<tr>
					<td class="key"><label for="path">File/URL:</label></td>
					<td colspan="3"><input type="text" name="path" id="path" size="60" maxlength="250" value="<?php echo $this->row->path; ?>" /></td>
				</tr>
				<!-- <tr>
					<td class="key"><label for="attrib[exclude]">Exclude from menu:</label></td>
					<td><input type="checkbox" name="attrib[exclude]" id="attrib[exclude]" value="1"<?php if($this->attribs->get( 'exclude', '' ) == 1) { echo ' checked="checked"'; } ?> /></td>
				</tr> -->
				<tr>
					<td class="key"><label for="attrib[duration]">Duration:</label></td>
					<td colspan="3"><input type="text" name="attrib[duration]" id="attrib[duration]" size="60" maxlength="100" value="<?php echo $this->attribs->get( 'duration', '' ); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="attrib[width]">Width:</label></td>
					<td><input type="text" name="attrib[width]" id="attrib[width]" size="5" maxlength="250" value="<?php echo $this->attribs->get( 'width', '' ); ?>" /></td>
					<td class="key"><label for="attrib[height]">Height:</label></td>
					<td><input type="text" name="attrib[height]" id="attrib[height]" size="5" maxlength="250" value="<?php echo $this->attribs->get( 'height', '' ); ?>" /></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		
		<table class="adminform">
			<tbody>
				<tr>
					<td>
						<label>Intro Text:</label><br />
						<?php
						$editor =& JFactory::getEditor();
						echo $editor->display('introtext', htmlentities(stripslashes($this->row->introtext), ENT_COMPAT, 'UTF-8'), '100%', '100px', '45', '10', false);
						?>
					</td>
				</tr>
				<tr>
					<td>
						<label>Main Text: (optional)</label><br />
						<?php
						echo $editor->display('fulltext', htmlentities(stripslashes($this->row->fulltext), ENT_COMPAT, 'UTF-8'), '100%', '300px', '45', '10', false);
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- </fieldset> -->
<?php if ($this->row->standalone == 1) { ?>
		<!-- <fieldset>
			<legend><?php echo JText::_('Custom fields'); ?></legend> -->
		<table class="adminform">
			<caption style="text-align: left; font-weight: bold;"><?php echo JText::_('Custom fields'); ?></caption>
			<tbody>
<?php
$i = 3; 

foreach ($fields as $field)
{ 
$i++;
/*
$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", end($field));
*/
$tagcontent = end($field);
?>
			<tr>
				<td>
					<label><?php echo stripslashes($field[1]); ?>: <?php echo ($field[3] == 1) ? '<span class="required">'.JText::_('REQUIRED').'</span>': ''; ?></label><br />
					<?php if ($field[2] == 'text') { ?>
						<input type="text" name="<?php echo 'nbtag['.$field[0].']'; ?>" cols="50" rows="6"><?php echo stripslashes($tagcontent); ?></textarea>
					<?php
					} else {
						echo $editor->display('nbtag['.$field[0].']', htmlentities(stripslashes($tagcontent), ENT_COMPAT, 'UTF-8'), '100%', '100px', '45', '10', false);
					} 
					?>
				</td>
			</tr>
			
<?php 
} 
?>
			</tbody>
		</table>
		<!-- </fieldset> -->
<?php } ?>
	</td>
	<td valign="top" width="320" style="padding: 7px 0 0 5px">

		<!-- <table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
			<tbody>
				<tr>
					<td><strong>Resource ID:</strong></td>
					<td><?php echo $this->row->id; ?></td>
				</tr>

			</tbody>
		</table> -->
<?php if ($this->row->standalone == 1) { ?>
		<fieldset>
			<legend>Contributors</legend>
			<?php echo $this->lists['authors']; ?>
		</fieldset>
<?php }

		echo $tabs->startPane("content-pane");
		echo $tabs->startPanel('Publishing','publish-page');
?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
			<tbody>
				<tr>
					<td class="paramlist_key"><label>Standalone:</label></td>
					<td><input type="checkbox" name="standalone" value="1" <?php echo ($this->row->standalone ==1) ? 'checked="checked"' : ''; ?> /> appears in searches, lists</td>
				</tr>
				<tr>
					<td class="paramlist_key"><label>Status:</label></td>
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
					<td class="paramlist_key"><label>Group:</label></td>
					<td><?php echo $this->lists['groups']; ?></td>
				</tr>
<?php } ?>
				<tr>
					<td class="paramlist_key"><label>Access Level:</label></td>
					<td><?php echo $this->lists['access']; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key"><label>Change Creator:</label></td>
					<td><?php echo $this->lists['created_by']; ?></td>
				</tr>
				<!-- <tr>
					<td class="paramlist_key"><label for="created">Created Date:</label></td>
					<td>
						<input type="text" name="created" id="created" size="19" maxlength="19" value="<?php echo $this->row->created; ?>" />
						<input type="reset" name="reset" id="reset" onclick="return showCalendar('created', 'y-mm-dd');" value="..." />
					</td>
				</tr> -->
<?php if ($this->row->standalone == 1) { ?>
				<tr>
					<td class="paramlist_key"><label for="publish_up">Start Publishing:</label></td>
					<td>
						<?php echo JHTML::_('calendar', $this->row->publish_up, 'publish_up', 'publish_up', "%Y-%m-%d", array('class' => 'inputbox')); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><label for="publish_down">Finish Publishing:</label></td>
					<td>
						<?php echo JHTML::_('calendar', $this->row->publish_down, 'publish_down', 'publish_down', "%Y-%m-%d", array('class' => 'inputbox')); ?>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td class="paramlist_key"><strong>Hits:</strong></td>
					<td>
						<?php echo $this->row->hits; ?>
						<?php if ( $this->row->hits ) { ?>
							<input type="button" name="reset_hits" id="reset_hits" value="Reset Hit Count" onclick="submitbutton('resethits');" />
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><strong>Created:</strong></td>
					<td><input type="hidden" name="created_by_id" value="<?php echo $this->row->created_by; ?>" /><?php echo ($this->row->created != '0000-00-00 00:00:00') ? $this->row->created.'</td></tr><tr><td class="paramlist_key"><strong>Created By:</strong></td><td>'.$this->row->created_by_name : 'New resource'; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key"><strong>Modified:</strong></td>
					<td><input type="hidden" name="modified_by_id" value="<?php echo $this->row->modified_by; ?>" /><?php echo ($this->row->modified != '0000-00-00 00:00:00') ? $this->row->modified.'</td></tr><tr><td class="paramlist_key"><strong>Modified By:</strong></td><td>'.$this->row->modified_by_name : 'Not modified';?></td>
				</tr>
<?php if ($this->row->standalone == 1) { ?>
				<tr>
					<td class="paramlist_key"><strong>Ranking:</strong></td>
					<td>
						<?php echo $this->row->ranking; ?>/10
						<?php if ($this->row->ranking != '0') { ?>
							<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" /> 
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><strong>Rating:</strong></td>
					<td>
						<?php echo $this->row->rating.'/5.0 ('.$this->row->times_rated.' reviews)'; ?>
						<?php if ( $this->row->rating != '0.0' ) { ?>
							<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" /> 
							<a onclick="popratings();" href="#">View ratings</a>
						<?php } ?>
					</td>
				</tr>
<?php } ?>
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
		<iframe width="100%" height="400" name="filer" id="filer" src="index3.php?option=com_resources&amp;task=media&amp;listdir=<?php echo $path.DS.$dir_id; ?>"></iframe>
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
			echo $this->params->render();
			echo $tabs->endPanel();
		}
		
		echo $tabs->endPane();
?>

			</td>
		</tr>
	</table>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter[sort]" value="<?php echo $this->return['sort']; ?>" />
	<input type="hidden" name="filter[status]" value="<?php echo $this->return['status']; ?>" />
	<input type="hidden" name="filter[type]" value="<?php echo $this->return['type']; ?>" />
	
	<div class="clr"></div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
