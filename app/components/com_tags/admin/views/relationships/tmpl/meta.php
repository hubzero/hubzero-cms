<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_FOCUS_AREAS'), 'tags.png');
//Toolbar::cancel();
//Toolbar::custom('index', 'edit', ' ', 'Tag Relationships', false);
Toolbar::apply('updatefocusareas');
Toolbar::spacer();
Toolbar::help('focusareas');

Html::behavior('framework');

$this->css('tag_graph.css');
$this->js('d3.js', 'system')
	->js('tag_graph.js');

$dbh = App::get('db');
$dbh->setQuery(
	'SELECT *, (SELECT group_concat(resource_type_id) FROM `#__focus_area_resource_type_rel` WHERE focus_area_id = fa.id) AS rtypes, (SELECT group_concat(master_type_id) FROM `#__focus_area_publication_master_type_rel` WHERE focus_area_id = fa.id) AS ptypes FROM `#__tags` t INNER JOIN `#__focus_areas` fa ON fa.tag_id = t.id ORDER BY raw_tag'
);
$fas = $dbh->loadAssocList();
$dbh->setQuery(
	'SELECT DISTINCT id, type FROM `#__resource_types` WHERE category = (SELECT id FROM `#__resource_types` WHERE type = \'Main Types\') AND contributable ORDER BY type'
);
$rtypes = $dbh->loadAssocList('id');	// Resource types
$dbh->setQuery(
	'SELECT DISTINCT id, type FROM `#__publication_master_types` WHERE contributable ORDER BY type'
);
$ptypes = $dbh->loadAssocList('id');	// Publication master types
?>
<script type="application/json" id="resource-types">
	{
		"rtypes": <?php echo json_encode(array_values($rtypes)); ?>
	}
