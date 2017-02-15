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

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE') . ': ' . $text, 'addedit.png');
if ($this->row->id)
{
	Toolbar::apply();
}
Toolbar::save();
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);

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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span6">
			<?php if ($this->row->id) { ?>
				<table class="meta">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID'); ?></th>
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
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE'); ?>">
					<label for="field-contributable"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?></label>
					<select name="fields[contributable]" id="field-contributable">
						<option value="0" <?php echo $this->row->contributable == 0 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JNO'); ?></option>
						<option value="1" <?php echo $this->row->contributable == 1 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JYES'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_CONFIG'); ?></span></legend>
				<?php if ($this->row->id) { ?>
					<div class="input-wrap">
						<label for="field-curatorgroup"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATOR_GROUP'); ?>:</label>
						<input type="text" name="curatorgroup" id="field-curatorgroup" maxlength="255" value="<?php echo $curatorGroup; ?>" />
					</div>
					<div class="input-wrap">
						<label for="field-defaulttitle"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_DEFAULT_TITLE'); ?>:</label>
						<input type="text" name="curation[params][default_title]" id="field-defaulttitle" maxlength="255" value="<?php echo $curParams->default_title;  ?>" />
					</div>
					<div class="input-wrap">
						<label for="field-defaultcategory"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_DEFAULT_CATEGORY'); ?>:</label>
						<select name="curation[params][default_category]" id="field-defaultcategory">
						<?php foreach ($this->cats as $cat) { ?>
							<option value="<?php echo $cat->id; ?>" <?php echo $curParams->default_category == $cat->id ? ' selected="selected"' : ''; ?>><?php echo $cat->name; ?></option>
						<?php } ?>
						</select>
					</div>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_HINT'); ?>">
						<label for="field-requiredoi"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI'); ?></label>

						<select name="curation[params][require_doi]" id="field-requiredoi">
							<option value="0" <?php echo $curParams->require_doi == 0 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_NO'); ?></option>
							<option value="1" <?php echo $curParams->require_doi == 1 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_YES'); ?></option>
							<option value="2" <?php echo $curParams->require_doi == 2 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_OPTIONAL'); ?></option>
						</select>
					</div>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_HINT'); ?>">
						<label for="field-showarchive"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL'); ?></label>
						<select name="curation[params][show_archival]" id="field-showarchive">
							<option value="0" <?php echo (!isset($curParams->show_archival) || $curParams->show_archival == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_NO'); ?></option>
							<option value="1" <?php echo (isset($curParams->show_archival) && $curParams->show_archival == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_YES'); ?></option>
							<option value="2" <?php echo (isset($curParams->show_archival) && $curParams->show_archival == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_PARTIAL'); ?></option>
						</select>
					</div>
					<div class="input-wrap">
						<label for="field-listall"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LIST_ALL'); ?></label>
						<select name="curation[params][list_all]" id="field-listall">
							<option value="0" <?php echo (!isset($curParams->list_all) || $curParams->list_all == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JNO'); ?></option>
							<option value="1" <?php echo (isset($curParams->list_all) && $curParams->list_all == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JYES'); ?></option>
						</select>
					</div>
					<div class="input-wrap">
						<label for="field-listlabel"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LIST_LABEL'); ?>:</label>
						<input type="text" name="curation[params][list_label]" id="field-listlabel" maxlength="255" value="<?php echo (isset($curParams->list_label) && $curParams->list_label) ? $curParams->list_label : '';  ?>" />
					</div>
				<?php } else {
					echo '<p class="warning">' . Lang::txt('COM_PUBLICATIONS_CURATION_SAVE_NEW') . '</p>';
				} ?>
			</fieldset>
		</div>
	</div>

	<?php if ($this->row->id) { $i=1; ?>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_BLOCKS'); ?></span> <a class="editthis" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addblock&id=' . $this->row->id ); ?>">[<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK'); ?>]</a></legend>
			<?php foreach ($blocks as $blockId => $block) {
				$blockMaster = $masterBlocks[$block->name];
				?>
			<fieldset class="adminform">
				<legend><span class="block-id"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID') . ': ' . $blockId; ?> - <?php echo $block->name; ?></span></legend>
				<div class="grid">
					<div class="col span2">
						<div class="input-wrap">
							<label class="block"><input type="radio" name="curation[blocks][<?php echo $blockId; ?>][active]" value="1" <?php if (!isset($block->active) || $block->active == 1 ) { echo 'checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></label>
							<label class="block"><input type="radio" name="curation[blocks][<?php echo $blockId; ?>][active]" value="0" <?php if (isset($block->active) && $block->active == 0 ) { echo 'checked="checked"'; } ?> <?php if ($blockMaster->minimum > 0 && !in_array($block->name, $blockSelection['active'])) { echo ' disabled="disabled"'; } ?> /> <?php echo Lang::txt('COM_PUBLICATIONS_STATUS_INACTIVE'); ?></label>
						</div>
						<div class="input-wrap tweakblock">
							<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ORDER'); ?>: <?php echo $i; ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editblockorder&id=' . $this->row->id ); ?>">[<?php echo Lang::txt('COM_PUBLICATIONS_EDIT'); ?>]</a>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeblock&id=' . $this->row->id . '&blockid=' . $blockId ); ?>">[<?php echo Lang::txt('COM_PUBLICATIONS_DELETE'); ?>]</a>
							</label>
						</div>
					</div>
					<div class="col span5 blockprop">
						<h5><?php echo Lang::txt('COM_PUBLICATIONS_BLOCK_PROPERTIES'); ?></h5>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-label"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_LABEL'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $blockId; ?>][label]" id="field-block-<?php echo $blockId; ?>-label" maxlength="255" value="<?php echo $block->label;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-title"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $blockId; ?>][title]" id="field-block-<?php echo $blockId; ?>-title" maxlength="255" value="<?php echo $block->title;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-draftHeading"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DRAFT_HEADING'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $blockId; ?>][draftHeading]" id="field-block-<?php echo $blockId; ?>-draftHeading" maxlength="255" value="<?php echo $block->draftHeading;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-draftTagline"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DRAFT_TAGLINE'); ?>:</label>
							<input type="text" name="curation[blocks][<?php echo $blockId; ?>][draftTagline]" id="field-block-<?php echo $blockId; ?>-draftTagline" maxlength="255" value="<?php echo $block->draftTagline;  ?>" />
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-about"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ABOUT'); ?>:</label>
							<textarea name="curation[blocks][<?php echo $blockId; ?>][about]" id="field-block-<?php echo $blockId; ?>-about"><?php echo $block->about;  ?></textarea>
						</div>
						<div class="input-wrap">
							<label for="field-block-<?php echo $blockId; ?>-adminTips"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ADMIN_TIPS'); ?>:</label>
							<textarea name="curation[blocks][<?php echo $blockId; ?>][adminTips]" id="field-block-<?php echo $blockId; ?>-adminTips"><?php echo $block->adminTips;  ?></textarea>
						</div>
					</div>
					<div class="col span5 blockparams">
						<h5><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PARAMS'); ?></h5>
						<?php foreach ($block->params as $paramname => $paramvalue) { ?>
						<div class="input-wrap">
							<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($paramname)); ?></label>
							<?php
								if (is_array($paramvalue)) {
								$val = implode(',', $paramvalue);
							?>
							<input type="text" name="curation[blocks][<?php echo $blockId; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $val;  ?>" />
							<?php } elseif (is_numeric($paramvalue)) { ?>
							<select name="curation[blocks][<?php echo $blockId; ?>][params][<?php echo $paramname; ?>]">
								<option value="0" <?php echo $paramvalue == 0 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JNO'); ?></option>
								<option value="1" <?php echo $paramvalue == 1 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JYES'); ?></option>
							</select>
							<?php } else { ?>
								<input type="text" name="curation[blocks][<?php echo $blockId; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $paramvalue;  ?>" />
							<?php } ?>
						</div>
						<?php } ?>
						<?php if ($block->elements) { ?>
						<h5><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ELEMENTS'); ?> <span class="editthis"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editelements&id=' . $this->row->id . '&bid=' . $blockId ); ?>">[<?php echo Lang::txt('COM_PUBLICATIONS_EDIT'); ?>]</a></span></h5>
						<?php foreach ($block->elements as $elementId => $element) { ?>
							<div class="input-wrap">
								<span class="block-id"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID') . ': ' . $elementId; ?> - <?php echo $element->name; ?> - <?php echo $element->name == 'metadata' ? $element->params->input : $element->params->type; ?></span>
								<span class="el-details"><?php echo $element->label; ?></span>
							</div>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</fieldset>
			<?php $blockSelection['active'][] = $block->name; $i++; } ?>
		</fieldset>
	<?php } ?>

	<?php echo Html::input('token'); ?>

	<?php if ($this->row->id) { ?>
		<p class="sublink"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=advanced&id=' . $this->row->id ); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING'); ?></a></p>
	<?php } ?>
</form>
