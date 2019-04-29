<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//tag editor
$task = 'post/' . $this->post_id . '/collect';
if ($this->collection_id)
{
	$task = Request::getString('board', 0) . '/collect';
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name . '&scope=' . $task); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('Collect'); ?></legend>

		<div class="grid">
			<div class="col span5">
				<div class="form-group">
					<label for="field-collection_id">
						<?php echo Lang::txt('Select collection'); ?>
						<select name="collection_id" id="field-collection_id" class="form-control">
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
											if ($board->id == $this->collection_id)
											{
												continue;
											}
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
			</div>
			<div class="col span2">
				<p class="or">OR</p>
			</div>
			<div class="col span5 omega">
				<label for="field-collection_title">
					<?php echo Lang::txt('Create collection'); ?>
					<input type="text" name="collection_title" id="field-collection_title" class="form-control" value="" />
				</label>
			</div>
		</div>

		<div class="form-group">
			<label for="field_description">
				<?php echo Lang::txt('Add a description'); ?>
				<?php echo $this->editor('description', '', 35, 5, 'field_description', array('class' => 'form-control minimal no-footer')); ?>
			</label>
		</div>
	</fieldset>

	<input type="hidden" name="post_id" value="<?php echo $this->post_id; ?>" />
	<input type="hidden" name="repost" value="1" />

	<input type="hidden" name="item_id" value="<?php echo $this->item_id; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="<?php echo $this->escape($this->name); ?>" />
	<input type="hidden" name="action" value="collect" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input class="btn" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_SAVE'); ?>" />
	</p>
</form>
