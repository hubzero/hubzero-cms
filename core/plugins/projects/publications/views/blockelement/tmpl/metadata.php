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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

include_once \Component::path('com_publications') . DS . 'models' . DS . 'elements.php';

// Parse data
$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->pub->metadata, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $match[2];
	}
}

// Get block/element properties
$props    = $this->pub->curation('blocks', $this->master->blockId, 'props') . '-' . $this->elementId;
$complete = $this->pub->curation('blocks', $this->master->blockId, 'elementStatus', $this->elementId);
$required = $this->pub->curation('blocks', $this->master->blockId, 'elements', $this->elementId)->params->required;

$elName   = 'element' . $this->elementId;
$aliasmap = $this->manifest->params->aliasmap;
$field    = $this->manifest->params->field;
$value    = $this->pub && isset($this->pub->$field) ? $this->pub->$field : null;
$size = isset($this->manifest->params->maxlength) && $this->manifest->params->maxlength
	? 'maxlength="' . $this->manifest->params->maxlength . '"'
	: '';
$placeholder = isset($this->manifest->params->placeholder)
			? 'placeholder="' . $this->manifest->params->placeholder . '"' : '';
$editor = $this->manifest->params->input == 'editor' ? 1 : 0;
$cols   = isset($this->manifest->params->cols) ? $this->manifest->params->cols : 50;
$rows   = isset($this->manifest->params->rows) ? $this->manifest->params->rows : 6;
$editorMacros = isset($this->manifest->params->editorMacros)
			? $this->manifest->params->editorMacros : 0;
$editorMinimal = isset($this->manifest->params->editorMinimal)
			? $this->manifest->params->editorMinimal : 1;
$editorImages = isset($this->manifest->params->editorImages)
			? $this->manifest->params->editorImages : 0;

// Metadata field?
if ($field == 'metadata')
{
	$field = 'nbtag[' . $aliasmap . ']';
	$value = isset($data[$aliasmap]) ? $data[$aliasmap] : null;
}

$class = $value ? ' be-complete' : '';

// Determine if current element is active/ not yet filled/ last in order
$active = (($this->active == $this->elementId) || !$this->collapse) ? 1 : 0;
$coming = $this->pub->_curationModel->isComing($this->master->block, $this->master->blockId, $this->active, $this->elementId);
$coming = $this->collapse ? $coming : 0;
$last   = ($this->order == $this->total) ? 1 : 0;

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus(
	$this->pub,
	$this->master->blockId,
	$this->elementId,
	'author'
);

$aboutText = $this->manifest->about ? $this->manifest->about : null;

if ($this->pub->_project->isProvisioned() && isset($this->manifest->aboutProv))
{
	$aboutText = $this->manifest->aboutProv;
}

// Wrap text in a paragraph
if (strlen($aboutText) == strlen(strip_tags($aboutText)))
{
	$aboutText = '<p>' . $aboutText . '</p>';
}

$complete = $curatorStatus->status == 1 && $required ? $curatorStatus->status : $complete;
$updated = $curatorStatus->updated && (($curatorStatus->status == 3 && !$complete) || $curatorStatus->status == 1 || $curatorStatus->status == 0) ? true : false;

$elementUrl = Route::url($this->pub->link('editversion') . '&section=' . $this->master->block . '&step=' . $this->master->blockId . '&move=continue' . '&el=' . $this->elementId . '#' . $elName);
?>

