<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('story');

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($this->type)) . ': ' . $text, 'newsletter');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($this->type)); ?></legend>

		<div class="input-wrap">
			<label for="field-nid"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER'); ?>:</label>
			<strong class="pseudo-input"><?php echo $this->escape($this->newsletter->name); ?></strong>
			<input type="hidden" name="story[nid]" id="field-nid" value="<?php echo $this->newsletter->id; ?>" />
			<input type="hidden" name="nid" id="nid" value="<?php echo $this->newsletter->id; ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-type"><?php echo Lang::txt('COM_NEWSLETTER_STORY_TYPE'); ?>:</label>
			<span class="pseudo-input"><?php echo Lang::txt('COM_NEWSLETTER_STORY_' . ucfirst($this->type)); ?></span>
			<input type="hidden" name="type" id="field-type" value="<?php echo $this->escape(strtolower($this->type)); ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-title"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE'); ?>:</label>
			<input type="text" name="story[title]" id="field-title" value="<?php echo $this->escape($this->story->title); ?>" />
		</div>
		<?php if ($this->story->id) : ?>
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER_HINT'); ?>">
				<label for="field-order"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER'); ?>:</label>
				<input type="text" name="story[order]" id="field-order" readonly value="<?php echo $this->story->order; ?>" />
				<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER_HINT'); ?></span>
			</div>
		<?php endif; ?>
		<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT1'); ?>">
			<label for="field-story"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY'); ?>:</label>
			<?php echo $this->editor("story[story]", $this->escape($this->story->story), 50, 10, 'field-story', array('full_paths' => true)); ?>
			<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT1'); ?></span>
			<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT2'); ?></span>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE'); ?></legend>
			<div class="input-wrap">
				<div class="grid">
					<div class="col span4">
						<input type="text" name="story[readmore_title]" value="<?php echo $this->escape($this->story->readmore_title); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_TITLE_PLACEHOLDER'); ?>" />
					</div>
					<div class="col span8">
						<input type="text" name="story[readmore_link]" value="<?php echo $this->escape($this->story->readmore_link); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_PLACEHOLDER'); ?>" />
					</div>
				</div>
			</div>
		</fieldset>
	</fielset>

	<input type="hidden" name="story[id]" value="<?php echo $this->story->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
