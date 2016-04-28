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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//tag editor
$task = 'post/' . $this->post_id . '/collect';
if ($this->collection_id)
{
	$task = Request::getVar('board', 0) . '/collect';
}

$this->css();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo Route::url($this->member->link() . '&active=' . $this->name . '&task=' . $task); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('Collect'); ?></legend>

		<div class="grid">
			<div class="col span5">
				<label for="field-collection_id">
					<?php echo Lang::txt('Select collection'); ?>
					<select name="collection_id" id="field-collection_id">
						<option value="0"><?php echo Lang::txt('Select ...'); ?></option>
						<optgroup label="<?php echo Lang::txt('My collections'); ?>">
						<?php
						if ($this->myboards)
						{
							foreach ($this->myboards as $board)
							{
								if ($board->id == $this->collection_id)
								{
									continue;
								}
								?>
								<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
								<?php
							}
						}
						?>
						</optgroup>
						<?php
						if ($this->groupboards)
						{
							foreach ($this->groupboards as $optgroup => $boards)
							{
								?>
								<optgroup label="<?php echo $this->escape(stripslashes($optgroup)); ?>">
									<?php
									foreach ($boards as $board)
									{
										?>
										<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
										<?php
									}
									?>
								</optgroup>
								<?php
							}
						}
						?>
					</select>
				</label>
			</div>
			<div class="col span2">
				<p class="or">OR</p>
			</div>
			<div class="col span5 omega">
				<label for="field-collection_title">
					<?php echo Lang::txt('Create collection'); ?>
					<input type="text" name="collection_title" id="field-collection_title" />
				</label>
			</div>
		</div>

		<label for="field_description">
			<?php echo Lang::txt('Add a description'); ?>
			<?php echo $this->editor('description', '', 35, 5, 'field_description', array('class' => 'minimal no-footer')); ?>
		</label>
	</fieldset>

	<input type="hidden" name="post_id" value="<?php echo $this->post_id; ?>" />
	<input type="hidden" name="repost" value="1" />

	<input type="hidden" name="item_id" value="<?php echo $this->item_id; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="action" value="collect" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_POST'); ?>" />
	</p>
</form>
