<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
	'SELECT *, (SELECT group_concat(resource_type_id) FROM `#__focus_area_resource_type_rel` WHERE focus_area_id = fa.id) AS types
	FROM `#__tags` t
	INNER JOIN `#__focus_areas` fa ON fa.tag_id = t.id
	ORDER BY raw_tag'
);
$fas = $dbh->loadAssocList();
$dbh->setQuery(
	'SELECT DISTINCT id, type FROM `#__resource_types` WHERE category = (SELECT id FROM `#__resource_types` WHERE type = \'Main Types\') AND contributable ORDER BY type'
);
$types = $dbh->loadAssocList('id');
?>
<script type="application/json" id="resource-types">
	{
		"types": <?php echo json_encode(array_values($types)); ?>
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span8">
			<div id="fas">
			<?php
			foreach ($fas as $i => $fa):
				$type_ids = array_flip(explode(',', $fa['types']));
			?>
				<fieldset class="adminform" id="group-<?php echo $i; //echo $fa['id']; ?>">
					<legend><span><?php echo Lang::txt('COM_TAGS_GROUP'); ?></span></legend>

					<div class="input-wrap">
						<label for="name-<?php echo $fa['id']; ?>"><?php echo Lang::txt('COM_TAGS_GROUP_NAME'); ?>:</label>
						<input type="text" name="name-<?php echo $fa['id']; ?>" id="name-<?php echo $fa['id']; ?>" value="<?php echo str_replace('"', '&quot;', $fa['raw_tag']); ?>" />
					</div>

					<fieldset>
						<legend><?php echo Lang::txt('COM_TAGS_GROUP_RESOURCE_TYPES'); ?>:</legend>

						<div class="input-wrap">
							<select id="types-<?php echo $fa['id']; ?>" name="types-<?php echo $fa['id']; ?>[]" multiple="multiple" size="<?php echo count($types); ?>">
								<?php foreach ($types as $type): ?>
									<option value="<?php echo $type['id']; ?>" <?php if (isset($type_ids[$type['id']])) { echo 'selected="selected" '; } ?>><?php echo $type['type']; ?></option>
								<?php endforeach; ?>
							</select>

							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="optional" <?php if (is_null($fa['mandatory_depth'])) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_OPTIONAL'); ?></label><br />
							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="mandatory" <?php if (!is_null($fa['mandatory_depth']) && $fa['mandatory_depth'] < 2) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_MANDATORY'); ?></label><br />
							<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="depth" <?php if ($fa['mandatory_depth'] > 1) { echo 'checked="checked" '; } ?>/> <?php echo Lang::txt('COM_TAGS_MANDATORY'); ?></label> <label><?php echo Lang::txt('COM_TAGS_GROUP_UNTIL_DEPTH'); ?>:</label><br />
							<input type="text" class="option" name="mandatory-depth-<?php echo $fa['id']; ?>" value="<?php if ($fa['mandatory_depth'] > 1) { echo $fa['mandatory_depth']; } ?>" />
						</div>
					</fieldset>

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
