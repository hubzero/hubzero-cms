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
JToolBarHelper::title(JText::_('Publication Master Type') . ': [ ' . $text . ' ]', 'addedit.png');
JToolBarHelper::save('save', 'Save');
JToolBarHelper::cancel();

// Determine whether master type is supported in current version of hub code
$aClass  = 'item_off';
$active  = 'off (type not supported)';

// If we got a plugin - type is supported
if (JPluginHelper::isEnabled('projects', $this->row->alias))
{
	$aClass  = 'item_on';
	$active  = 'on (type supported)';
}

$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}

$params = new $paramsClass($this->row->params);

// Available panels and default config
$panels = array(
	'content' 		=> 2,
	'description' 	=> 2,
	'authors'		=> 2,
	'audience'		=> 0,
	'gallery'		=> 1,
	'tags'			=> 1,
	'access'		=> 0,
	'license'		=> 2,
	'citations'		=> 1,
	'notes'			=> 1
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

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Master Type Information'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-type"><?php echo JText::_('Name'); ?>:<span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[type]" id="field-type" maxlength="100" value="<?php echo $this->escape($this->row->type); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('Alias'); ?>:</label>
				<input type="text" name="fields[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('Description'); ?>:</label>
				<input type="text" name="fields[description]" id="field-description" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Draft Panels / Sections'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<?php
					foreach ($panels as $panel => $val)
					{
						?>
						<tr>
							<th><?php echo ucfirst($panel); ?>:</th>
							<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="0"<?php echo ($params->get('show_'.$panel, $val) == 0) ? ' checked="checked"':''; ?> <?php if (in_array($panel, $required)) { echo ' disabled="disabled"'; } ?> /> <?php echo JText::_('COM_PUBLICATIONS_HIDE'); ?></label></td>
							<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="1"<?php echo ($params->get('show_'.$panel, $val) == 1) ? ' checked="checked"':''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_SHOW'); ?></label></td>
							<td><label><input type="radio" name="params[show_<?php echo $panel; ?>]" value="2"<?php echo ($params->get('show_'.$panel, $val) == 2) ? ' checked="checked"':''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_SHOW_AND_REQUIRE'); ?></label></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<th><?php echo JText::_('Metadata'); ?>:</th>
						<td><label><input type="radio" name="params[show_metadata]" value="0"<?php echo ($params->get('show_metadata', 0) == 0) ? ' checked="checked"' : ''; ?> /> hide</label></td>
						<td><label><input type="radio" name="params[show_metadata]" value="1"<?php echo ($params->get('show_metadata', 0) == 1) ? ' checked="checked"' : ''; ?> /> show</label></td>
						<td></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Submitter'); ?>:</th>
						<td><label><input type="radio" name="params[show_submitter]" value="0"<?php echo ($params->get('show_submitter', 0) == 0) ? ' checked="checked"' : ''; ?> /> hide</label></td>
						<td><label><input type="radio" name="params[show_submitter]" value="1"<?php echo ($params->get('show_submitter', 0) == 1) ? ' checked="checked"' : ''; ?> /> show</label></td>
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
	<div class="col width-50 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Type supported?'); ?></th>
					<td>
						<div class="notice"><?php echo JText::_('There may or may not be a plugin to support this master type in the current version of hub code'); ?></div>
						<span class="<?php echo $aClass; ?>"><?php echo $active; ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Item Configuration'); ?></span></legend>

			<fieldset>
				<legend><?php echo JText::_('Contributable'); ?></legend>
				<div class="input-wrap" data-hint="<?php echo JText::_('Offered as choice for primary content?'); ?>">
					<span class="hint"><?php echo JText::_('Offered as choice for primary content?'); ?></span>

					<input class="option" name="fields[contributable]" id="field-contributable1" type="radio" value="1" <?php echo $this->row->contributable == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-contributable1"><?php echo JText::_('Yes'); ?></label>
					<br />
					<input class="option" name="fields[contributable]" id="field-contributable0" type="radio" value="0" <?php echo $this->row->contributable == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-contributable0"><?php echo JText::_('No'); ?></label>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('Supporting'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('Offered as choice for supporting content?'); ?>">
					<span class="hint"><?php echo JText::_('Offered as choice for supporting content?'); ?></span>

					<input class="option" name="fields[supporting]" id="field-supporting1" type="radio" value="1" <?php echo $this->row->supporting == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-supporting1"><?php echo JText::_('Yes*'); ?></label>
					<br />
					<input class="option" name="fields[supporting]" id="field-supporting0" type="radio" value="0" <?php echo $this->row->supporting == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-supporting0"><?php echo JText::_('No'); ?></label>

					<span class="hint"><?php echo JText::_('*May be unsupported in the current version of hub code'); ?></span>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('Issue DOI'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('Is DOI required/optional/inapplicable for this type?'); ?>">
					<span class="hint"><?php echo JText::_('Is DOI required/optional/inapplicable for this type?'); ?></span>

					<input class="option" name="params[issue_doi]" id="field-issue_doi1" type="radio" value="1" <?php echo ($params->get('issue_doi', 1) == 1) ? ' checked="checked"' : ''; ?> />
					<label for="field-issue_doi1"><?php echo JText::_('Required'); ?></label>
					<br />
					<input class="option" name="params[issue_doi]" id="field-issue_doi2" type="radio" value="2" <?php echo ($params->get('issue_doi', 1) == 2) ? ' checked="checked"' : ''; ?> />
					<label for="field-issue_doi2"><?php echo JText::_('Optional (submitter may decide)'); ?></label>
					<br />
					<input class="option" name="params[issue_doi]" id="field-issue_doi0" type="radio" value="0" <?php echo ($params->get('issue_doi', 1) == 0) ? ' checked="checked"' : ''; ?> />
					<label for="field-issue_doi0"><?php echo JText::_('No DOI (inapplicable)'); ?></label>
				</div>
			</fieldset>

			<div class="input-wrap" data-hint="<?php echo JText::_('Choose a default category assigned for this type'); ?>">
				<span class="hint"><?php echo JText::_('Choose a default category assigned for this type'); ?></span>

				<label for="field-default_category"><?php echo JText::_('Default Category'); ?></label>
				<select name="params[default_category]" id="field-default_category">
				<?php foreach ($this->cats as $cat) { ?>
					<option value="<?php echo $cat->id; ?>" <?php echo ($params->get('default_category', 1) == $cat->id) ? ' selected="selected"' : ''; ?>><?php echo $cat->name; ?></option>
				<?php } ?>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>