<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';

JToolBarHelper::title( JText::_( 'Store Manager' ).$text, 'addedit.png' );
JToolBarHelper::save('saveitem', 'Save Store Item');
JToolBarHelper::cancel('cancel_i');

$created = NULL;
if (intval( $this->row->created ) <> 0) {
	$created = JHTML::_('date', $this->row->created, '%d %b, %Y');
}

?>

<script type="text/javascript">
public function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );

}
</script>

<p class="extranav" style="margin-left:1.5em;"><?php echo JText::_('VIEW'); ?>: <a href="index.php?option=<?php echo $this->option; ?>&amp;task=storeitems"><?php echo JText::_('STORE').' '. JText::_('ITEMS'); ?></a></p>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
<?php if (isset($this->row->id)) { ?>
			<legend><?php echo JText::_('STORE').' '. JText::_('ITEM').' #'.$this->row->id.' '.JText::_('DETAILS'); ?></legend>
			<table class="admintable">
			 <tbody>
	         <tr>
			  <td class="key"><label><?php echo JText::_('CATEGORY'); ?>:</label></td>
			   <td><select name="category">
	           		<option value="service"<?php if ($this->row->category == 'service') { echo ' selected="selected"'; } ?>>Service</option>
		 			<option value="wear"<?php if ($this->row->category == 'wear') { echo ' selected="selected"'; } ?>>Wear</option>
	     			<option value="office"<?php if ($this->row->category == 'office') { echo ' selected="selected"'; } ?>>Office</option>
	                <option value="fun"<?php if ($this->row->category == 'fun') { echo ' selected="selected"'; } ?>>Fun</option>
				   </select>
	    		</td>
			  </tr>
	          <tr>
			   <td class="key"><label><?php echo JText::_('PRICE'); ?>:</label></td>
			   <td><input type="text" name="price" id="price"  size="5" value="<?php echo $this->row->price; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
			   <td><input type="text" name="title" id="title"  maxlength="100" style="width:100%" value="<?php echo stripslashes($this->row->title); ?>" /></td>
			  </tr>
	          <tr>
			  <td class="key"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
			   <td><textarea name="description" id="description"  cols="50" rows="10"><?php echo stripslashes($this->row->description); ?></textarea>
	        <br /><?php echo JText::_('WARNING_DESCR'); ?></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('OPTIONS'); ?></legend>
			<table class="admintable">
			 <tbody>
			 <tr>
			  <td class="key"><label><?php echo JText::_('PUBLISHED'); ?>:</label></td>
			   <td><input type="checkbox" name="published" value="1" <?php echo ($this->row->published) ? 'checked="checked"' : ''; ?> /></td>
			  </tr>
	          <tr>
			   <td class="key"><label><?php echo ucfirst(JText::_('INSTOCK')); ?>:</label></td>
			   <td><input type="checkbox" name="available" value="1" <?php echo ($this->row->available) ? 'checked="checked"' : ''; ?> /></td>
			  </tr> 
	          <tr>
			   <td class="key"><label><?php echo JText::_('FEATURED'); ?>:</label></td>
			   <td><input type="checkbox" name="featured" id="featured" value="1" <?php echo ($this->row->featured) ? 'checked="checked"' : ''; ?> /></td>
			  </tr> 
	          <tr>
			   <td class="key"><label><?php echo JText::_('AV_SIZES'); ?>:</label></td>
			   <td><input type="text" name="sizes" size="15" value="<?php echo (isset($this->row->size)) ? $this->row->size : '' ; ?>" /><br /><?php echo JText::_('SAMPLE_SIZES'); ?>:</td>
			  </tr>           
			 </tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>			 
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="saveorder" />
<?php  } // end if id exists ?>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>