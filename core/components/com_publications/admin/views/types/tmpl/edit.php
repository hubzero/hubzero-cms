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

$this->css()
     ->js('curation.js');

$canDo = \Components\Publications\Helpers\Permissions::getActions('type');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();

// Determine whether master type is supported in current version of hub code
$aClass  = 'item_off';
$active  = Lang::txt('COM_PUBLICATIONS_MTYPE_OFF');

// If we got a plugin - type is supported
if (Plugin::isEnabled('projects', $this->row->alias))
{
	$aClass  = 'item_on';
	$active  = Lang::txt('COM_PUBLICATIONS_MTYPE_ON');
}

$params = new \Hubzero\Config\Registry($this->row->params);

// Available panels and default config
$panels = array(
	'content'     => 2,
	'description' => 2,
	'authors'     => 2,
	'audience'    => 0,
	'gallery'     => 1,
	'tags'        => 1,
	'access'      => 0,
	'license'     => 2,
	'citations'   => 1,
	'notes'       => 1
);

// Sections that cannot be hidden, ever
$required = array('content', 'description', 'authors');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_INFO'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NAME'); ?>:<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[type]" id="field-type" maxlength="100" value="<?php echo $this->escape($this->row->type); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DESCRIPTION'); ?>:</label>
					<input type="text" name="fields[description]" id="field-description" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" />
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_DRAFT_SECTIONS'); ?></span></legend>

				<table class="admintable">
					<tbody>
						<?php
						foreach ($panels as $panel => $val)
						{
							?>
							<tr>
								<th><?php echo ucfirst($panel); ?>:</th>
								<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="0"<?php echo ($params->get('show_'.$panel, $val) == 0) ? ' checked="checked"':''; ?> <?php if (in_array($panel, $required)) { echo ' disabled="disabled"'; } ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_HIDE'); ?></label></td>
								<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="1"<?php echo ($params->get('show_'.$panel, $val) == 1) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_SHOW'); ?></label></td>
								<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="2"<?php echo ($params->get('show_'.$panel, $val) == 2) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_SHOW_AND_REQUIRE'); ?></label></td>
							</tr>
							<?php
						}
						?>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_METADATA'); ?>:</th>
							<td><label><input type="radio" name="params[show_metadata]" value="0"<?php echo ($params->get('show_metadata', 0) == 0) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_HIDE'); ?></label></td>
							<td><label><input type="radio" name="params[show_metadata]" value="1"<?php echo ($params->get('show_metadata', 0) == 1) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_SHOW'); ?></label></td>
							<td></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER'); ?>:</th>
							<td><label><input type="radio" name="params[show_submitter]" value="0"<?php echo ($params->get('show_submitter', 0) == 0) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_HIDE'); ?></label></td>
							<td><label><input type="radio" name="params[show_submitter]" value="1"<?php echo ($params->get('show_submitter', 0) == 1) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_SHOW'); ?></label></td>
							<td></td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_IS_SUPPORTED'); ?></th>
						<td>
							<span class="<?php echo $aClass; ?>"><?php echo $active; ?></span>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ITEM_CONFIG'); ?></span></legend>

				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?></legend>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE'); ?></span>

						<input class="option" name="fields[contributable]" id="field-contributable1" type="radio" value="1" <?php echo $this->row->contributable == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-contributable1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[contributable]" id="field-contributable0" type="radio" value="0" <?php echo $this->row->contributable == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-contributable0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('Supporting'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE_SUPPORT'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE_SUPPORT'); ?></span>

						<input class="option" name="fields[supporting]" id="field-supporting1" type="radio" value="1" <?php echo $this->row->supporting == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-supporting1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[supporting]" id="field-supporting0" type="radio" value="0" <?php echo $this->row->supporting == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-supporting0"><?php echo Lang::txt('JNO'); ?></label>

						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE_NOTICE'); ?></span>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('Issue DOI'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_DOI_QUESTION'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_DOI_QUESTION'); ?></span>

						<input class="option" name="params[issue_doi]" id="field-issue_doi1" type="radio" value="1" <?php echo ($params->get('issue_doi', 1) == 1) ? ' checked="checked"' : ''; ?> />
						<label for="field-issue_doi1"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></label>
						<br />
						<input class="option" name="params[issue_doi]" id="field-issue_doi2" type="radio" value="2" <?php echo ($params->get('issue_doi', 1) == 2) ? ' checked="checked"' : ''; ?> />
						<label for="field-issue_doi2"><?php echo Lang::txt('JOPTION_OPTIONAL'); ?></label>
						<br />
						<input class="option" name="params[issue_doi]" id="field-issue_doi0" type="radio" value="0" <?php echo ($params->get('issue_doi', 1) == 0) ? ' checked="checked"' : ''; ?> />
						<label for="field-issue_doi0"><?php echo Lang::txt('COM_PUBLICATIONS_NA'); ?></label>
					</div>
				</fieldset>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_CHOOSE_CAT'); ?>">
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_CHOOSE_CAT'); ?></span>

					<label for="field-default_category"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_DEFAULT_CAT'); ?></label>
					<select name="params[default_category]" id="field-default_category">
					<?php foreach ($this->cats as $cat) { ?>
						<option value="<?php echo $cat->id; ?>" <?php echo ($params->get('default_category', 1) == $cat->id) ? ' selected="selected"' : ''; ?>><?php echo $cat->name; ?></option>
					<?php } ?>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>