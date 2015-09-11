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

// Get requirements
$element = $this->publication->curation('blocks', $this->step, 'elements', $this->element);
$params  = $element->params;

// Get attachment type model
$attModel = new \Components\Publications\Models\Attachments($this->database);

// Get attached items
$attachments = $this->publication->attachments();
$attachments = isset($attachments['elements'][$this->element]) ? $attachments['elements'][$this->element] : NULL;
$attachments = $attModel->getElementAttachments($this->element, $attachments, $params->type);

// Get preselected items
$selected = array();
if ($attachments)
{
	foreach ($attachments as $attach)
	{
		$selected[] = $attach->object_name;
	}
}

?>
<div id="abox-content-wrap">
	<div id="abox-content">
	<script src="/core/plugins/projects/publications/assets/js/selector.js"></script>
		<h3><?php echo Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR'); ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-filesave"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR_SAVE_SELECTION'); ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_CANCEL'); ?></a>
				<?php } ?>
			</span>
		</h3>
		<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url( $this->publication->link('edit')); ?>">
			<fieldset >
				<input type="hidden" name="id" id="projectid" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
				<input type="hidden" name="p" id="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->publication->get('id'); ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->publication->get('version_id'); ?>" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->block; ?>" />
				<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
				<input type="hidden" name="element" value="<?php echo $this->element; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="apply" />
				<input type="hidden" name="move" value="continue" />
				<input type="hidden" id="selecteditems" name="selecteditems" value="" />
			</fieldset>
			<?php if ($this->model->isProvisioned()) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR_PROVISIONED'); ?></p>
			<?php } elseif ($this->items) { ?>
			<ul class="pub-selector database-selector" id="pub-selector">
				<?php foreach ($this->items as $item) {
					$liId = 'choice-' . $item->database_name;
					$sel = in_array($item->database_name, $selected) ? true : false;
					?>
					<li class="c-click databases allowed <?php if ($sel) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
						<span class="item-info"></span>
						<span class="item-wrap"><?php echo $item->title; ?></span>
						<span class="item-fullinfo">
							<?php echo $item->description; ?>
						</span>
					</li>
				<?php } ?>
			</ul>
			<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR_NONE'); ?> <span class="block"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_GO_TO'); ?> <a href="<?php echo Route::url($this->model->link('databases')); ?>"><?php echo Lang::txt('PLG_PROJECTS_DATABASES'); ?></a> <?php echo Lang::txt('PLG_PROJECTS_DATABASES_TO_CREATE'); ?></span></p>
		<?php } ?>
		</form>
	</div>
</div>