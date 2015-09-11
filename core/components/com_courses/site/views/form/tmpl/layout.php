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

$this->css('form.css')
     ->js('layout.js');

$this->css('jquery.ui.css', 'system')
     ->js('jquery.iframe-transport', 'system')
     ->js('jquery.fileupload', 'system');

?>
<section class="main section courses-form">
	<noscript>
		<div class="error">You must enable JavaScript to annotate PDFs for deployment as forms, sorry.</div>
	</noscript>
	<form action="" method="post">
		<div id="saved-notification" class="passed-box">Save complete</div>
		<label>
			Title:
			<?php if (!$this->readonly) : ?>
				<input type="text" class="required" id="title" value="<?php echo str_replace('"', '&quot;', $this->title) ?>" />
			<?php else : ?>
				<?php echo $this->title ?>
			<?php endif; ?>
				<span>
			<p id="title-error" class="error"></p>
		</label>
		<ol id="pages"<?php echo ($this->readonly) ? ' class="readonly"' : '' ?>>
			<?php
				$tabs = array();
				$layout = $this->pdf->getPageLayout();
				$this->pdf->eachPage(function($src, $idx) use(&$tabs, $layout) {
					$tabs[] = '<li><a href="#page-'.$idx.'"'.($idx == 1 ? ' class="current"' : '').'>'.$idx.'</a></li>';
					echo '<li id="page-'.$idx.'">';
					echo '<img src="'.$src.'" />';
					if (isset($layout[$idx - 1])) {
						$qidx = 0;
						foreach ($layout[$idx - 1] as $group) {
							echo '<div class="group-marker" style="width: '.$group['width'].'px; height: '.$group['height'].'px; top: '.$group['top'].'px; left: '.$group['left'].'px;">';
							echo '<div class="group-marker-header"></div>';
							echo '<button class="remove">x</button>';
							foreach ($group['answers'] as $aidx=>$ans) {
								echo '<div class="radio-container'.($ans['correct'] ? ' selected' : '').'" style="top: '.($ans['top'] - $group['top'] - 5).'px; left: '.($ans['left'] - $group['left'] - 26).'px;">';
								echo '<button class="remove">x</button>';
								echo '<input name="question-saved-'.$idx.'-'.$qidx.'" value="'.$aidx.'" class="placeholder"'.($ans['correct'] ? ' checked="checked"' : '').' type="radio" />';
								echo '</div>';
							}
							++$qidx;
							echo '</div>';
						}
					}
					echo '</li>';
			}); ?>
		</ol>
		<div class="toolbar">
			<?php if (!$this->readonly) : ?>
				<div><a href="" id="save">Save and Close</a></div>
				<div class="new-upload-button">
					<input data-url="<?php echo Route::url($this->base.'&task=form.saveLayout&formId='.$this->pdf->getId()); ?>" type="file" name="pdf" id="new-upload" />
					<span>Upload New PDF</span>
				</div>
			<?php endif; ?>
			<div><a href="/courses/form" id="done"><?php echo ($this->readonly) ? 'Done' : 'Cancel' ?></a></div>
			<?php if (!$this->readonly) : ?>
				<div class="question-info">
					<p>
						<span class="questions-total"><?php echo $this->pdf->getQuestionCount() ?></span> question(s) total, <span class="questions-unsaved">0</span> changes unsaved
					</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="navbar">
			<ol id="page-tabs">
				<?php echo implode("\n", $tabs); ?>
			</ol>
		</div>
		<p id="layout-error" class="error"></p>
		<div class="clear"></div>
	</form>
</section>