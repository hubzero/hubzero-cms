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

$elName   = 'content-element' . $this->elementId;
$max      = $this->manifest->params->max;
$multiZip = (isset($this->manifest->params->typeParams->multiZip) && $this->manifest->params->typeParams->multiZip == 0)
			? false
			: true;

// Customize title
$defaultTitle = $this->manifest->params->title
			? str_replace('{pubtitle}', $this->pub->title,
			$this->manifest->params->title) : null;
$defaultTitle = $this->manifest->params->title
			? str_replace('{pubversion}', $this->pub->version_label,
			$defaultTitle) : null;

$error 			= $this->status->getError();

$aboutTxt 		= $this->manifest->adminTips
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

// Get version params and extract bundle name
$bundleName  = $this->pub->params->get($elName . 'bundlename', $defaultTitle);
$bundleName  = $bundleName ? $bundleName : 'bundle';
$bundleName .= '.zip';

// Get attachment model
$modelAttach = new \Components\Publications\Models\Attachments($this->database);

// Get handler model
$modelHandler = new \Components\Publications\Models\Handlers($this->database);

// Is there handler choice?
$handlers = $this->manifest->params->typeParams->handlers;

// Is there handler assigned?
$handler = $this->manifest->params->typeParams->handler;
$useHandles = ($handlers || $handler ) ? true : false;

if ($handler)
{
	// Load handler
	$handler = $modelHandler->ini($handler);
}

$bundleUrl = Route::url($this->pub->link('serve') . '&el=' . $this->elementId . '&download=1');

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
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div>
		</div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->label); ?>
		<div class="block-subject">
		<?php } ?>
			<h5 class="element-title"><?php echo $this->manifest->label; ?>
				<?php if (count($this->attachments)) { echo ' (' . count($this->attachments) . ')'; }?>
				<?php if (count($this->attachments) > 1 && $multiZip && $this->type == 'file') { ?><span class="download-all"><a href="<?php echo $bundleUrl; ?>" title="<?php echo $bundleName; ?>"><?php echo Lang::txt('Download all'); ?></a></span><?php } ?></h5>
				<?php if ($this->name == 'curator') { echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', $elName); } ?>
		<?php if (count($this->attachments) > 0) { ?>
		<div class="list-wrapper">
			<ul class="itemlist">
		<?php	$i= 1; ?>
				<?php foreach ($this->attachments as $att) {

					// Collect data
					$data = $modelAttach->buildDataObject(
						$this->type,
						$att,
						$this,
						$i
					);
					if ($data)
					{
						$i++;

						// Draw attachment
						echo $modelAttach->drawAttachment(
							$att->type,
							$data,
							$this->manifest->params->typeParams,
							$handler
						);
					}
				} ?>
			</ul>
		</div>
		<?php } else {  ?>
			<p class="noresults"><?php echo $this->name == 'curator' ? Lang::txt('No user input') : Lang::txt('No items attached'); ?></p>
		<?php } ?>

			<?php if ($this->pub->state != 1 && ($error || ($required && !$complete))) { ?>
				<p class="witherror"><?php echo $error ? $error : Lang::txt('Missing required input'); ?></p>
			<?php } else { ?>

			<?php } ?>
		<?php if ($this->name == 'curator') { ?>
		</div>
		<?php } ?>
	</div>
</div>