<div id="<?php echo $elName; ?>" class="blockelement <?php echo $required ? ' el-required' : ' el-optional'; echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php if ($editor) { echo ' el-editor'; } ?> <?php if ($coming) { echo ' el-coming'; } ?> <?php echo $curatorStatus->status == 1 ? ' el-passed el-reviewed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $updated ? ' el-updated' : ''; echo ($curatorStatus->status == 3 && !$complete) ? ' el-skipped' : ''; ?> ">
	<!-- Showing status only -->
	<div class="element_overview<?php if ($active) { echo ' hidden'; } ?>">
		<div class="block-aside"></div>
		<div class="block-subject">
			<span class="checker">&nbsp;</span>
			<h5 class="element-title"><?php echo $this->manifest->label; ?>
			<span class="element-options"><a href="<?php echo $elementUrl; ?>" class="edit-element" id="<?php echo $elName; ?>-edit"><?php echo Lang::txt('[edit]'); ?></a></span>
			</h5>
			<?php if (!$coming && $value) {
				// Parse editor text
				$val = $value;
				if ($editor)
				{
					$model = new \Components\Publications\Models\Publication($this->pub);
					$val = $model->parse($aliasmap, $this->manifest->params->field, 'parsed');
				}
				?>
				<div class="element-value"><?php echo $val; ?></div>
			<?php } ?>
		</div>
	</div>
	<!-- Active editing -->
	<div class="element_editing<?php if (!$active) { echo ' hidden'; } ?>">
		<div class="block-aside">
			<div class="block-info">
			<?php
				$shorten = ($aboutText && strlen($aboutText) > 200) ? 1 : 0;

				if ($shorten)
				{
					$about = \Hubzero\Utility\Str::truncate($aboutText, 200, array('html' => true));
					$about.= ' <a href="#more-' . $elName . '" class="more-content">'
								. Lang::txt('PLG_PROJECTS_PUBLICATIONS_READ_MORE') . ' &raquo;</a>';
					$about.= ' <div class="hidden">';
					$about.= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutText . '</div>';
					$about.= ' </div>';
				}
				else
				{
					$about = $aboutText;
				}

				echo $about;
			?></div>
		</div>
		<div class="block-subject">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo Lang::txt('Required'); ?></span><?php } ?><?php if (!$required) { ?><span class="optional"><?php echo Lang::txt('Optional'); ?></span><?php } ?>
				<?php echo $this->manifest->label; ?>
				<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
				<?php
				$output = '  <span class="field-wrap' . $class . '">';
				switch ($this->manifest->params->input)
				{
					case 'editor':
						$classes  = $editorMinimal == 1 ? 'minimal ' : '';
						$classes .= ' no-footer ';
						$classes .= $editorImages == 1 ? 'images ' : '';
						$classes .= $editorMacros == 1 ? 'macros ' : '';
						$output .= App::get('editor')->display($field, $value, '', '', $cols, $rows, false, 'pub-' . $elName, null, null, array('class' => $classes));
					break;

					case 'textarea':
						$value = preg_replace("/\r\n/", "\r", trim($value));
						$output .= '<textarea name="' . $field . '" id="pub-' . $elName . '" ' . $size.' ' . $placeholder . ' cols="' . $cols . '" rows="' . $rows . '">' . $value . '</textarea>';
					break;

					case 'text':
					default:
						$output .= '<input type="text" name="' . $field . '" id="pub-' . $elName . '" value="' . $this->escape($value) . '" ' . $size .' ' . $placeholder . ' />';
					break;
				}
				$output .= '  </span>';
				echo $output; ?>
			</label>
			<?php if ($curatorStatus->status == 3 && !$complete) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SKIPPED_ITEM'); echo $curatorStatus->authornotice ? ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_REASON') . ':"' . $curatorStatus->authornotice . '"' : ''; ?></p>
			<?php } ?>
			<?php // Navigate to next element
				if ($active && $this->collapse) { ?>
				<p class="element-move">
					<span class="button-wrapper icon-next" id="next-<?php echo $props; ?>">
						<input type="button" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="<?php echo $elName; ?>-apply" class="save-element btn icon-next"/>
					</span>
					<span class="button-wrapper icon-apply">
						<input type="button" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_APPLY_CHANGES'); ?>" id="apply-<?php echo $props; ?>" class="save-element btn icon-apply" />
					</span>
				</p>
			<?php } ?>
		</div>
	</div>
</div>
