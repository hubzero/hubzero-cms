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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));
JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION') . ' ' . JText::_('COM_PUBLICATIONS_MASTER_TYPE') . ': [ ' . $text . ' ]', 'addedit.png');
if ($this->row->id)
{
	JToolBarHelper::apply();
}
JToolBarHelper::save();
JToolBarHelper::cancel();

$params = new JRegistry($this->row->params);

// Get curator group cn
$curatorGroup = '';
if ($this->row->curatorgroup && $group = \Hubzero\User\Group::getInstance($this->row->curatorgroup))
{
	$curatorGroup = $group->get('cn');
}
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks	   = $manifest->blocks;

$blockSelection = array('active' => array());
$masterBlocks = array();
foreach ($this->blocks as $b)
{
	$masterBlocks[$b->block] = $b;
}

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
		<?php if ($this->row->id) { ?>
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<fieldset class="adminform">
			<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_MTYPE_INFO'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-type"><?php echo JText::_('COM_PUBLICATIONS_FIELD_NAME'); ?>:<span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[type]" id="field-type" maxlength="100" value="<?php echo $this->escape($this->row->type); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="fields[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_PUBLICATIONS_FIELD_DESCRIPTION'); ?>:</label>
				<input type="text" name="fields[description]" id="field-description" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE'); ?>">
				<label for="field-contributable"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?></label>
				<select name="fields[contributable]" id="field-contributable">
					<option value="0" <?php echo $this->row->contributable == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
					<option value="1" <?php echo $this->row->contributable == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_CURATION_CONFIG'); ?></span></legend>
			<?php if ($this->row->id) { ?>
			<div class="input-wrap">
				<label for="field-curatorgroup"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CURATOR_GROUP'); ?>:</label>
				<input type="text" name="curatorgroup" id="field-curatorgroup" maxlength="255" value="<?php echo $curatorGroup; ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-defaulttitle"><?php echo JText::_('COM_PUBLICATIONS_CURATION_DEFAULT_TITLE'); ?>:</label>
				<input type="text" name="curation[params][default_title]" id="field-defaulttitle" maxlength="255" value="<?php echo $curParams->default_title;  ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-defaultcategory"><?php echo JText::_('COM_PUBLICATIONS_CURATION_DEFAULT_CATEGORY'); ?>:</label>
				<select name="curation[params][default_category]" id="field-defaultcategory">
				<?php foreach ($this->cats as $cat) { ?>
					<option value="<?php echo $cat->id; ?>" <?php echo $curParams->default_category == $cat->id ? ' selected="selected"' : ''; ?>><?php echo $cat->name; ?></option>
				<?php } ?>
				</select>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_HINT'); ?>">
				<label for="field-requiredoi"><?php echo JText::_('COM_PUBLICATIONS_CURATION_REQUIRE_DOI'); ?></label>

				<select name="curation[params][require_doi]" id="field-requiredoi">
					<option value="0" <?php echo $curParams->require_doi == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_NO'); ?></option>
					<option value="1" <?php echo $curParams->require_doi == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_YES'); ?></option>
					<option value="2" <?php echo $curParams->require_doi == 2 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_OPTIONAL'); ?></option>
				</select>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_HINT'); ?>">
				<label for="field-showarchive"><?php echo JText::_('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL'); ?></label>
				<select name="curation[params][show_archival]" id="field-showarchive">
					<option value="0" <?php echo (!isset($curParams->show_archival) || $curParams->show_archival == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_NO'); ?></option>
					<option value="1" <?php echo (isset($curParams->show_archival) && $curParams->show_archival == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_YES'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-listall"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LIST_ALL'); ?></label>
				<select name="curation[params][list_all]" id="field-listall">
					<option value="0" <?php echo (!isset($curParams->list_all) || $curParams->list_all == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
					<option value="1" <?php echo (isset($curParams->list_all) && $curParams->list_all == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-listlabel"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LIST_LABEL'); ?>:</label>
				<input type="text" name="curation[params][list_label]" id="field-listlabel" maxlength="255" value="<?php echo (isset($curParams->list_label) && $curParams->list_label) ? $curParams->list_label : '';  ?>" />
			</div>
			<?php } else {
				echo '<p class="warning">' . JText::_('COM_PUBLICATIONS_CURATION_SAVE_NEW') . '</p>';
			} ?>
		</fieldset>
	</div>
	<div class="clr"></div>
<?php if ($this->row->id) { $i=1; ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_CURATION_BLOCKS'); ?></span> <a class="editthis" href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=addblock&amp;id=<?php echo $this->row->id; ?>">[<?php echo JText::_('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK'); ?>]</a></legend>
			<?php foreach ($blocks as $sequence => $block) {
				$blockSelection['active'][] = $block->name;
				$blockMaster = $masterBlocks[$block->name];
				?>
			<fieldset class="adminform">
				<legend><span class="block-sequence"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID') . ': ' . $sequence; ?> - <?php echo $block->name; ?></span></legend>
				<div class="input-wrap">
					<div class="col width-20 fltlft">
						<div class="input-wrap">
							<label class="block"><input type="radio" name="curation[blocks][<?php echo $sequence; ?>][active]" value="1" <?php if (!isset($block->active) || $block->active == 1 ) { echo 'checked="checked"'; } ?> /> <?php echo JText::_('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></label>
							<label class="block"><input type="radio" name="curation[blocks][<?php echo $sequence; ?>][active]" value="0" <?php if (isset($block->active) && $block->active == 0 ) { echo 'checked="checked"'; } ?> <?php if ($blockMaster->minimum > 0) { echo ' disabled="disabled"'; } ?> /> <?php echo JText::_('COM_PUBLICATIONS_STATUS_INACTIVE'); ?></label>
						</div>
						<div class="input-wrap tweakblock">
							<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_ORDER'); ?>: <?php echo $i; ?> <a href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=editblockorder&amp;id=<?php echo $this->row->id; ?>">[<?php echo JText::_('COM_PUBLICATIONS_EDIT'); ?>]</a></label>
						</div>
					</div>
					<div class="col width-40 fltlft blockprop">
						<h5><?php echo JText::_('COM_PUBLICATIONS_BLOCK_PROPERTIES'); ?></h5>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-label"><?php echo JText::_('COM_PUBLICATIONS_FIELD_BLOCK_LABEL'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][label]" id="field-block-<?php echo $sequence; ?>-label" maxlength="255" value="<?php echo $block->label;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-title"><?php echo JText::_('COM_PUBLICATIONS_FIELD_TITLE'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][title]" id="field-block-<?php echo $sequence; ?>-title" maxlength="255" value="<?php echo $block->title;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-draftHeading"><?php echo JText::_('COM_PUBLICATIONS_FIELD_DRAFT_HEADING'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][draftHeading]" id="field-block-<?php echo $sequence; ?>-draftHeading" maxlength="255" value="<?php echo $block->draftHeading;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-draftTagline"><?php echo JText::_('COM_PUBLICATIONS_FIELD_DRAFT_TAGLINE'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][draftTagline]" id="field-block-<?php echo $sequence; ?>-draftTagline" maxlength="255" value="<?php echo $block->draftTagline;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-about"><?php echo JText::_('COM_PUBLICATIONS_FIELD_BLOCK_ABOUT'); ?>:</label>
							<textarea name="curation[blocks][<?php echo $sequence; ?>][about]" id="field-block-<?php echo $sequence; ?>-about"><?php echo $block->about;  ?></textarea>
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $sequence; ?>-adminTips"><?php echo JText::_('COM_PUBLICATIONS_FIELD_BLOCK_ADMIN_TIPS'); ?>:</label>
							<textarea name="curation[blocks][<?php echo $sequence; ?>][adminTips]" id="field-block-<?php echo $sequence; ?>-adminTips"><?php echo $block->adminTips;  ?></textarea>
						</div>
					</div>
					<div class="col width-40 fltlft blockparams">
						<h5><?php echo JText::_('COM_PUBLICATIONS_FIELD_PARAMS'); ?></h5>
						<?php foreach ($block->params as $paramname => $paramvalue) { ?>
						<div class="input-wrap">
							<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($paramname)); ?></label>
							<?php
								if (is_array($paramvalue)) {
								$val = implode(',', $paramvalue);
							?>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $val;  ?>" />
							<?php } elseif (is_numeric($paramvalue)) { ?>
							<select name="curation[blocks][<?php echo $sequence; ?>][params][<?php echo $paramname; ?>]">
								<option value="0" <?php echo $paramvalue == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
								<option value="1" <?php echo $paramvalue == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
							</select>
							<?php } else { ?>
								<input type="text" name="curation[blocks][<?php echo $sequence; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $paramvalue;  ?>" />
							<?php } ?>
						</div>
						<?php } ?>
						<?php if ($block->elements) { ?>
						<h5><?php echo JText::_('COM_PUBLICATIONS_FIELD_BLOCK_ELEMENTS'); ?> <span class="editthis"><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=editelements&amp;id=<?php echo $this->row->id . '&amp;bid=' . $sequence; ?>">[<?php echo JText::_('COM_PUBLICATIONS_EDIT'); ?>]</a></span></h5>
						<?php foreach ($block->elements as $elementId => $element) { ?>
							<div class="input-wrap">
								<span class="block-sequence"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID') . ': ' . $elementId; ?> - <?php echo $element->name; ?> - <?php echo $element->name == 'metadata' ? $element->params->input : $element->params->type; ?></span>
								<span class="el-details"><?php echo $element->label; ?></span>
							</div>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</fieldset>
			<?php $i++; } ?>
		</fieldset>
	</div>
<?php } ?>
	<?php echo JHTML::_('form.token'); ?>
	<p class="sublink"><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=advanced&amp;id=<?php echo $this->row->id; ?>"><?php echo JText::_('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING'); ?></a></p>
</form>