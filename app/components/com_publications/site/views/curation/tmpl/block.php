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

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Side text from block manifest
$about = $this->manifest->adminTips ? $this->manifest->adminTips : $this->manifest->about;

// Get curator status
$curatorStatus = $this->pub->curation()->getCurationStatus($this->pub, $this->step, 0, 'curator');
?>
<div class="curation-block">
	<h4><?php echo $this->manifest->title; ?></h4>
	<?php if (!$this->pub->curation('blocks', $this->step, 'hasElements')) { ?>
		<div id="<?php echo 'element' . $this->active; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional'; echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?>">
			<div class="element_overview">
				<div class="block-aside">
					<div class="block-info"><?php echo $about; ?></div>
				</div>
				<?php echo $this->pub->curation()->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->title); ?>
				<div class="block-subject">
					<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>
					<?php echo $this->pub->curation()->drawCurationNotice($curatorStatus, $props, 'curator', 'element' . $this->active); ?>
					<?php echo $this->content; ?>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="curation-item">
			<?php echo $this->content; ?>
		</div>
	<?php } ?>
</div>
