<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>
<?php JHTML::stylesheet('icons.css', 'administrator/components/com_jce/css/'); ?>

<?php
	JToolBarHelper::title( JText::_( 'Plugin Manager' ), 'plugin.png' );
	
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	
	JToolBarHelper::addNew( 'add', JText::_( 'New Plugin' ) );
	JToolBarHelper::custom( 'manage', 'upload.png', 'upload_f2.png', JText::_( 'Install' ), false );
	//jceToolbarHelper::popup( JText::_( 'Editor Layout' ), 'move', 'plugin', 'layout' );
	//JToolBarHelper::custom( '', 'refresh.png', 'refresh_f2.png', JText::_( 'Refresh List' ), false );
	JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
	jceToolbarHelper::help( 'plugins' );

	$rows =& $this->items;

?>
<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php
			echo $this->lists['type'];
			echo $this->lists['state'];
			?>
		</td>
	</tr>
</table>

<table class="adminlist">
<thead>
	<tr>
		<th width="20">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort',   'Title', 'p.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th nowrap="nowrap" width="5%">
			<?php echo JHTML::_('grid.sort',   'Published', 'p.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
        <th nowrap="nowrap" width="5%">
			<?php echo JHTML::_('grid.sort',   'Row', 'p.row', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th width="8%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',   'Order', 'p.ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th nowrap="nowrap"  width="10%" class="title">
			<?php echo JHTML::_('grid.sort',   'Type', 'p.type', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th nowrap="nowrap"  width="10%" class="title">
			<?php echo JHTML::_('grid.sort',   'Name', 'p.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
        <th nowrap="nowrap"  width="10%" class="title">
			<?php echo JHTML::_('grid.sort',   'Icon', 'p.layout', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th nowrap="nowrap"  width="1%" class="title">
			<?php echo JHTML::_('grid.sort',   'ID', 'p.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
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
	$row 	= $rows[$i];

	$link = JRoute::_( 'index.php?option=com_jce&type=plugin&view=plugin&task=edit&cid[]='. $row->id );

	$checked 	= JHTML::_('grid.checkedout',   $row, $i );
	$published 	= JHTML::_('grid.published', $row, $i );

	$ordering = ($this->lists['order'] == 'p.type');
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
			if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) || !$row->editable ) {
				echo $row->title;
			} else {	
			?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Plugin' );?>::<?php echo $row->title; ?>">
				<a href="<?php echo $link; ?>">
					<?php echo $row->title; ?></a></span>
			<?php } ?>
		</td>
		<td align="center">
			<?php echo $published;?>
		</td>
        <td align="center">
			<?php echo $row->row;?>
		</td>
		<td class="order">
			<?php echo $row->ordering;?>
		</td>
		<td nowrap="nowrap">
			<?php echo $row->type;?>
		</td>
		<td nowrap="nowrap">
			<?php echo $row->name;?>
		</td>
        <td nowrap="nowrap">
			<?php if( $row->type == 'plugin' && $row->layout ){
            	echo '<img height="20" src="../plugins/editors/jce/tiny_mce/plugins/'. $row->name .'/img/'. $row->layout .'.gif" alt="'. $row->name .'" />';	
            }
            if( $row->type == 'command' ){
				echo '<img height="20" src="../plugins/editors/jce/tiny_mce/themes/advanced/img/'. $row->layout .'.gif" alt="'. $row->name .'" />';
			}?>
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
    <input type="hidden" name="type" value="plugin" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>