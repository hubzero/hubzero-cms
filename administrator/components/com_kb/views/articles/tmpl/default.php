<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_('KNOWLEDGE_BASE').'</a>: '.JText::_('ARTICLES'), 'addedit.png' );
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::addNew( 'newfaq' );
JToolBarHelper::editList();
JToolBarHelper::deleteList( '', 'deletefaq', JText::_('DELETE') );

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>


<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('CATEGORY'); ?>: 
			<?php
			if ($this->filters['cid']) {
				echo KbHtml::sectionSelect( $this->sections, $this->filters['cid'], 'id' );
			} else {
				echo KbHtml::sectionSelect( $this->sections, $this->filters['id'], 'id' );
			}
			?>
		</label>
	
		<label>
			<?php echo JText::_('SORT_BY'); ?>: 
			<select name="filterby" onchange="document.adminForm.task='articles';document.adminForm.submit();">
				<option value="m.modified"<?php if ($this->filters['filterby'] == 'm.modified') { echo ' selected="selected"'; } ?>><?php echo JText::_('MODIFIED'); ?></option>
				<option value="m.title"<?php if ($this->filters['filterby'] == 'm.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
				<option value="m.id"<?php if ($this->filters['filterby'] == 'm.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
			</select>
		</label>
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
 				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
 				<th><?php echo JText::_('QUESTION'); ?></th>
 				<th><?php echo JText::_('PUBLISHED'); ?></th>
 				<th><?php echo JText::_('CATEGORY'); ?></th>
 				<th><?php echo JText::_('Votes'); ?></th>
 				<th><?php echo JText::_('CHECKED_OUT'); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$database =& JFactory::getDBO();
//$sc = new SupportComment( $database );
$st = new KbTags( $database );

for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];

	switch ($row->state) 
	{
		case '1':
			$class = 'published';
			$task = 'unpublish';
			$alt = JText::_('PUBLISHED');
			break;
		case '2':
			$class = 'expired';
			$task = 'publish';
			$alt = JText::_('TRASHED');
			break;
		case '0':
			$class = 'unpublished';
			$task = 'publish';
			$alt = JText::_('UNPUBLISHED');
			break;
	}
	
	$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;task=editfaq&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('EDIT_ARTICLE'); ?>"><?php echo stripslashes($row->title); ?></a><br />
					<span>Tags: <?php echo $tags; ?></span>
				</td>
				<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;cid=<?php echo $this->filters['id']; ?>" title="<?php echo JText::sprintf('SET_TASK',$task);?>"><span><?php echo $alt; ?></span></a></td>
				<td><?php echo $row->ctitle; echo ($row->cctitle) ? ' ('.$row->cctitle.')' : ''; ?></td>
				<td>+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?></td>
				<td><?php echo $row->editor; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['cid']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<p><?php echo JText::_('PUBLISH_KEY'); ?></p>