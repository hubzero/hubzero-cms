<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<p><a class="btn primary" href="#collectionForm" id="add-collection"><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $this->type->type);?></a></p>

<form action="<?php echo Route::url($this->resource->link());?>"  method="post" id="collectionForm" class="full hide">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $this->type->type);?></legend>
		<div class="grid">
			<?php if ($this->resources->count() > 0): ?>
				<div class="col span12">
					<label for="pid">
						<?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT', $this->type->type); ?>
					</label>
					<select name="pid" id="pid">
						<option value="" selected><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT_PLACEHOLDER', $this->type->type);?></option>
						<?php foreach ($this->resources as $entry): ?>
							<option value="<?php echo $entry->id;?>"><?php echo $entry->title; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col span12">
					<p class="or"><?php echo Lang::txt('OR'); ?></p>
				</div>
			<?php endif; ?>
			<div class="col span12" id="new-series-add">
				<label><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD_NEW', $this->type->type);?></label>
				<label for="resource-title">
					<?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_TITLE');?>
				</label>
				<input type="text" name="resource-title" id="resource-title" value="" />
			</div>
		</div>
	</fieldset>
	<p class="submit">
		<input type="hidden" name="childid" value="<?php echo $this->resource->id;?>"/>
		<input type="hidden" name="controller" value="attachments" />
		<input type="hidden" name="task" value="create" />
		<input type="hidden" name="type" value="<?php echo $this->type->id;?>" />
		<input type="submit" value="<?php echo Lang::txt('Add'); ?>" />
	</p>
</form>
