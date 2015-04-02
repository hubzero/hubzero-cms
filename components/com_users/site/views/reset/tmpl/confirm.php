<?php
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>

<header id="content-header">
	<h2><?php echo JText::_('Confirm your Account'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo JRoute::_( 'index.php?option=com_users&task=reset.Confirm' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo JText::_('Email New Password'); ?></legend>

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<p><?php echo JText::_($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</section><!-- / .main section -->