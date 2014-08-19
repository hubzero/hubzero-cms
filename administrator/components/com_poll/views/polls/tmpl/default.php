<?php defined('_JEXEC') or die('Restricted access');

	JHTML::_('behavior.tooltip');

	$canDo = PollHelper::getActions('component');

	JToolBarHelper::title(  JText::_( 'COM_POLL' ), 'poll.png' );
	if ($canDo->get('core.edit.state'))
	{
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
	}
	if ($canDo->get('core.delete'))
	{
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
	}
	if ($canDo->get('core.edit'))
	{
		JToolBarHelper::editListX();
	}
	if ($canDo->get('core.create'))
	{
		JToolBarHelper::addNewX();
	}
	JToolBarHelper::spacer();
	JToolBarHelper::help( 'screen.polls' );
?>

<form action="index.php?option=com_poll" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->lists['search']); ?>" placeholder="<?php echo JText::_('COM_POLL_SEARCH_PLACEHOLDER'); ?>" />

			<button onclick="this.form.submit();"><?php echo JText::_('COM_POLL_GO'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<?php echo $this->lists['state']; ?>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('COM_POLL_COL_NUM'); ?>
				</th>
				<th>
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_TITLE', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_PUBLISHED', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_OPEN', 'm.open', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_VOTES', 'm.voters', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_OPTIONS', 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_LAG', 'm.lag', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_POLL_COL_ID', 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
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

			$link = JRoute::_('index.php?option=com_poll&view=poll&task=edit&cid='. $row->id);

			//$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i );

			$task  = $row->published ? 'unpublish' : 'publish';
			$class = $row->published ? 'published' : 'unpublished';
			$alt   = $row->published ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');

			$task2  = ($row->open == 1) ? 'close' : 'open';
			$class2 = ($row->open == 1) ? 'published' : 'unpublished';
			$alt2   = ($row->open == 1) ? JText::_('COM_POLL_OPEN') : JText::_('COM_POLL_CLOSED');
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php if (($row->checked_out && $row->checked_out != $this->user->get('id')) || !$canDo->get('core.edit')) { ?>
						<span> </span>
					<?php } else { ?>
						<input type="checkbox" name="cid[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td>
					<?php if (($row->checked_out && $row->checked_out != $this->user->get('id')) || !$canDo->get('core.edit')) {
						echo $row->title;
					} else { ?>
						<span class="editlinktip hasTip" title="<?php echo $this->escape($row->title); ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $this->escape($row->title); ?>
							</a>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class;?>" href="index.php?option=com_poll&amp;task=<?php echo $task; ?>&amp;cid=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_POLL_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class2; ?>" href="index.php?option=com_poll&amp;task=<?php echo $task2; ?>&amp;cid=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_POLL_SET_TO', $task2); ?>">
							<span><?php echo $alt2; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class2; ?>">
							<span><?php echo $alt2; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php echo $row->voters; ?>
				</td>
				<td>
					<?php echo $row->numoptions; ?>
				</td>
				<td>
					<?php echo $row->lag; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_poll" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>