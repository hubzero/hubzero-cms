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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', 1);

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($this->type)) . ': ' . $text, 'addedit.png');
Toolbar::save();
Toolbar::cancel();

$this->css();
$this->js('autogen-story.js');
?>


<?php if (count($this->enabledSources) > 0): ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="autogen-form" data-formwatcher-message="You are now leaving this page to add stories and your current changes have not been saved. Click &quot;Stay on Page&quot; and then save the newsletter first before proceeding to add stories.">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo Lang::txt('COM_NEWSLETTER_STORY_SETTINGS'); ?>:</legend>

			<div class="input-wrap">
				<label for="newsletter-contentSource required"><?php echo Lang::txt('COM_NEWSLETTER_STORY_SOURCE'); ?>:</label>
				<select id="contentSource" name="contentSource">
					<option value='none'><?php echo Lang::txt('MAKE_A_SELECTION'); ?></option>
				<?php foreach ($this->enabledSources as $source): ?>
					<option value="<?php echo $source; ?>"><?php echo $source; ?></option>
				<?php endforeach; ?>
				</select>
			</div>

			<div class="input-wrap">
				<label for="story-title"><?php echo Lang::txt('COM_NEWSLETTER_STORY_TITLE'); ?>:</label>
				<input type="text" name="title" id="story-title">
			</div>

			<div class="input-wrap">
				<label for="story-itemCount"><?php echo Lang::txt('COM_NEWSLETTER_STORY_ITEM_COUNT'); ?>:</label>
				<input type="text" name="itemCount" id="itemCount" value="5">
			</div>

			<div class="input-wrap">
				<label for="storyLayout"><?php echo Lang::txt('COM_NEWSLETTER_STORY_LAYOUT_TEMPLATE'); ?>:</label>
				<select id="storyLayout" name="layout">
				<?php foreach ($this->layouts as $layout): ?>
					<option value="<?php echo $layout; ?>"><?php echo $layout; ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</fieldset>

		<input type="hidden" name="story" value="" />
		<input type="hidden" name="nid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="type" value="autogen" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</div>

	<div class="col width-50 fltlft">
	<fieldset class="adminform">
	<legend>Story Preview</legend>
	<div id="previewArea">
		<span id="previewStoryTitle"></span>
		<span id="previewContentArea"></span>
	</div>
	</div>
	</fieldset>

</form>

<?php else: ?>
	<div class="warning">
		<?php echo Lang::txt('There are no enabled Newsletter Source Plugins'); ?>
	</div>
<?php endif; ?>
