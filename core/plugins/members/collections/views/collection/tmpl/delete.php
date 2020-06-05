<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

$this->css();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo Route::url($base . '&task=' . $this->collection->get('alias') . '/delete'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_HEADER'); ?></legend>

			<p class="warning">
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_COLLECTION_WARNING', $this->escape(stripslashes($this->collection->get('title')))); ?>
			</p>

			<div class="form-group form-check">
				<label for="confirmdel" class="form-check-label">
					<input type="checkbox" class="option form-check-input" name="confirmdel" value="1" />
					<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE_COLLECTION_CONFIRM'); ?>
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
		<input type="hidden" name="action" value="deletecollection" />
		<input type="hidden" name="board" value="<?php echo $this->escape($this->collection->get('id')); ?>" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?>" />

			<?php if (!$this->no_html) { ?>
				<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</p>
	</form>
