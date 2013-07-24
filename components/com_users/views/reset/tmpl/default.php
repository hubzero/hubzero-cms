<?php defined('_JEXEC') or die; ?>

<?php
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div id="content-header">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
	</div>
<?php endif; ?>
	<div class="main section">
		<form action="<?php echo JRoute::_( 'index.php?option=com_users&task=reset.request' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
			<div class="explaination">
				<p class="info">
					<?php echo JText::_('Forgot your username? Go <a href="/login/remind">here</a> to recover it.'); ?>
				</p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('Email Verification Token'); ?></legend>

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>

			</fieldset>
			<div class="clear"></div>
			
			<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div><!-- / .main section -->
