<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
$fas = $recommended->loadFocusAreas();
$focusareas = array();
foreach ($fas as $tag => $fa)
{
	if (!isset($focusareas[$fa['label']]))
	{
		$focusareas[$fa['label']] = array();
	}
	$focusareas[$fa['label']][$tag] = $fa;
}
// Now sort by ordering
function custom_sort($a,$b) {
	return (int)$a['ordering']>(int)$b['ordering'];
}
foreach ($focusareas as $key => $fas)
{
	usort($focusareas[$key], "custom_sort");
}

$this->css('tags.css')
     ->js('tags.js');
?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete';
	echo $curatorStatus->status == 1 ? ' el-passed' : '';
	echo $curatorStatus->status == 0 ? ' el-failed' : '';
	echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : '';
	?>">
	<div class="element_editing">
		<div class="pane-wrapper">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl">
				<?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
				<?php } else { ?><span class="optional"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?></span><?php } ?>
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_TAGS')); ?>
			</label>
		  <?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>

			<fieldset class="focus-areas">
				<?php
					if (count($focusareas) > 0):
						$fa_existing = $recommended->get_existing_focus_areas_map();
						$fa_props = $recommended->get_focus_area_properties();

						echo 'Choose from these recommended tags:';
						$idx = 0;
						foreach ($focusareas as $label=>$fas):
				?>
							<fieldset value="<?php echo ($fa_props[$label]['mandatory_depth'] ? $fa_props[$label]['mandatory_depth'] : 0) ?>">
								<legend>
									<span class="tooltips" title='<?php echo $fa_props[$label]['about']; ?>'><?php echo ($fa_props[$label]['label'] ? $fa_props[$label]['label'] : $label) ?></span>
									<?php echo ($fa_props[$label]['mandatory_depth'] ? '<span class="required">required</span>' : '<span class="optional">optional</span>'); ?>
								</legend>
								<?php $recommended->fa_controls(++$idx, $fas, $fa_props, $fa_existing); ?>
							</fieldset>
						<?php
						endforeach;
					endif;
				?>
			</fieldset>
			<?php
			$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $recommended->get_existing_tags_value_list())) );

			echo 'Enter your own tags below:';
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
<?php }
