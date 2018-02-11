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

include_once \Component::path('com_publications') . DS . 'helpers' . DS . 'recommendedTags.php';

// Get focus areas - this may be redundant: check $recommended->get_focus_area_properties();
$db = \App::get('db');
$recommended = new \Components\Publications\Helpers\RecommendedTags($this->pub->id, 0, $db = $db);
$db->setQuery('SELECT master_type FROM `#__publications` WHERE id = ' . $db->quote($this->pub->id));
$fas = $recommended->loadFocusAreas($db->loadResult());
$focusareas = array();
foreach ($fas as $tag => $fa)
{
	if (!isset($focusareas[$fa['label']]))
	{
		$focusareas[$fa['label']] = array();
	}
	$focusareas[$fa['label']][$tag] = $fa;
}

$this->css('tags.css')
     ->js('tags.js');
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

					<fieldset>
						<?php
							if (count($focusareas) > 0):
								$fa_existing = $recommended->get_existing_focus_areas_map();
								$fa_props = $recommended->get_focus_area_properties();

								$idx = 0;
								foreach ($focusareas as $label=>$fas):
								?>
									<fieldset value="<?php echo ($fa_props[$label]['mandatory_depth'] ? $fa_props[$label]['mandatory_depth'] : 0) ?>">
										<legend><?php echo 'Select from '.$label.' ontology: '.($fa_props[$label]['mandatory_depth'] ? '<span class="required">required to depth of ' . $fa_props[$label]['mandatory_depth'] . '</span>' : ''); ?></legend>
										<?php $recommended->fa_controls(++$idx, $fas, $fa_props, $fa_existing); ?>
									</fieldset>
								<?php
								endforeach;
							endif;
						?>
					</fieldset>
						<?php
						$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $recommended->get_existing_tags_value_list())) );

						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<textarea name="tags" id="tags" rows="6" cols="35">' . $recommended->get_existing_tags_value_list() . '</textarea>' . "\n";
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
