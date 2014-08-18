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

$this->css('form.css')
     ->js('layout.js');

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');
\Hubzero\Document\Assets::addSystemScript('jquery.iframe-transport');
\Hubzero\Document\Assets::addSystemScript('jquery.fileupload');

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
					<input data-url="<?php echo JRoute::_($this->base.'&task=form.saveLayout&formId='.$this->pdf->getId()); ?>" type="file" name="pdf" id="new-upload" />
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