<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Preference;
use Components\Story\Helpers\Thread;

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
			<p class="hint"><?php echo Lang::txt('COM_STORY_PREFERENCES_SCORING_INTRO'); ?></p>

			<div class="grid">
				<div class="col span4">
					<label for="field-threshold"><?php echo Lang::txt('COM_STORY_FIELD_THRESHOLD'); ?>
						<input type="number" step="0.5" name="fields[threshold]" id="field-threshold" value="<?php echo $p->get('threshold'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-highlight-threshold"><?php echo Lang::txt('COM_STORY_FIELD_HIGHLIGHT_THRESHOLD'); ?>
						<input type="number" step="0.5" name="fields[highlight_threshold]" id="field-highlight-threshold" value="<?php echo $p->get('highlight_threshold'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-hide-scores" class="checkbox">
						<input type="hidden" name="fields[hide_scores]" value="0" />
						<input type="checkbox" name="fields[hide_scores]" id="field-hide-scores" value="1"<?php echo $p->get('hide_scores') ? ' checked="checked"' : ''; ?> />
						<?php echo Lang::txt('COM_STORY_FIELD_HIDE_SCORES'); ?>
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span4">
					<label for="field-comment-limit"><?php echo Lang::txt('COM_STORY_FIELD_COMMENT_LIMIT'); ?>
						<input type="number" step="1" name="fields[comment_limit]" id="field-comment-limit" value="<?php echo $p->get('comment_limit'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-comment-spill"><?php echo Lang::txt('COM_STORY_FIELD_COMMENT_SPILL'); ?>
						<input type="number" step="1" name="fields[comment_spill]" id="field-comment-spill" value="<?php echo $p->get('comment_spill'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-max-comment-size"><?php echo Lang::txt('COM_STORY_FIELD_MAX_COMMENT_SIZE'); ?>
						<input type="number" step="1" name="fields[max_comment_size]" id="field-max-comment-size" value="<?php echo $p->get('max_comment_size'); ?>" />
					</label>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php echo Lang::txt('COM_STORY_PREFERENCES_MODIFIERS'); ?></legend>
			<p class="hint"><?php echo Lang::txt('COM_STORY_PREFERENCES_MODIFIERS_INTRO'); ?></p>

			<div class="grid">
				<div class="col span4">
					<label for="field-bonus-long"><?php echo Lang::txt('COM_STORY_FIELD_BONUS_LONG'); ?>
						<input type="number" step="0.5" name="fields[bonus_long]" id="field-bonus-long" value="<?php echo Thread::number($p->get('bonus_long'), false); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-length-long"><?php echo Lang::txt('COM_STORY_FIELD_LENGTH_LONG'); ?>
						<input type="number" step="1" name="fields[length_long]" id="field-length-long" value="<?php echo $p->get('length_long'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-bonus-short"><?php echo Lang::txt('COM_STORY_FIELD_BONUS_SHORT'); ?>
						<input type="number" step="0.5" name="fields[bonus_short]" id="field-bonus-short" value="<?php echo Thread::number($p->get('bonus_short'), false); ?>" />
					</label>
				</div>
			</div>
			<div class="grid">
				<div class="col span4">
					<label for="field-length-short"><?php echo Lang::txt('COM_STORY_FIELD_LENGTH_SHORT'); ?>
						<input type="number" step="1" name="fields[length_short]" id="field-length-short" value="<?php echo $p->get('length_short'); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-bonus-anonymous"><?php echo Lang::txt('COM_STORY_FIELD_BONUS_ANONYMOUS'); ?>
						<input type="number" step="0.5" name="fields[bonus_anonymous]" id="field-bonus-anonymous" value="<?php echo Thread::number($p->get('bonus_anonymous'), false); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="field-bonus-new-user"><?php echo Lang::txt('COM_STORY_FIELD_BONUS_NEW_USER'); ?>
						<input type="number" step="0.5" name="fields[bonus_new_user]" id="field-bonus-new-user" value="<?php echo Thread::number($p->get('bonus_new_user'), false); ?>" />
					</label>
				</div>
			</div>
			<div class="grid">
				<div class="col span4">
					<label for="field-bonus-karma"><?php echo Lang::txt('COM_STORY_FIELD_BONUS_KARMA'); ?>
						<input type="number" step="0.5" name="fields[bonus_karma]" id="field-bonus-karma" value="<?php echo Thread::number($p->get('bonus_karma'), false); ?>" />
					</label>
				</div>
				<div class="col span4">
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
