<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Preference;

$p = $this->preference;

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_STORY_PREFERENCES'); ?></h2>
	<p class="story-preferences-intro"><?php echo Lang::txt('COM_STORY_PREFERENCES_INTRO'); ?></p>
</header>

<section class="main section">

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&view=comments&task=preferences'); ?>" method="post" id="preferences-form">

		<fieldset>
			<legend><?php echo Lang::txt('COM_STORY_PREFERENCES_READING'); ?></legend>

			<div class="grid">
				<div class="col span6">
					<label for="field-mode"><?php echo Lang::txt('COM_STORY_FIELD_MODE'); ?>
						<select name="fields[mode]" id="field-mode">
<?php foreach (Preference::modes() as $mode) { ?>
							<option value="<?php echo $mode; ?>"<?php echo ($p->get('mode') == $mode) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_STORY_MODE_' . strtoupper($mode)); ?></option>
<?php } ?>
						</select>
					</label>
				</div>
				<div class="col span6">
					<label for="field-sort"><?php echo Lang::txt('COM_STORY_FIELD_SORT'); ?>
						<select name="fields[sort]" id="field-sort">
							<option value="oldest"<?php echo ($p->get('sort') == 'oldest') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_STORY_SORT_OLDEST'); ?></option>
							<option value="newest"<?php echo ($p->get('sort') == 'newest') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_STORY_SORT_NEWEST'); ?></option>
						</select>
						<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_SORT_HINT'); ?></span>
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="field-max-comment-size"><?php echo Lang::txt('COM_STORY_FIELD_MAX_SIZE'); ?>
						<input type="number" name="fields[max_comment_size]" id="field-max-comment-size" value="<?php echo (int) $p->get('max_comment_size'); ?>" />
					</label>
				</div>
				<div class="col span6">
					<label for="field-hide-signatures" class="checkbox">
						<input type="hidden" name="fields[hide_signatures]" value="0" />
						<input type="checkbox" name="fields[hide_signatures]" id="field-hide-signatures" value="1"<?php echo $p->get('hide_signatures') ? ' checked="checked"' : ''; ?> />
						<?php echo Lang::txt('COM_STORY_FIELD_HIDE_SIGNATURES'); ?>
					</label>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php echo Lang::txt('COM_STORY_PREFERENCES_SCORING'); ?></legend>
			<p class="info"><?php echo Lang::txt('COM_STORY_PREFERENCES_SCORING_LATER'); ?></p>

			<div class="grid">
				<div class="col span6">
					<label for="field-hide-scores" class="checkbox">
						<input type="hidden" name="fields[hide_scores]" value="0" />
						<input type="checkbox" name="fields[hide_scores]" id="field-hide-scores" value="1"<?php echo $p->get('hide_scores') ? ' checked="checked"' : ''; ?> />
						<?php echo Lang::txt('COM_STORY_FIELD_HIDE_SCORES'); ?>
					</label>
				</div>
				<div class="col span6">
					<label for="field-willing" class="checkbox">
						<input type="hidden" name="fields[willing_to_moderate]" value="0" />
						<input type="checkbox" name="fields[willing_to_moderate]" id="field-willing" value="1"<?php echo $p->get('willing_to_moderate') ? ' checked="checked"' : ''; ?> />
						<?php echo Lang::txt('COM_STORY_FIELD_WILLING'); ?>
					</label>
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_WILLING_HINT'); ?></span>
				</div>
			</div>
		</fieldset>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_STORY_SAVE_PREFERENCES'); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="view" value="comments" />
		<input type="hidden" name="task" value="preferences" />
		<?php echo Html::input('token'); ?>
	</form>

</section>
