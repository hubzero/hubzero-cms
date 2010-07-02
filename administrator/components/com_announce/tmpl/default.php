<?php 
$mdl = $this->getModel('announcements');
$rows = $mdl->get_all(); 
?>
<form action="index.php" method="get" name="adminForm" id="adminForm">
	<fieldset id="filter">                                         
		<label>                                                
			<?php echo JText::_('SEARCH'); ?>:             
			<input type="text" name="terms" value="<?php echo $mdl->get_search_terms(); ?>" />
		</label>                                                                             
		<input type="submit" value="Search" />
	</fieldset>                                                          
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
	<thead>                                                             
	<tr>                                                        
		<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /> ID</th>
		<th>Title</th>                                                                  
		<th>Link</th>      
		<th>Category</th>
		<th>Active date</th>                                                             
		<th>Archive date</th>                                                                  
		<th>Current state</th>
	</tr>                                                                                                              
</thead>                                                                                                                   
<tbody>                                                                                                                    
	<?php if (count($rows)) :
		foreach ($rows as $idx=>$ann)
		{
	?>
			<tr>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $idx;?>" value="<?php echo $ann['id']; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id=<? echo $ann['id']; ?>" title="Edit"><?php echo $ann['title']; ?></a></td>
				<td><a href="<?php echo $ann['link']; ?>" title="Visit link"><?php echo $ann['link']; ?></a></td>
				<td><?php echo $ann['category']; ?></td>
				<td><?php echo date('Y/m/d', strtotime($ann['active_date'])); ?></td>
				<td><?php echo $ann['expiration_date'] ? date('Y/m/d', strtotime($ann['expiration_date'])) : 'None'; ?></td>
				<td><?php echo $ann['state']; ?></td>
			</tr>
	<?php
		}
		else : ?>
		<tr>
			<td colspan="7">No announcements found</td>
		</tr>
	<?php endif; ?>
</tbody>                                                                                                                                       
</table>                                                                                                                                               
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="<?php echo $option ?>" />
<input type="hidden" name="task" value="" />                       
</form>  
