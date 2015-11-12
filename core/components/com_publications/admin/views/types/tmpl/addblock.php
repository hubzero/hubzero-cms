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

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE') . ' - ' . $this->row->type . ': ' . Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK'), 'addedit.png');
Toolbar::save('saveblock');
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;

$blockSelection = array('active' => array());
$masterBlocks = array();
foreach ($this->blocks as $b)
{
	$masterBlocks[$b->block] = $b;
}
foreach ($blocks as $blockId => $block)
{
	$blockSelection['active'][] = $block->name;
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>
<p class="backto"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->row->id); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_BACK') . ' ' . $this->row->type . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE'); ?></a></p>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK'); ?></span></legend>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="saveblock" />

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-newblock"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SELECT_BLOCK'); ?>:</label>
				<select name="newblock" id="field-newblock">
				<?php foreach ($this->blocks as $sBlock) {
					if (!in_array($sBlock->block, $blockSelection['active']) || $sBlock->maximum > 1) {  ?>
					<option value="<?php echo $sBlock->block; ?>"><?php echo $sBlock->block; ?></option>
				<?php  }
				} ?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-order"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_INSERT_BLOCK_BEFORE'); ?>:</label>
				<select name="before" id="field-order">
				<?php foreach ($blocks as $blockId => $block) { ?>
					<option value="<?php echo $blockId; ?>"><?php echo $block->name; ?></option>
				<?php  } ?>
				</select>
			</div>
		</fieldset>
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>