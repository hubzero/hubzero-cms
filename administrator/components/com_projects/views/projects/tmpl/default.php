<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b. %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M. Y';
	$tz = false;
}

JToolBarHelper::title( JText::_( 'Projects' ), 'user.png' );
JToolBarHelper::preferences('com_projects', '550');
JToolBarHelper::editList();

include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter">
		<span><?php echo JText::_('Total projects'); ?>: <strong><?php echo $this->total; ?></strong></span> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
		<label for="search">
			<?php echo JText::_('Search'); ?>: 
			<input type="text" name="search" id="search" value="<?php echo $this->filters['search']; ?>" />
		</label>	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	<table class="adminlist" id="projects-admin">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th style="width: 30px;"></th>
				<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th colspan="2"><?php echo JHTML::_('grid.sort', 'Owner', 'owner', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Status', 'status', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Privacy', 'privacy', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JText::_('Activity count'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$filterstring  = ($this->filters['sortby'])   ? '&amp;sort='.$this->filters['sortby']     : '';

			$database =& JFactory::getDBO();
			$now = date( "Y-m-d H:i:s" );
			
			$database =& JFactory::getDBO();
			$pt = new ProjectTags($database);
			
			for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
			{
				$row =& $this->rows[$i];
				
				$thumb = ProjectsHtml::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
				
				if($row->owned_by_group && !$row->groupcn) {
					$row->groupname = '<span class="italic pale">'.JText::_('COM_PROJECTS_INFO_DELETED_GROUP').'</span>';
				}
				$owner = ($row->owned_by_group) ? $row->groupname.'<span class="block  prominent">'.$row->groupcn.'</span>' : $row->authorname;			
				$ownerclass = ($row->owned_by_group) ? '<span class="i_group">&nbsp;</span>' : '<span class="i_user">&nbsp;</span>';
				
				// Determine status
				$status = '';
				if($row->state == 1 && $row->setup_stage >= $setup_complete) {
					$status = '<span class="active">'.JText::_('Active').'</span> '.JText::_('since').' '.JHTML::_('date', $row->created, $dateFormat, $tz);
				}
				else if($row->state == 2) {
					$status  = '<span class="deleted">'.JText::_('Deleted').'</span> ';
				}
				else if ($row->setup_stage < $setup_complete) {
					$status = '<span class="setup">'.JText::_('Setup').'</span> '.JText::_('in progress');
				}
				else if($row->state == 0) {
					$status = '<span class="faded italic">'.JText::_('Inactive/Suspended').'</span> ';
				}
				else if($row->state == 5) {
					$status = '<span class="inactive">'.JText::_('Pending approval').'</span> ';
				}
				
				$tags = $pt->get_tag_cloud(3, 1, $row->id);
	
			?>
						<tr class="<?php echo "row$k"; ?>">
							<td style="width: 10px;"><?php echo JHTML::_('grid.id', $i, $row->id, false, 'id' ); ?></td>
							<td style="width: 30px;"><?php echo $row->id; ?></td>
							<td style="width: 30px;"><?php echo '<img src="'.$thumb.'" width="30" height="30" alt="'.htmlentities($row->alias).'" />'; ?></td>
							<td>
								<a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>"><?php echo stripslashes($row->title); ?></a><br /><strong><?php echo stripslashes($row->alias); ?></strong>
								<?php if ($tags) { ?>
														<span class="project-tags block">
															<?php echo $tags; ?>
														</span>
								<?php } ?>
							</td>
							<td style="width: 20px;"><?php echo $ownerclass; ?></td>
							<td><?php echo $owner; ?></td>
							<td><?php echo $status; ?></td>						
							<td><?php echo ($row->private == 1) ? '<span class="private">&nbsp;</span>' : ''; ?></td>
							<td class="centeralign"><?php echo $row->activity; ?></td>
						</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
