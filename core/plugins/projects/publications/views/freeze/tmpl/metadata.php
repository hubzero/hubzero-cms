<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block/element properties
$props    = $this->pub->curation('blocks', $this->master->blockId, 'props') . '-' . $this->elementId;
$complete = $this->pub->curation('blocks', $this->master->blockId, 'elementStatus', $this->elementId);
$required = $this->pub->curation('blocks', $this->master->blockId, 'elements', $this->elementId)->params->required;

$elName   = 'description-element' . $this->elementId;
$aliasmap = $this->manifest->params->aliasmap;
$field    = $this->manifest->params->field;
$value    = $this->pub && isset($this->pub->$field) ? $this->pub->$field : null;

$editor = $this->manifest->params->input == 'editor' ? 1 : 0;
$aboutTxt = $this->manifest->adminTips
		? $this->manifest->adminTips
		: $this->manifest->about;

$shorten = ($aboutTxt && strlen($aboutTxt) > 200) ? 1 : 0;

if ($shorten)
{
	$about  = \Hubzero\Utility\Str::truncate($aboutTxt, 200);
	$about .= ' <a href="#more-' . $elName . '" class="more-content">' . Lang::txt('COM_PUBLICATIONS_READ_MORE') . '</a>';
	$about .= ' <div class="hidden">';
	$about .= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutTxt . '</div>';
	$about .= ' </div>';
}
else
{
	$about = $aboutTxt;
}

// Get curator status
if ($this->name == 'curator')
{
	$curatorStatus = $this->pub->_curationModel->getCurationStatus(
		$this->pub,
		$this->master->blockId,
		$this->elementId,
		'curator'
	);
}

?>
<?php if ($this->name == 'curator') { ?>
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; echo ($curatorStatus->status == 3 && !$complete) ? ' el-skipped' : ''; ?>">
<?php } else { ?>
	<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete'; ?>">
<?php } ?>
	<!-- Showing status only -->
	<div class="element_overview">
		<?php if ($this->name == 'curator') { ?>
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div></div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->label); ?>
		<div class="block-subject">
		<?php } ?>
			<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>
			<?php if ($this->name == 'curator') { $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', $elName); } ?>
			<?php if ($value) {
				// Parse editor text
				if ($editor)
				{
					$model = new \Components\Publications\Models\Publication($this->pub);
					$value = $model->parse($aliasmap, $field, 'parsed');
				}
				?>
				<div class="element-value"><?php echo $value; ?></div>
			<?php } else { ?>
				<p class="noresults">No user input</p>
				<?php if (!$this->pub->isPublished() && ($this->status->getError() || ($required && !$complete))) { ?>
					<p class="witherror"><?php echo $this->status->getError() ? $this->status->getError() : Lang::txt('Missing required input'); ?></p>
				<?php } ?>
			<?php } ?>
		<?php if ($this->name == 'curator') { ?>
		</div>
		<?php } ?>
	</div>
</div>
