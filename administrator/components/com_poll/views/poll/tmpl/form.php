<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
	$edit=JRequest::getVar( 'edit', true );
	JArrayHelper::toInteger($cid, array(0));

	$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

	JToolBarHelper::title(  JText::_( 'Poll' ).': ' . $text, 'poll.png');
	if ($this->poll->id) 
	{
		JToolBarHelper::Preview('index.php?option=com_poll&controller=poll&cid[]='.$cid[0]);
		JToolBarHelper::spacer();
	}
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::spacer();
	if ($edit) {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'Close' );
	} else {
		JToolBarHelper::cancel();
	}
	JToolBarHelper::spacer();
	JToolBarHelper::help( 'screen.polls.edit' );
?>

<?php
JFilterOutput::objectHTMLSafe( $this->poll, ENT_QUOTES );
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.title.value == "") {
			alert( "<?php echo JText::_( 'Poll must have a title', true ); ?>" );
		} else if (isNaN(parseInt( form.lag.value ) ) || parseInt(form.lag.value) < 1)  {
			alert( "<?php echo JText::_( 'Poll must have a non-zero lag time', true ); ?>" );
		//} else if (form.menu.options.value == ""){
		//	alert( "Poll must have pages." );
		//} else if (form.adminForm.textfieldcheck.value == 0){
		//	alert( "Poll must have options." );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_( 'Details' ); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_( 'Title' ); ?>:</label><br />
				<input class="inputbox" type="text" name="title" id="field-title" value="<?php echo $this->escape($this->poll->title); ?>" />
			</div>
			<div class="input-wrap">
				<label for="alias"><?php echo JText::_( 'Alias' ); ?>:</label><br />
				<input class="inputbox" type="text" name="alias" id="field-alias" value="<?php echo $this->escape($this->poll->alias); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_( 'seconds between votes' ); ?>">
				<label for="lag"><?php echo JText::_( 'Lag' ); ?>:</label><br />
				<input class="inputbox" type="text" name="lag" id="field-lag" value="<?php echo $this->escape($this->poll->lag); ?>" />
				<span class="hint"><?php echo JText::_( '(seconds between votes)' ); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-published"><?php echo JText::_( 'Published' ); ?>:</label><br />
				<?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->poll->published ); ?>
			</div>
			<div class="input-wrap">
				<label for="field-open"><?php echo JText::_( 'Open' ); ?>:</label><br />
				<?php echo JHTML::_( 'select.booleanlist',  'open', 'class="inputbox"', $this->poll->open ); ?>
			</div>
		</fieldset>
		<p class="warning">This whole thing is wildly inaccurate. Rounding errors, ballot stuffers, dynamic IPs, firewalls. If you're using these numbers to do anything important, you're insane.</p>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_( 'Options' ); ?></span></legend>

		<?php for ($i=0, $n=count( $this->options ); $i < $n; $i++ ) { ?>
			<div class="input-wrap">
				<label for="polloption<?php echo $this->options[$i]->id; ?>"><?php echo JText::_( 'Option' ); ?> <?php echo ($i+1); ?></label><br />
				<input class="inputbox" type="text" name="polloption[<?php echo $this->options[$i]->id; ?>]" id="polloption<?php echo $this->options[$i]->id; ?>" value="<?php echo $this->escape($this->options[$i]->text); ?>" />
			</div>
			<?php } for (; $i < 12; $i++) { ?>
			<div class="input-wrap">
				<label for="polloption<?php echo $i + 1; ?>"><?php echo JText::_( 'Option' ); ?> <?php echo $i + 1; ?></label><br />
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
