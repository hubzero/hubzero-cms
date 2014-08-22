<?php defined('_JEXEC') or die('Restricted access');

	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
	$edit=JRequest::getVar( 'edit', true );
	JArrayHelper::toInteger($cid, array(0));

	$text = ( $edit ? JText::_( 'JACTION_EDIT' ) : JText::_( 'JACTION_CREATE' ) );

	JToolBarHelper::title(  JText::_( 'COM_POLL' ).': ' . $text, 'poll.png');
	if ($this->poll->id)
	{
		JToolBarHelper::preview('index.php?option=com_poll&controller=poll&cid='.$cid[0]);
		JToolBarHelper::spacer();
	}
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::spacer();
	if ($edit) {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'COM_POLL_CLOSE' );
	} else {
		JToolBarHelper::cancel();
	}
	JToolBarHelper::spacer();
	JToolBarHelper::help('poll');

JFilterOutput::objectHTMLSafe( $this->poll, ENT_QUOTES );
?>

<script type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.title.value == "") {
			alert( "<?php echo JText::_( 'COM_POLL_ERROR_MISSING_TITLE', true ); ?>" );
		} else if (isNaN(parseInt( form.lag.value ) ) || parseInt(form.lag.value) < 1)  {
			alert( "<?php echo JText::_( 'COM_POLL_ERROR_MISSING_LAG', true ); ?>" );
		//} else if (form.menu.options.value == ""){
		//	alert( "COM_POLL_ERROR_MISSING_OPTIONS" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_( 'JDETAILS' ); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_( 'COM_POLL_FIELD_TITLE' ); ?>:</label><br />
				<input class="inputbox" type="text" name="title" id="field-title" value="<?php echo $this->escape($this->poll->title); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_( 'COM_POLL_FIELD_ALIAS' ); ?>:</label><br />
				<input class="inputbox" type="text" name="alias" id="field-alias" value="<?php echo $this->escape($this->poll->alias); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_( 'COM_POLL_FIELD_LAG_HINT' ); ?>">
				<label for="field-lag"><?php echo JText::_( 'COM_POLL_FIELD_LAG' ); ?>:</label><br />
				<input class="inputbox" type="text" name="lag" id="field-lag" value="<?php echo $this->escape($this->poll->lag); ?>" />
				<span class="hint"><?php echo JText::_( 'COM_POLL_FIELD_LAG_HINT' ); ?></span>
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_( 'COM_POLL_FIELD_PUBLISHED' ); ?>:</label><br />
				<?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->poll->published ); ?>
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_( 'COM_POLL_FIELD_OPEN' ); ?>:</label><br />
				<?php echo JHTML::_( 'select.booleanlist',  'open', 'class="inputbox"', $this->poll->open ); ?>
			</div>
		</fieldset>
		<p class="warning"><?php echo JText::_('COM_POLL_WARNING'); ?></p>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_( 'COM_POLL_FIELDSET_OPTIONS' ); ?></span></legend>

			<?php for ($i=0, $n=count( $this->options ); $i < $n; $i++ ) { ?>
				<div class="input-wrap">
					<label for="polloption<?php echo $this->options[$i]->id; ?>"><?php echo JText::_( 'COM_POLL_FIELD_OPTION' ); ?> <?php echo ($i+1); ?></label><br />
					<input class="inputbox" type="text" name="polloption[<?php echo $this->options[$i]->id; ?>]" id="polloption<?php echo $this->options[$i]->id; ?>" value="<?php echo $this->escape(str_replace('&#039;', "'", $this->options[$i]->text)); ?>" />
				</div>
			<?php } ?>
			<?php for (; $i < 12; $i++) { ?>
				<div class="input-wrap">
					<label for="polloption<?php echo $i + 1; ?>"><?php echo JText::_( 'COM_POLL_FIELD_OPTION' ); ?> <?php echo $i + 1; ?></label><br />
					<input class="inputbox" type="text" name="polloption[]" id="polloption<?php echo $i + 1; ?>" value="" />
				</div>
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_poll" />
	<input type="hidden" name="id" value="<?php echo $this->poll->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->poll->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
