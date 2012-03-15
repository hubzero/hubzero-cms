<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';

JToolBarHelper::title(JText::_('Store Manager') . $text, 'store.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

$created = NULL;
if (intval( $this->row->created ) <> 0)
{
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
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
<?php if (isset($this->row->id)) { ?>
			<legend><span><?php echo JText::_('STORE').' '. JText::_('ITEM').' #'.$this->row->id.' '.JText::_('DETAILS'); ?></span></legend>
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
	<div class="col width-40 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('OPTIONS'); ?></span></legend>
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
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
<?php  } // end if id exists ?>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
