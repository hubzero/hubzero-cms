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

$elName = "tagsPick";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?>">
			<div class="element_editing">
				<div class="pane-wrapper">
					<span class="checker">&nbsp;</span>
					<label id="<?php echo $elName; ?>-lbl">
						<?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
						<?php } else { ?><span class="optional"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?></span><?php } ?>
						<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_TAGS')); ?>
					</label>
						<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>

						<?php
						$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->pub->getTagsForEditing())) );

						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<textarea name="tags" id="tags" rows="6" cols="35">' . $this->pub->getTagsForEditing() . '</textarea>' . "\n";
						}
						?>
				</div>
			</div>
</div>
<?php if ($this->categories && count($this->categories) > 1) { ?>
<div class="blockelement el-optional el-complete">
	<div class="element_editing">
		<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<label><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_SEARCH_CATEGORY'); ?></label>
		<?php foreach ($this->categories as $cat)
		{
			$params = new \Hubzero\Config\Registry($cat->params);

			// Skip inaplicable category
			if (!$params->get('type_' . $this->pub->base, 1))
			{
				continue;
			}
			?>
				<div class="pubtype-block">
					<input type="radio" name="pubtype" value="<?php echo $cat->id; ?>" <?php if ($this->pub->category == $cat->id) { echo 'checked="checked"'; } ?> class="radio" />
					<?php echo $cat->name; ?>
					<span><?php echo $cat->description; ?></span>
				</div>
		<?php } ?>
		</div>
	</div>
</div>
<?php } ?>