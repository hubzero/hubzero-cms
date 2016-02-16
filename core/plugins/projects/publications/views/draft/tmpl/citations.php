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

$selectUrl = Route::url( $this->pub->link('editversionid') . '&active=links&action=select' . '&p=' . $props);

$elName = "citationsPick";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

$citationFormat = $this->pub->config('citation_format', 'apa');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete == 1 ? ' el-complete' : ' el-incomplete'; ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?> ">
	<div class="element_editing">
		<div class="pane-wrapper">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><?php } else { ?><span class="optional"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?></span><?php } ?>
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CITATIONS')); ?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<div class="list-wrapper">
			<?php if (count($this->pub->_citations) > 0) {
				$i= 1;

				$formatter = new \Components\Citations\Helpers\Format;
				$formatter->setTemplate($citationFormat);
				?>
					<ul class="itemlist" id="citations-list">
					<?php foreach ($this->pub->_citations as $cite) {

							$citeText = $cite->formatted
										? '<p>' . $cite->formatted . '</p>'
										: \Components\Citations\Helpers\Format::formatReference($cite, '');
						 ?>
						<li>
							<span class="item-options">
									<a href="<?php echo Route::url($this->pub->link('editversionid') . '&active=links&action=newcite&cid=' . $cite->id . '&p=' . $props); ?>" class="item-edit showinbox" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT'); ?>">&nbsp;</a>
									<a href="<?php echo Route::url($this->pub->link('editversionid') . '&active=links&action=deletecitation&cid=' . $cite->id . '&p=' . $props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
							</span>
							<span class="item-title citation-formatted"><?php echo $citeText; ?></span>
						</li>
				<?php	$i++; } ?>
					</ul>
				<?php  }  ?>
					<div class="item-new">
						<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADD_CITATION'); ?></a></span>
					</div>
				</div>
		</div>
	</div>
</div>
