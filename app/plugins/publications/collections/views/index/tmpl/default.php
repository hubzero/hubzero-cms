<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<p><a class="btn primary" href="#collectionForm" id="add-collection"><?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_ADD', $this->type->type);?></a></p>

<form action="<?php echo Route::url($this->publication->link());?>"  method="post" id="collectionForm" class="full hide">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_ADD', $this->type->type);?></legend>
		<div class="grid">
			<?php if ($this->publications->count() > 0): ?>
				<div class="col span12">
					<label for="pid">
						<?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_SELECT', $this->type->type); ?>
					</label>
					<select name="pid" id="pid">
						<option value="" selected><?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_SELECT_PLACEHOLDER', $this->type->type);?></option>
						<?php foreach ($this->publications as $entry): ?>
							<option value="<?php echo $entry->id;?>"><?php echo $entry->title; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col span12">
					<p class="or"><?php echo Lang::txt('OR'); ?></p>
				</div>
			<?php endif; ?>
			<div class="col span12" id="new-series-add">
				<label><?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_ADD_NEW', $this->type->type);?></label>
				<label for="publication-title">
					<?php echo Lang::txt('PLG_PUBLICATIONS_COLLECTIONS_TITLE');?>
				</label>
				<input type="text" name="publication-title" id="publication-title" value="" />
			</div>
		</div>
	</fieldset>
	<p class="submit">
		<input type="hidden" name="childid" value="<?php echo $this->publication->id;?>"/>
		<input type="hidden" name="controller" value="attachments" />
		<input type="hidden" name="task" value="create" />
		<input type="hidden" name="type" value="<?php echo $this->type->id;?>" />
		<input type="submit" value="<?php echo Lang::txt('Add'); ?>" />
	</p>
</form>
