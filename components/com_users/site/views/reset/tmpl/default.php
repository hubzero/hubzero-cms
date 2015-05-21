<?php defined('_JEXEC') or die; ?>

<?php
Html::behavior('keepalive');
Html::behavior('formvalidation');
?>

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<header id="content-header">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
	</header>
<?php endif; ?>
	<section class="main section">
		<form action="<?php echo Route::url( 'index.php?option=com_users&task=reset.request' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
			<div class="explaination">
				<p class="info">
					<?php echo Lang::txt('Forgot your username? Go <a href="%s">here</a> to recover it.', Route::url('index.php?option=com_users&view=remind')); ?>
				</p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('Email Verification Token'); ?></legend>

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<p><?php echo Lang::txt($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>

			</fieldset>
			<div class="clear"></div>

			<p class="submit"><button type="submit" class="validate"><?php echo Lang::txt('Submit'); ?></button></p>
			<?php echo Html::input('token'); ?>
		</form>
	</div><!-- / .main section -->
