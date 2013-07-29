<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelper::getActions('page');

$text = ($this->task == 'editpage' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('Wiki') . ': ' . JText::_('Page').': <small><small>[ ' . $text . ' ]</small></small>', 'wiki.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm(<?php echo JText::_('RESET_HITS_WARNING'); ?>)){
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
	if ($('pagetitle').value == '') {
		alert(<?php echo JText::_('ERROR_MISSING_TITLE'); ?>);
	} else if ($('pagename').value == '') {
		alert(<?php echo JText::_('ERROR_MISSING_PAGENAME'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('Title'); ?>:</label></td>
						<td><input type="text" name="page[title]" id="pagetitle" size="30" maxlength="255" value="<?php echo htmlentities(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Pagename'); ?>:</label></td>
						<td><input type="text" name="page[pagename]" id="pagename" size="30" maxlength="255" value="<?php echo stripslashes($this->row->pagename); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Scope'); ?>:</label></td>
						<td><input type="text" name="page[scope]" id="pagescope" size="30" maxlength="255" value="<?php echo stripslashes($this->row->scope); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Group'); ?>:</label></td>
						<td><input type="text" name="page[group_cn]" id="pagegroup" size="30" maxlength="255" value="<?php echo stripslashes($this->row->group_cn); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata for this entry'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->id); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Created by'); ?></th>
					<td><?php echo $this->escape(stripslashes($this->creator->get('name'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Hits'); ?></th>
					<td><?php echo $this->escape($this->row->hits); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Revisions'); ?></th>
					<td><?php echo $this->row->getRevisionCount(); ?></td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('PARAMETERS'); ?></span></legend>
			
			<?php 
			//$paramsClass = 'JRegistry';
			//if (version_compare(JVERSION, '1.6', 'lt'))
			//{
				$paramsClass = 'JParameter';
			//}
			$params = new $paramsClass($this->row->params, JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'wiki.xml');
			echo $params->render();
			?>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="authors"><?php echo JText::_('Authors'); ?>:</label></td>
						<td><input type="text" name="page[authors]" id="pageauthors" value="<?php echo htmlentities($this->row->authors); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="state"><?php echo JText::_('State'); ?>:</label></td>
						<td>
							<select name="page[state]" id="pagestate">
								<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Public'); ?></option>
								<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Locked'); ?></option>
								<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Trashed'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key" style="vertical-align: top;"><?php echo JText::_('Access Level'); ?>:</td>
						<td><?php echo JHTML::_('list.accesslevel', $this->row); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="page[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>