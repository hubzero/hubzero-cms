<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('story');

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($this->type)) . ': ' . $text, 'addedit');
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
				<input type="text" name="story[readmore_title]" value="<?php echo $this->escape($this->story->readmore_title); ?>" style="width:30%;margin-right:2%" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_TITLE_PLACEHOLDER'); ?>" />
				<input type="text" name="story[readmore_link]" value="<?php echo $this->escape($this->story->readmore_link); ?>" style="width:67.5%" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_PLACEHOLDER'); ?>" />
			</div>
		</fieldset>
	</fielset>

	<input type="hidden" name="story[id]" value="<?php echo $this->story->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