</script>
<script type="application/json" id="publication-types">
	{
		"ptypes": <?php echo json_encode(array_values($ptypes)); ?>
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span8">
			<div id="fas">
			<?php
			foreach ($fas as $i => $fa):
				$rtype_ids = array_flip(explode(',', $fa['rtypes']));
				$ptype_ids = array_flip(explode(',', $fa['ptypes']));
			?>
				<fieldset class="adminform" id="group-<?php echo $i; //echo $fa['id']; ?>">
					<legend><span><?php echo Lang::txt('COM_TAGS_GROUP'); ?></span></legend>

					<div class="input-wrap">
						<label for="name-<?php echo $fa['id']; ?>"><?php echo Lang::txt('COM_TAGS_GROUP_NAME'); ?>:</label>
						<input type="text" name="name-<?php echo $fa['id']; ?>" id="name-<?php echo $fa['id']; ?>" value="<?php echo str_replace('"', '&quot;', $fa['raw_tag']); ?>" />
					</div>
					<div class="input-wrap">
						<label for="label-<?php echo $fa['id']; ?>"><?php echo Lang::txt('COM_TAGS_GROUP_LABEL'); ?>:</label>
						<input type="text" name="label-<?php echo $fa['id']; ?>" id="label-<?php echo $fa['id']; ?>" value="<?php echo str_replace('"', '&quot;', $fa['label']); ?>" />
					</div>
					<div class="input-wrap">
						<label for="about-<?php echo $fa['id']; ?>"><?php echo Lang::txt('COM_TAGS_GROUP_ABOUT'); ?>:</label>
						<?php echo $this->editor('about[' . $fa['id'] . ']', $this->escape($fa['about']), 50, 5, 'about-' . $fa['id'], array('class' => 'minimal no-footer', 'buttons' => false)); ?>
					</div>

					<!-- Resources -->
					<fieldset>
						<legend><?php echo Lang::txt('COM_TAGS_GROUP_RESOURCE_TYPES'); ?>:</legend>

						<div class="input-wrap">
							<select id="rtypes-<?php echo $fa['id']; ?>" name="rtypes-<?php echo $fa['id']; ?>[]" multiple="multiple" size="<?php echo count($rtypes); ?>">
								<?php foreach ($rtypes as $type): ?>
									<option value="<?php echo $type['id']; ?>" <?php if (isset($rtype_ids[$type['id']])) echo 'selected="selected" '; ?>><?php echo $type['type']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</fieldset>

					<!-- Publications -->
					<fieldset>
						<legend><?php echo Lang::txt('COM_TAGS_GROUP_PUBLICATION_TYPES'); ?>:</legend>
						<div class="input-wrap">
							<select id="ptypes-<?php echo $fa['id']; ?>" name="ptypes-<?php echo $fa['id']; ?>[]" multiple="multiple" size="<?php echo count($ptypes); ?>">
								<?php foreach ($ptypes as $type): ?>
									<option value="<?php echo $type['id']; ?>" <?php if (isset($ptype_ids[$type['id']])) { echo 'selected="selected" '; } ?>><?php echo $type['type']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</fieldset>

					<!-- Requirement -->
					<fieldset>
						<legend><?php echo Lang::txt('COM_TAGS_GROUP_REQUIREMENT'); ?>:</legend>
						<div class="input-wrap">
							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="optional" <?php if (is_null($fa['mandatory_depth'])) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_OPTIONAL'); ?></label><br />
							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="mandatory" <?php if (!is_null($fa['mandatory_depth']) && $fa['mandatory_depth'] < 2) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_MANDATORY'); ?></label><br />
							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="depth" <?php if ($fa['mandatory_depth'] > 1) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_MANDATORY'); ?></label> <label><?php echo Lang::txt('COM_TAGS_GROUP_UNTIL_DEPTH'); ?>:</label><br />
							<input type="text" class="option" name="mandatory-depth-<?php echo $fa['id']; ?>" value="<?php if ($fa['mandatory_depth'] > 1) { echo $fa['mandatory_depth']; } ?>" />
						</div>
					</fieldset>

					<!-- Selection type -->
					<fieldset>
						<legend><?php echo Lang::txt('COM_TAGS_GROUP_SELECTION_TYPE'); ?>:</legend>
						<div class="input-wrap">
							<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="multiple" <?php if (!is_null($fa['multiple_depth']) && $fa['multiple_depth'] < 2) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_GROUP_MULTI_SELECT'); ?></label><br />
							<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="single" <?php if (is_null($fa['multiple_depth'])) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_GROUP_SINGLE_SELECT_RADIO'); ?> </label><br />
							<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="depth" <?php if ($fa['multiple_depth'] > 1) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_GROUP_SINGLE_SELECT'); ?></label> <label><?php echo Lang::txt('COM_TAGS_GROUP_UNTIL_DEPTH'); ?>: </label><br />

							<input type="text" name="multiple-depth-<?php echo $fa['id']; ?>" value="<?php if ($fa['multiple_depth'] > 1) { echo $fa['multiple_depth']; } ?>" />
						</div>
					</fieldset>

					<div class="input-wrap">
						<button class="delete-group" id="delete-<?php echo $i; //$fa['id']; ?>" rel="group-<?php echo $i; //$fa['id']; ?>"><?php echo Lang::txt('COM_TAGS_DELETE_GROUP'); ?></button>
					</div>
				</fieldset>
			<?php
			endforeach;
			$fill_new = !isset($added_new_focus_area);
			$type_ids = $fill_new && isset($_POST['types-new']) ? array_flip($_POST['types-new']) : array();
			?>
			</div><!-- / #fas -->
			<p>
				<button id="add_group"><?php echo Lang::txt('COM_TAGS_ADD_GROUP'); ?></button>
			</p>
			<p>
				<input type="hidden" value="<?php echo $this->option; ?>" name="option" />
				<input type="hidden" value="<?php echo $this->controller; ?>" name="controller" />
				<input type="hidden" name="task" value="updatefocusareas" />
			</p>
		</div>
		<div class="col span4">
			<?php echo Lang::txt('COM_TAGS_GROUP_EXPLANATION'); ?>
		</div>
	</div>
</form>
