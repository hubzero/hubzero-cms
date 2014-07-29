<?php defined('_JEXEC') or die; ?>

<?php
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>

<?php if ($this->params->get('show_page_title',1)) : ?>
<header id="content-header">
	<h2><?php echo $this->escape($this->params->get('page_title')) ?></h2>
</header>
<?php endif; ?>

<section class="main section">
	<form action="<?php echo JRoute::_( 'index.php?option=com_users&task=remind.remind' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you already know your username, and only need your password reset, <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">go here now</a>.
			</p>
		</div>
		<fieldset>
			<legend>Recover Username(s)</legend>

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<p><?php echo JText::_($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>

			<div class="help">
				<h4>What if I have also lost my password?</h4>
				<p>
					Fill out this form to retrieve your username(s). The email you
					receive will contain instructions on how to reset your password as well.
				</p>

				<h4>What if I have multiple accounts?</h4>
				<p>
					All accounts registered to your email address will be located, and you will be given a
					list of all of those usernames.
				</p>

				<h4>What if this cannot find my account?</h4>
				<p>
					It is possible you registered under a different email address.  Please try any other email
					addresses you have.
				</p>
			</div>
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div><!-- / .main section -->