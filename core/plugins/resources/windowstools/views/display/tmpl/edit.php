<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
?>

<div class="pages-wrap">
	<div class="pages-content">

		<form action="<?php echo Route::url($this->base); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_EDIT_PAGE'); ?></legend>

				<label for="fields_content">
					<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<?php echo $this->editor('fields[content]', $this->escape($this->page->get('content')), 35, 50, 'field_content'); ?>
				</label>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo Route::url($this->base); ?>">
						<?php echo Lang::txt('JCANCEL'); ?>
					</a>
				</p>
			</fieldset>

			<input type="hidden" name="fields[plugin]" value="<?php echo $this->name; ?>" />
			<input type="hidden" name="fields[title]" value="<?php echo $this->page->get('title'); ?>" />
			<input type="hidden" name="fields[alias]" value="<?php echo $this->page->get('alias'); ?>" />
			<input type="hidden" name="fields[state]" value="<?php echo $this->page->get('state'); ?>" />
			<input type="hidden" name="fields[access]" value="<?php echo $this->page->get('access'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->page->get('id'); ?>" />

			<?php echo Html::input('token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
			<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
			<input type="hidden" name="action" value="save" />
		</form>

	</div>
</div>