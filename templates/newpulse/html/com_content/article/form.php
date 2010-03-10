<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
<!--
function setgood() {
	// TODO: Put setGood back
	return true;
}

var sectioncategories = new Array;
<?php
$i = 0;
foreach ($this->lists['sectioncategories'] as $k=>$items) {
	foreach ($items as $v) {
		echo "sectioncategories[".$i++."] = new Array( '$k','".addslashes( $v->id )."','".addslashes( $v->title )."' );\n\t\t";
	}
}
?>


function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	try {
		form.onsubmit();
	} catch(e) {
		alert(e);
	}

	// do field validation
	var text = <?php echo $this->editor->getContent( 'text' ); ?>
	if (form.title.value == '') {
		return alert ( "<?php echo JText::_( 'Article must have a title', true ); ?>" );
	} else if (text == '') {
		return alert ( "<?php echo JText::_( 'Article must have some text', true ); ?>");
	} else if (parseInt('<?php echo $this->article->sectionid;?>')) {
		// for articles
		if (form.catid && getSelectedValue('adminForm','catid') < 1) {
			return alert ( "<?php echo JText::_( 'Please select a category', true ); ?>" );
		}
	}
	<?php echo $this->editor->save( 'text' ); ?>
	submitform(pressbutton);
}
//-->
</script>
<div id="content-header" class="full">
	<h2><?php echo JText::_('Article'); ?></h2>
</div>
<div class="main section">
<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="hubForm" class="full" onSubmit="setgood();">
	<div class="explaination">
		<button type="button" onclick="submitbutton('save')">
			<?php echo JText::_('Save') ?>
		</button>
		<button type="button" onclick="submitbutton('cancel')">
			<?php echo JText::_('Cancel') ?>
		</button>
	</div>
	<fieldset>
		<legend><?php echo JText::_('Editor'); ?></legend>

		<label>
			<?php echo JText::_( 'Title' ); ?>:
			<input class="inputbox" type="text" id="title" name="title" size="50" maxlength="100" value="<?php echo $this->escape($this->article->title); ?>" />
		</label>

		<label>
			<?php echo JText::_( 'Content' ); ?>:
			<?php echo $this->editor->display('text', $this->article->text, '100%', '400', '70', '15'); ?>
		</label>
	</fieldset><div class="clear"></div>
	
	<fieldset>
		<legend><?php echo JText::_('Publishing'); ?></legend>

		<div class="group">
			<label>
				<?php echo JText::_( 'Section' ); ?>:
				<?php echo $this->lists['sectionid']; ?>
			</label>

			<label>
				<?php echo JText::_( 'Category' ); ?>:
				<?php echo $this->lists['catid']; ?>
			</label>
		</div>
		
<?php if ($this->user->authorize('com_content', 'publish', 'content', 'all')) : ?>
		<div class="group">
			<fieldset class="options">
				<legend><?php echo JText::_( 'Published' ); ?>:</legend>
				<?php echo $this->lists['state']; ?>
			</fieldset>
<?php endif; ?>
			<fieldset class="options">
				<legend><?php echo JText::_( 'Show on Front Page' ); ?>:</legend>
				<?php echo $this->lists['frontpage']; ?>
			</fieldset>
<?php if ($this->user->authorize('com_content', 'publish', 'content', 'all')) : ?>
		</div>
<?php endif; ?>
			<label>
				<?php echo JText::_( 'Author Alias' ); ?>:
				<input type="text" id="created_by_alias" name="created_by_alias" size="50" maxlength="100" value="<?php echo $this->article->created_by_alias; ?>" class="inputbox" />
			</label>
		
		<div class="group">
			<label class="datepickers">
				<?php echo JText::_( 'Start Publishing' ); ?>:
				<?php echo JHTML::_('calendar', $this->article->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
			</label>

			<label class="datepickers">
				<?php echo JText::_( 'Finish Publishing' ); ?>:
				<?php echo JHTML::_('calendar', $this->article->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
			</label>
		</div>

		<label for="access">
			<?php echo JText::_( 'Access Level' ); ?>:
			<?php echo $this->lists['access']; ?>
		</label>

		<label>
			<?php echo JText::_( 'Ordering' ); ?>:
			<?php echo $this->lists['ordering']; ?>
		</label>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('Metadata'); ?></legend>

		<label>
			<?php echo JText::_( 'Description' ); ?>:
			<textarea rows="5" cols="50" id="metadesc" name="metadesc"><?php echo str_replace('&','&amp;',$this->article->metadesc); ?></textarea>
		</label>

		<label>
			<?php echo JText::_( 'Keywords' ); ?>:
			<textarea rows="5" cols="50" id="metakey" name="metakey"><?php echo str_replace('&','&amp;',$this->article->metakey); ?></textarea>
		</label>
	</fieldset>

	<input type="hidden" name="option" value="com_content" />
	<input type="hidden" name="id" value="<?php echo $this->article->id; ?>" />
	<input type="hidden" name="version" value="<?php echo $this->article->version; ?>" />
	<input type="hidden" name="created_by" value="<?php echo $this->article->created_by; ?>" />
	<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>
<div class="clear"></div>
</div>