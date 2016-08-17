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

// Get block/element properties
$props    = $this->pub->curation('blocks', $this->master->blockId, 'props') . '-' . $this->elementId;
$complete = $this->pub->curation('blocks', $this->master->blockId, 'elementStatus', $this->elementId);
$required = $this->pub->curation('blocks', $this->master->blockId, 'elements', $this->elementId)->params->required;
$elName   = 'element' . $this->elementId;

$active = (($this->active == $this->elementId) || !$this->collapse) ? 1 : 0;
$coming = $this->pub->_curationModel->isComing($this->master->block, $this->master->blockId, $this->active, $this->elementId);
$coming = $this->collapse ? $coming : 0;
$last   = ($this->order == $this->total) ? 1 : 0;
$max    = $this->manifest->params->max;

// Get side text
$aboutText = $this->manifest->about ? $this->manifest->about : NULL;
if ($this->pub->_project->isProvisioned() && isset($this->manifest->aboutProv))
{
	$aboutText = $this->manifest->aboutProv;
}
// Wrap text in a paragraph
if (strlen($aboutText) == strlen(strip_tags($aboutText)))
{
	$aboutText = '<p>' . $aboutText . '</p>';
}

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->master->blockId, $this->elementId, 'author');

// Get attachment model
$modelAttach = new \Components\Publications\Models\Attachments($this->database);

// Get handler model
$modelHandler = new \Components\Publications\Models\Handlers($this->database);

// Is there handler choice?
$handlers 	  = $this->manifest->params->typeParams->handlers;

// Is there handler assigned?
$handler 	  = $this->manifest->params->typeParams->handler;
$useHandles   = ($handlers || $handler ) ? true : false;

if ($handler)
{
	// Load handler
	$handler = $modelHandler->ini($handler);
}

$multiZip = (isset($this->manifest->params->typeParams->multiZip)
		&& $this->manifest->params->typeParams->multiZip == 0)
		? false : true;

$complete = $curatorStatus->status == 1 && $required ? $curatorStatus->status : $complete;
$updated  = $curatorStatus->updated && (($curatorStatus->status == 3 && !$complete)
		|| $curatorStatus->status == 1 || $curatorStatus->status == 0) ? true : false;

$handlerOptions = count($this->attachments) > 0 && $useHandles ? $modelHandler->showHandlers($this->pub, $this->elementId, $handlers, $handler, $this->attachments, $props) : NULL;

$elementUrl = Route::url($this->pub->link('editversion') . '&section='
	. $this->master->block . '&step=' . $this->master->blockId . '&move=continue' . '&el=' . $this->elementId . '#' . $elName);

?>

<div id="<?php echo $elName; ?>" class="blockelement <?php echo $required ? ' el-required' : ' el-optional';
echo $complete == 1 ? ' el-complete' : ' el-incomplete'; ?> <?php if ($coming) { echo ' el-coming'; } ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $updated ? ' el-updated' : ''; echo ($curatorStatus->status == 3 && !$complete) ? ' el-skipped' : ''; ?> ">
	<!-- Showing status only -->
	<div class="element_overview<?php if ($active) { echo ' hidden'; } ?>">
		<div class="block-subject withhandler">
			<span class="checker">&nbsp;</span>
			<h5 class="element-title"><?php echo $this->manifest->label; ?> <?php if (count($this->attachments)) { echo '(' . count($this->attachments) .')'; } ?>
			<span class="element-options"><a href="<?php echo $elementUrl; ?>"><?php echo Lang::txt('[edit]'); ?></a></span>
			</h5>
		</div>
		<div class="block-aside-omega"></div>
	</div>
	<!-- Active editing -->
	<div class="element_editing<?php if (!$active) { echo ' hidden'; } ?>">
		<div class="block-subject withhandler">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo Lang::txt('Required'); ?></span><?php } ?><?php if (!$required) { ?><span class="optional"><?php echo Lang::txt('Optional'); ?></span><?php } ?>
				<?php echo $this->manifest->label; ?> <?php if (count($this->attachments)) { echo '(' . count($this->attachments) . ')'; }?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<div class="list-wrapper">
			<ul class="itemlist">
		<?php if (count($this->attachments) > 0) {
			$i = 1; ?>
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
				}
			}  ?>
				</ul>
				<?php if ($max > count($this->attachments)) {
					// Draw link to select more items
					$this->view('_select', 'attachments')
					     ->set('pub', $this->pub)
					     ->set('type', $this->type)
					     ->set('props', $props)
					     ->display();
				} ?>
			</div>

			<?php if ($curatorStatus->status == 3 && !$complete) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SKIPPED_ITEM'); echo $curatorStatus->authornotice ? ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_REASON') . ':"' . $curatorStatus->authornotice . '"' : ''; ?></p>
			<?php } ?>
		</div>
		<?php if ($handlerOptions)  { ?>
		<div class="handler-aside">
			<?php
				// Present handler options
				echo $handlerOptions;
			?>
		</div>
		<?php } ?>
				<div class="block-info">
				<?php
					$shorten = ($aboutText && strlen($aboutText) > 200) ? 1 : 0;

					if ($shorten)
					{
						$about = \Hubzero\Utility\String::truncate($aboutText, 200, array('html' => true));
						$about.= ' <a href="#more-' . $elName . '" class="more-content">'
									. Lang::txt('PLG_PROJECTS_PUBLICATIONS_READ_MORE') . '</a>';
						$about.= ' <div class="hidden">';
						$about.= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutText . '</div>';
						$about.= ' </div>';
					}
					else
					{
						$about = $aboutText;
					}

					echo $about;
			?>
			</div>
		</div>	
		<?php } ?>
		<?php if ($active && $this->collapse) { ?>
		<div class="clear"></div>
		<div class="withhandler">
			<p class="element-move">
			<?php // display error
			 if ($this->status->getError()) { echo '<span class="element-error">' . $this->status->getError() . '</span>'; } ?>
				<span class="button-wrapper icon-next" id="next-<?php echo $props; ?>">
				<input type="button" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="<?php echo $elName; ?>-apply" class="save-element btn icon-next"/>
				</span>
			</p>
		</div>
		<?php } ?>
	</div>
</div>
