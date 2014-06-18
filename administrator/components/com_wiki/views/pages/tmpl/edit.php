<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelper::getActions('page');

$text = ($this->task == 'editpage' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_WIKI') . ': ' . JText::_('COM_WIKI_PAGE') .': ' . $text, 'wiki.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('page');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm(<?php echo JText::_('COM_WIKI_WARNING_RESET_HITS'); ?>)){
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
		alert(<?php echo JText::_('COM_WIKI_ERROR_MISSING_TITLE'); ?>);
	} else if ($('pagename').value == '') {
		alert(<?php echo JText::_('COM_WIKI_ERROR_MISSING_PAGENAME'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="pagetitle"><?php echo JText::_('COM_WIKI_FIELD_TITLE'); ?>:</label><br />
				<input type="text" name="page[title]" id="pagetitle" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_WIKI_FIELD_PAGENAME_HINT'); ?>">
				<label for="pagename"><?php echo JText::_('COM_WIKI_FIELD_PAGENAME'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="page[pagename]" id="pagename" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('pagename'))); ?>" />
				<span class="hint"><?php echo JText::_('COM_WIKI_FIELD_PAGENAME_HINT'); ?></span>
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="pagescope"><?php echo JText::_('COM_WIKI_FIELD_SCOPE'); ?>:</label><br />
					<input type="text" name="page[scope]" id="pagescope" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('scope'))); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="pagegroup"><?php echo JText::_('COM_WIKI_FIELD_GROUP'); ?>:</label><br />
					<input type="text" name="page[group_cn]" id="pagegroup" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('group_cn'))); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_WIKI_FIELD_AUTHORS_HINT'); ?>">
				<label for="authors"><?php echo JText::_('COM_WIKI_FIELD_AUTHORS'); ?>:</label><br />
				<textarea name="page[authors]" id="pageauthors" col="35" rows="3"><?php echo $this->escape($this->row->authors('string')); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_WIKI_FIELD_AUTHORS_HINT'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_WIKI_FIELD_TAGS_HINT'); ?>">
				<label for="field-tags"><?php echo JText::_('COM_WIKI_FIELD_TAGS'); ?>:</label><br />
				<textarea name="page[tags]" id="field-tags" col="35" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_WIKI_FIELD_TAGS_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_CREATED'); ?></th>
					<td><?php echo $this->escape(stripslashes($this->row->creator('name'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_HITS'); ?></th>
					<td><?php echo $this->escape($this->row->get('hits')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_REVISIONS'); ?></th>
					<td><?php echo $this->row->revisions('count'); ?></td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WIKI_FIELDSET_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
			<?php
			$params = new JParameter($this->row->get('params'), JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'wiki.xml');
			echo $params->render();
			?>
			</div>

			<div class="input-wrap">
				<label for="state"><?php echo JText::_('COM_WIKI_FIELD_STATE'); ?>:</label><br />
				<select name="page[state]" id="pagestate">
					<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_OPEN'); ?></option>
					<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_LOCKED'); ?></option>
					<option value="2"<?php echo ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_TRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-access"><?php echo JText::_('COM_WIKI_FIELD_ACCESS'); ?>:</label><br />
				<select name="page[access]" id="field-access">
					<option value="0"<?php if ($this->row->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_WIKI_ACCESS_PUBLIC'); ?></option>
					<option value="1"<?php if ($this->row->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_WIKI_ACCESS_REGISTERED'); ?></option>
					<option value="4"<?php if ($this->row->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_WIKI_ACCESS_PRIVATE'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="page[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>