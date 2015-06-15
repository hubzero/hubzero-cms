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
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Kb\Admin\Helpers\Permissions::getActions('article');

$text = ($this->task == 'edit' ? Lang::txt('COM_KB_EDIT') : Lang::txt('COM_KB_NEW'));

Toolbar::title(Lang::txt('COM_KB') . ': ' . Lang::txt('COM_KB_ARTICLE') . ': ' . $text, 'kb.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('article');

$selected = null;
?>
<script type="text/javascript">
var categories = new Array;
<?php
	$i = 0;
	foreach ($this->sections as $section)
	{
		echo 'categories[' . $i++ . "] = new Array( '" . $section->get('id') . "','0','" . addslashes(Lang::txt('COM_KB_NONE')) . "' );\n\t\t";
		foreach ($section->children('list', array('state' => -1, 'access' => -1, 'empty' => true)) as $v)
		{
			echo 'categories[' . $i++ . "] = new Array( '" . $section->get('id') . "','" . addslashes($v->get('id')) . "','" . addslashes($v->get('title')) . "' );\n\t\t";
		}
	}
?>

function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm('<?php echo Lang::txt('COM_KB_RESET_HITS_WARNING'); ?>')) {
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
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_KB_ERROR_MISSING_TITLE'); ?>');
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_KB_DETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-section"><?php echo Lang::txt('COM_KB_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[section]" id="field-section" onchange="changeDynaList('fieldcategory', categories, document.getElementById('field-section').options[document.getElementById('field-section').selectedIndex].value, 0, 0);if (jQuery.uniform) { $.uniform.update('#fieldcategory'); }">
					<?php foreach ($this->sections as $section) { ?>
						<?php
						if ($this->row->get('section') == $section->get('id'))
						{
							$selected = $section;
						}
						?>
						<option value="<?php echo $section->get('id'); ?>"<?php echo ($this->row->get('section') == $section->get('id')) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="fieldcategory"><?php echo Lang::txt('COM_KB_SUB_CATEGORY'); ?>:</label><br />
					<select name="fields[category]" id="fieldcategory">
						<option value="0"<?php echo ($this->row->get('category') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_KB_NONE'); ?></option>
				<?php if ($selected) { ?>
					<?php foreach ($selected->children() as $category) { ?>
						<option value="<?php echo $category->get('id'); ?>"<?php echo ($this->row->get('category') == $category->get('id')) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($category->get('title'))); ?></option>
					<?php } ?>
				<?php } ?>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_KB_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="100" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo Lang::txt('COM_KB_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-fulltxt"><?php echo Lang::txt('COM_KB_BODY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo $this->editor('fields[fulltxt]', $this->escape(stripslashes($this->row->get('fulltxt'))), 60, 30, 'field-fulltxt'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-tags"><?php echo Lang::txt('COM_KB_TAGS'); ?>:</label><br />
				<textarea name="tags" id="field-tags" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_KB_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id', 0); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_KB_CREATED'); ?>:</th>
					<td><?php echo $this->row->get('created'); ?></td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_KB_CREATOR'); ?>:</th>
					<td><?php echo $this->escape($this->row->creator('name')); ?></td>
				</tr>
		<?php
		if ($this->row->exists() && $this->row->get('modified') != '0000-00-00 00:00:00') {
			$modifier = User::getInstance($this->row->get('modified_by'));
		?>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>:</th>
					<td><?php echo $this->row->get('modified'); ?></td>
				</tr>
			<?php if (is_object($modifier)) {?>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_KB_MODIFIER'); ?>:</th>
					<td><?php echo $this->escape($modifier->get('name')); ?></td>
				</tr>
			<?php } ?>
		<?php } ?>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_KB_HITS'); ?>:</td>
					<td>
						<?php echo $this->row->get('hits', 0); ?>
						<?php if ($this->row->get('hits', 0)) { ?>
						<input type="button" name="reset_hits" id="reset_hits" value="<?php echo Lang::txt('COM_KB_RESET_HITS'); ?>" onclick="submitbutton('resethits');" />
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_KB_VOTES'); ?>:</td>
					<td>
						+<?php echo $this->row->get('helpful', 0); ?> -<?php echo $this->row->get('nothelpful', 0); ?>
						<?php if ($this->row->get('helpful', 0) > 0 || $this->row->get('nothelpful', 0) > 0) { ?>
						<input type="button" name="reset_votes" value="<?php echo Lang::txt('COM_KB_RESET_VOTES'); ?>" onclick="submitbutton('resetvotes');" />
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('State'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_KB_PUBLISH'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-access"><?php echo Lang::txt('COM_KB_ACCESS_LEVEL'); ?>:</label>
				<select name="fields[access]" id="field-access">
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
				</select>
			</div>
		</fieldset>

		<fieldset class="adminform paramlist">
			<legend><span><?php echo Lang::txt('COM_KB_PARAMETERS'); ?></span></legend>

			<?php echo $this->params->render(); ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
