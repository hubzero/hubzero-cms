<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->collection_id)
{
	$task = Request::getString('board', 0) . '/collect';
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=collect&post=' . $this->post_id); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></legend>

		<div class="grid">
			<div class="col span5">
				<label for="field-collection_id">
					<?php echo Lang::txt('COM_COLLECTIONS_COLLECTION'); ?>
					<select name="collection_id" id="field-collection_id">
						<option value="0"><?php echo Lang::txt('COM_COLLECTIONS_SELECT_COLLECTION'); ?></option>
						<optgroup label="<?php echo Lang::txt('COM_COLLECTIONS_MY_COLLECTIONS'); ?>">
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
			</div><!-- / .col -->

			<div class="col span2">
				<p class="or"><?php echo Lang::txt('COM_COLLECTIONS_OR'); ?></p>
			</div>

			<div class="col span5 omega">
				<label for="field-collection_title">
					<?php echo Lang::txt('COM_COLLECTIONS_CREATE_COLLECTION'); ?>
					<input type="text" name="collection_title" id="field-collection_title" value="" />
				</label>
			</div><!-- / .col -->
		</div><!-- / .grid -->

		<label for="field_description">
			<?php echo Lang::txt('COM_COLLECTIONS_ADD_DESCRIPTION'); ?>
			<?php echo $this->editor('description', '', 35, 5, 'field_description', array('class' => 'minimal no-footer')); ?>
		</label>
	</fieldset>

	<input type="hidden" name="post_id" value="<?php echo $this->post_id; ?>" />
	<input type="hidden" name="repost" value="1" />

	<input type="hidden" name="item_id" value="<?php echo $this->item_id; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<input type="hidden" name="id" value="<?php echo User::get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="collect" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt(strtoupper($this->option) . '_SAVE'); ?>" />
	</p>
</form>