<?php defined('_JEXEC') or die('Restricted access');

	JHTML::_('behavior.tooltip');

	$canDo = PollHelper::getActions('component');

	JToolBarHelper::title(  JText::_( 'Poll Manager' ), 'poll.png' );
	if ($canDo->get('core.edit.state')) 
	{
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
	}
	if ($canDo->get('core.delete')) 
	{
		JToolBarHelper::deleteList();
	}
	if ($canDo->get('core.edit')) 
	{
		JToolBarHelper::editListX();
	}
	if ($canDo->get('core.create')) 
	{
		JToolBarHelper::addNewX();
	}
	JToolBarHelper::help( 'screen.polls' );
?>

<form action="index.php?option=com_poll" method="post" name="adminForm" id="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th>
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th>
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',   'Poll Title', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'Published', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'Open', 'm.open', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'Votes', 'm.voters', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'Options', 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'Lag', 'm.lag', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'ID', 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 		= JRoute::_( 'index.php?option=com_poll&view=poll&task=edit&cid[]='. $row->id );

		$checked 	= JHTML::_('grid.checkedout',   $row, $i );
		//$published 	= JHTML::_('grid.published', $row, $i );
		
		$task  = $row->published ? 'unpublish' : 'publish';
		$class = $row->published ? 'published' : 'unpublished';
		$alt   = $row->published ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');

		$task2  = ($row->open == 1) ? 'close' : 'open';
		$class2 = ($row->open == 1) ? 'published' : 'unpublished';
		$alt2   = ($row->open == 1) ? JText::_('OPEN') : JText::_('CLOSED');
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
			<?php if (JTable::isCheckedOut($this->user->get('id'), $row->checked_out) || !$canDo->get('core.edit')) {
				echo $row->title;
			} else {
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Poll' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $link  ?>">
						<?php echo $this->escape($row->title); ?>
					</a>
				</span>
				<?php
			}
			?>
			</td>
			<td>
<?php if ($canDo->get('core.edit.state')) { ?>
				<a class="state <?php echo $class;?>" href="index.php?option=com_poll&amp;task=<?php echo $task; ?>&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>">
					<span><?php echo $alt; ?></span>
				</a>
<?php } else { ?>
				<span class="state <?php echo $class;?>">
					<span><?php echo $alt; ?></span>
				</span>
<?php } ?>
			</td>
			<td>
<?php if ($canDo->get('core.edit.state')) { ?>
				<a class="state <?php echo $class2;?>" href="index.php?option=com_poll&amp;task=<?php echo $task2; ?>&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task2;?>">
					<span><?php echo $alt2; ?></span>
				</a>
<?php } else { ?>
				<span class="state <?php echo $class2;?>">
					<span><?php echo $alt2; ?></span>
				</span>
<?php } ?>
			</td>
			<td align="center">
				<?php echo $row->voters; ?>
			</td>
			<td align="center">
				<?php echo $row->numoptions; ?>
			</td>
			<td align="center">
				<?php echo $row->lag; ?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="option" value="com_poll" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>