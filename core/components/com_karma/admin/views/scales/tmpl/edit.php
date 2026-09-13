<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Karma\Scale;
use Hubzero\Karma\Bands;

Toolbar::title(Lang::txt($this->row->isNew() ? 'COM_KARMA_SCALE_NEW' : 'COM_KARMA_SCALE_EDIT'), 'karma.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();

$selfOptions = array(
	Scale::SELF_EXACT     => Lang::txt('COM_KARMA_VISIBILITY_SELF_EXACT'),
	Scale::SELF_ADJECTIVE => Lang::txt('COM_KARMA_VISIBILITY_SELF_ADJECTIVE')
);

$publicOptions = array(
	Scale::PUBLIC_HIDDEN    => Lang::txt('COM_KARMA_VISIBILITY_PUBLIC_HIDDEN'),
	Scale::PUBLIC_OPT_IN    => Lang::txt('COM_KARMA_VISIBILITY_PUBLIC_OPT_IN'),
	Scale::PUBLIC_ADJECTIVE => Lang::txt('COM_KARMA_VISIBILITY_PUBLIC_ADJECTIVE'),
	Scale::PUBLIC_EXACT     => Lang::txt('COM_KARMA_VISIBILITY_PUBLIC_EXACT')
);

// Sample values to show the bands and the visibility settings working. The
// band boundaries themselves are the interesting points, plus the ends of
// the scale.
$samples = array((float) $this->row->get('floor'), 0.0, (float) $this->row->get('ceiling'));

foreach (array_keys(Bands::parse($this->row->get('adjectives'))) as $threshold)
{
	$samples[] = (float) $threshold;
}

$samples = array_unique($samples);
sort($samples);

$floor   = (float) $this->row->get('floor');
$ceiling = (float) $this->row->get('ceiling');
$inRange = array();

foreach ($samples as $value)
{
	if ($value >= $floor && $value <= $ceiling)
	{
		$inRange[] = $value;
	}
}

$samples = $inRange;

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=scales'); ?>" method="post" name="adminForm" id="item-form" class="main section">

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_DETAILS'); ?></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_KARMA_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" size="40" value="<?php echo $this->escape($this->row->get('title')); ?>" required />
				</div>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_KARMA_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" size="40" value="<?php echo $this->escape($this->row->get('alias')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_KARMA_FIELD_DESCRIPTION'); ?>:</label>
					<textarea name="fields[description]" id="field-description" rows="4" cols="50"><?php echo $this->escape($this->row->get('description')); ?></textarea>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_BOUNDS'); ?></legend>

				<div class="input-wrap">
					<label for="field-floor"><?php echo Lang::txt('COM_KARMA_FIELD_FLOOR'); ?>:</label>
					<input type="number" name="fields[floor]" id="field-floor" value="<?php echo $this->escape($this->row->get('floor')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-ceiling"><?php echo Lang::txt('COM_KARMA_FIELD_CEILING'); ?>:</label>
					<input type="number" name="fields[ceiling]" id="field-ceiling" value="<?php echo $this->escape($this->row->get('ceiling')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-initial"><?php echo Lang::txt('COM_KARMA_FIELD_INITIAL'); ?>:</label>
					<input type="number" name="fields[initial]" id="field-initial" value="<?php echo $this->escape($this->row->get('initial')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_INITIAL_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_DECAY'); ?></legend>

				<div class="input-wrap">
					<label for="field-decay_per_day"><?php echo Lang::txt('COM_KARMA_FIELD_DECAY_PER_DAY'); ?>:</label>
					<input type="text" name="fields[decay_per_day]" id="field-decay_per_day" value="<?php echo $this->escape($this->row->get('decay_per_day', 0)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_DECAY_PER_DAY_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-decay_after_days"><?php echo Lang::txt('COM_KARMA_FIELD_DECAY_AFTER'); ?>:</label>
					<input type="number" name="fields[decay_after_days]" id="field-decay_after_days" value="<?php echo $this->escape($this->row->get('decay_after_days', 0)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-decay_toward"><?php echo Lang::txt('COM_KARMA_FIELD_DECAY_TOWARD'); ?>:</label>
					<input type="number" name="fields[decay_toward]" id="field-decay_toward" value="<?php echo $this->escape($this->row->get('decay_toward', 0)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_DECAY_TOWARD_HINT'); ?></span>
				</div>
			</fieldset>
		</div>

		<div class="col span5 omega">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_VISIBILITY'); ?></legend>

				<div class="input-wrap">
					<label for="field-visibility_self"><?php echo Lang::txt('COM_KARMA_FIELD_VISIBILITY_SELF'); ?>:</label>
					<select name="fields[visibility_self]" id="field-visibility_self">
<?php foreach ($selfOptions as $value => $label) { ?>
						<option value="<?php echo $this->escape($value); ?>"<?php if ($this->row->get('visibility_self') == $value) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-visibility_public"><?php echo Lang::txt('COM_KARMA_FIELD_VISIBILITY_PUBLIC'); ?>:</label>
					<select name="fields[visibility_public]" id="field-visibility_public">
<?php foreach ($publicOptions as $value => $label) { ?>
						<option value="<?php echo $this->escape($value); ?>"<?php if ($this->row->get('visibility_public') == $value) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
<?php } ?>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_VISIBILITY_PUBLIC_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-adjectives"><?php echo Lang::txt('COM_KARMA_FIELD_ADJECTIVES'); ?>:</label>
					<input type="text" name="fields[adjectives]" id="field-adjectives" size="50" value="<?php echo $this->escape($this->row->get('adjectives')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_ADJECTIVES_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_PREVIEW'); ?></legend>

				<p class="hint"><?php echo Lang::txt('COM_KARMA_SCALE_PREVIEW_INTRO'); ?></p>

				<table class="adminlist">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_KARMA'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_SELF'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_OTHERS'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_ADMIN'); ?></th>
						</tr>
					</thead>
					<tbody>
<?php
foreach ($samples as $value)
{
	$adjective = $this->row->adjective($value);

	$self = ($this->row->get('visibility_self') == Scale::SELF_EXACT) ? $value : $adjective;

	switch ($this->row->get('visibility_public'))
	{
		case Scale::PUBLIC_EXACT:
			$others = (string) $value;
		break;
		case Scale::PUBLIC_ADJECTIVE:
			$others = $adjective;
		break;
		case Scale::PUBLIC_OPT_IN:
			$others = Lang::txt('COM_KARMA_PREVIEW_OPT_IN', $adjective);
		break;
		default:
			$others = Lang::txt('COM_KARMA_PREVIEW_NOTHING');
		break;
	}
	?>
						<tr>
							<td><?php echo $this->escape($value); ?></td>
							<td><?php echo $this->escape($self); ?></td>
							<td><?php echo $this->escape($others); ?></td>
							<td><?php echo $this->escape($value); ?></td>
						</tr>
	<?php
}
?>
					</tbody>
				</table>

				<p class="hint"><?php echo Lang::txt('COM_KARMA_SCALE_PREVIEW_STANDING'); ?></p>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_PUBLISHING'); ?></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_KARMA_FIELD_STATE'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="1"<?php if ($this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="0"<?php if (!$this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-ordering"><?php echo Lang::txt('COM_KARMA_FIELD_ORDERING'); ?>:</label>
					<input type="number" name="fields[ordering]" id="field-ordering" value="<?php echo $this->escape($this->row->get('ordering', 0)); ?>" />
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="scales" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
