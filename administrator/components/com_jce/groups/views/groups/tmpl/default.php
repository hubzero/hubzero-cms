<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>
<?php JHTML::stylesheet('icons.css', 'administrator/components/com_jce/css/'); ?>

<?php
	$rows =& $this->items;
	
	JToolBarHelper::title( JText::_( 'JCE Group Manager' ), 'user.png' );
	
	JToolBarHelper::editListX();
	JToolBarHelper::addNewX();
	JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_('Copy'), true );
	if( count( $rows ) > 1 ){
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
	}
	JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
	jceToolbarHelper::help( 'groups' );


?>
<script type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		// Cancel button
		if (pressbutton == "cancelEdit") {
			submitform(pressbutton);
			return;
		}
		// validation
		if (form.name.value == "") {
			alert( "<?php echo JText::_( 'Group must have a name', true ); ?>" );
		} else {
			submitform(pressbutton);
		}
	}
</script>
<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
	</tr>
</table>

<table class="adminlist">
<thead>
	<tr>
		<th width="1%">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<th width="1%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
		</th>
		<th class="title" width="20%">
			<?php echo JHTML::_('grid.sort',   'Name', 'g.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
        <th class="title">
			<?php echo JHTML::_('grid.sort',   'Description', 'g.description', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th nowrap="nowrap" width="5%">
			<?php echo JHTML::_('grid.sort',   'Published', 'g.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
        <th nowrap="nowrap" width="8%" >
			<?php echo JHTML::_('grid.sort',   'Priority', 'g.ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			<?php if( count( $rows ) > 1 ){ echo JHTML::_('grid.order',  $rows );}?>
		</th>
		<th nowrap="nowrap"  width="1%" class="title">
			<?php echo JHTML::_('grid.sort',   'ID', 'g.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="12">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
<tbody>
<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	$row = $rows[$i];

	$link = JRoute::_( 'index.php?option=com_jce&type=group&task=edit&cid[]='. $row->id );
	if( $n == 1){
		$published 	= JHTMLImage::administrator('tick.png');
	}else{
		$published 	= JHTML::_('grid.published', $row, $i );
	}
	$checked 	= JHTML::_('grid.checkedout', $row, $i );
	$ordering 	= ($this->lists['order'] == 'g.ordering');
?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="right">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td>
			<?php echo $checked; ?>
		</td>
		<td>
			<?php
			if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
				echo $row->name;
			} else {	
			?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Group' );?>::<?php echo $row->name; ?>">
				<a href="<?php echo $link; ?>">
					<?php echo $row->name; ?></a></span>
			<?php } ?>
		</td>
        <td>
			<?php echo $row->description;?>
		</td>
		<td align="center">
			<?php echo $published;?>
		</td>
        <td class="order">
			<span><?php echo $this->pagination->orderUpIcon( $i, ($row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'Move Up', $ordering ); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'Move Down', $ordering ); ?></span>
			<?php $disabled = $n > 1?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled ?> class="text_area" style="text-align: center" />
		</td>
		<td align="center">
			<?php echo $row->id;?>
		</td>
	</tr>
	<?php
		$k = 1 - $k;
	}
	?>
</tbody>
</table>
	<input type="hidden" name="option" value="com_jce" />
	<input type="hidden" name="task" value="view" />
    <input type="hidden" name="type" value="group" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>