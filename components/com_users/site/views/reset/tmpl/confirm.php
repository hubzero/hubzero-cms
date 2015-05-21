<?php
defined('_JEXEC') or die;

Html::behavior('keepalive');
Html::behavior('formvalidation');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('Confirm your Account'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url( 'index.php?option=com_users&task=reset.Confirm' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo Lang::txt('Email New Password'); ?></legend>

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<p><?php echo Lang::txt($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit" class="validate"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</section><!-- / .main section -->