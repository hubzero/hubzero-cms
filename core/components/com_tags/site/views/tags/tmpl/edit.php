<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-tag tag btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>

<section class="main section">
	<div class="section-inner">
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p><?php echo Lang::txt('COM_TAGS_NORMALIZED_TAG_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_TAGS_DETAILS'); ?></legend>

				<label for="field-raw_tag">
					<?php echo Lang::txt('COM_TAGS_FIELD_TAG'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input type="text" name="fields[raw_tag]" id="field-raw_tag" data-error="<?php echo Lang::txt('COM_TAGS_FIELD_TAG_BLANK'); ?>" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" size="38" />
				</label>

				<label for="field-admin">
					<input class="option" type="checkbox" name="fields[admin]" id="field-admin" value="1" />
					<strong><?php echo Lang::txt('COM_TAGS_FIELD_ADMINISTRATION'); ?></strong>
					<span class="hint">(<?php echo Lang::txt('COM_TAGS_FIELD_ADMINISTRATION_EXPLANATION'); ?>)</span>
				</label>

				<label for="field-description">
					<?php echo Lang::txt('COM_TAGS_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" rows="7" cols="35"><?php echo $this->escape(stripslashes($this->tag->get('description'))); ?></textarea>
				</label>

				<label for="field-substitutions">
					<?php echo Lang::txt('COM_TAGS_FIELD_ALIAS'); ?>
					<textarea name="fields[substitutions]" id="field-substitutions" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->tag->substitutes)); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_ALIAS_HINT'); ?></span>
				</label>

				<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->get('tag'); ?>" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<?php echo Html::input('token'); ?>

				<input type="hidden" name="limit" value="<?php echo $this->escape($this->filters['limit']); ?>" />
				<input type="hidden" name="limitstart" value="<?php echo $this->escape($this->filters['start']); ?>" />
				<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
				<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
				<input type="hidden" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_TAGS_SUBMIT'); ?>" />
				<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>
		</form>
	</div>
</section><!-- / .main section -->
