<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

Html::behavior('keepalive');
Html::behavior('tooltip');
Html::behavior('calendar');
Html::behavior('formvalidation');
Html::asset('script', 'system/core.js', false, true);

$this->js();

// Create shortcut to parameters.
$params = $this->params;
//$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions):
	$params->show_urls_images_frontend = '0';
endif;
?>

<script type="text/javascript">
	Hubzero.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid($('#adminForm'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Hubzero.submitform(task);
		} else {
			alert('<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<?php if ($params->get('show_page_heading')) : ?>
	<header id="content-header">
		<h2>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h2>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<form action="<?php echo Route::url('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="hubForm" class="full form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
			<fieldset>
				<legend><?php echo Lang::txt('JEDITOR'); ?></legend>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<?php if (is_null($this->item->id)): ?>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('alias'); ?>
						<?php echo $this->form->getInput('alias'); ?>
					</div>
				<?php endif; ?>

				<div class="form-group input-wrap">
					<?php echo $this->form->getInput('articletext'); ?>
				</div>
			</fieldset>

			<?php if ($params->get('show_urls_images_frontend')): ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CONTENT_IMAGES_AND_URLS'); ?></legend>

					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_intro', 'images'); ?>
						<?php echo $this->form->getInput('image_intro', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_intro_alt', 'images'); ?>
						<?php echo $this->form->getInput('image_intro_alt', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_intro_caption', 'images'); ?>
						<?php echo $this->form->getInput('image_intro_caption', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('float_intro', 'images'); ?>
						<?php echo $this->form->getInput('float_intro', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_fulltext', 'images'); ?>
						<?php echo $this->form->getInput('image_fulltext', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_fulltext_alt', 'images'); ?>
						<?php echo $this->form->getInput('image_fulltext_alt', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('image_fulltext_caption', 'images'); ?>
						<?php echo $this->form->getInput('image_fulltext_caption', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('float_fulltext', 'images'); ?>
						<?php echo $this->form->getInput('float_fulltext', 'images'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urla', 'urls'); ?>
						<?php echo $this->form->getInput('urla', 'urls'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urlatext', 'urls'); ?>
						<?php echo $this->form->getInput('urlatext', 'urls'); ?>
					</div>
					<?php echo $this->form->getInput('targeta', 'urls'); ?>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urlb', 'urls'); ?>
						<?php echo $this->form->getInput('urlb', 'urls'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urlbtext', 'urls'); ?>
						<?php echo $this->form->getInput('urlbtext', 'urls'); ?>
					</div>
					<?php echo $this->form->getInput('targetb', 'urls'); ?>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urlc', 'urls'); ?>
						<?php echo $this->form->getInput('urlc', 'urls'); ?>
					</div>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('urlctext', 'urls'); ?>
						<?php echo $this->form->getInput('urlctext', 'urls'); ?>
					</div>
					<?php echo $this->form->getInput('targetc', 'urls'); ?>
				</fieldset>
			<?php endif; ?>

			<fieldset>
				<legend><?php echo Lang::txt('COM_CONTENT_PUBLISHING'); ?></legend>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('catid'); ?>
					<span class="category">
						<?php echo $this->form->getInput('catid'); ?>
					</span>
				</div>
				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('created_by_alias'); ?>
					<?php echo $this->form->getInput('created_by_alias'); ?>
				</div>

				<?php if ($this->item->params->get('access-change')): ?>
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('state'); ?>
						<?php echo $this->form->getInput('state'); ?>
					</div>

					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('featured'); ?>
						<?php echo $this->form->getInput('featured'); ?>
					</div>

					<div class="grid">
						<div class="col span6">
							<div class="form-group input-wrap">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
						</div>
						<div class="col span6 omega">
							<div class="form-group input-wrap">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?>
				</div>

				<?php if (is_null($this->item->id)): ?>
					<div class="form-note">
						<p><?php echo Lang::txt('COM_CONTENT_ORDERING'); ?></p>
					</div>
				<?php endif; ?>
			</fieldset>

			<fieldset>
				<legend><?php echo Lang::txt('JFIELD_LANGUAGE_LABEL'); ?></legend>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo Lang::txt('COM_CONTENT_METADATA'); ?></legend>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('metadesc'); ?>
					<?php echo $this->form->getInput('metadesc'); ?>
				</div>
				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('metakey'); ?>
					<?php echo $this->form->getInput('metakey'); ?>
				</div>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
				<?php if ($this->params->get('enable_category', 0) == 1) :?>
					<input type="hidden" name="fields[catid]" value="<?php echo $this->params->get('catid', 1); ?>"/>
				<?php endif;?>
				<?php echo Html::input('token'); ?>
			</fieldset>

			<div class="formelm-buttons submit">
				<input type="submit" class="btn btn-success" id="btn-save" value="<?php echo Lang::txt('JSAVE') ?>" />
				<button type="button" class="btn btn-secondary" id="btn-cancel">
					<?php echo Lang::txt('JCANCEL') ?>
				</button>
			</div>
		</form>
	</div>
</section>
