<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::preferences('com_jobs', '550');
JToolBarHelper::spacer();
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
//JToolBarHelper::deleteList('remove');

JHTML::_('behavior.tooltip');
//jimport('joomla.html.html.grid');
include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');
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

<h3><?php echo JText::_('Job Postings'); ?></h3>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label for="search">
			<?php echo JText::_('Search'); ?>: 
			<input type="text" name="search" id="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('A list of jobs and their relevant data'); ?>">
		<thead>
			<tr>
				<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('Code'); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th><?php echo JText::_('Company & Location'); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Status', 'status', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Owner', 'adminposting', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Added', 'added', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th><?php echo JText::_('Applications'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$filterstring  = ($this->filters['sortby'])   ? '&amp;sort='.$this->filters['sortby']     : '';
$filterstring .= '&amp;category='.$this->filters['category'];

$now = date( "Y-m-d H:i:s" );

$database =& JFactory::getDBO();

$jt = new JobType( $database );
$jc = new JobCategory( $database );
	
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row =& $this->rows[$i];
	
	$admin = $row->employerid == 1 ? 1 : 0;
	$adminclass = $admin ? 'class="adminpost"' : '';
	
	$curtype = $row->type > 0 ? $jt->getType($row->type) : '';
	$curcat = $row->cid > 0 ? $jc->getCat($row->cid) : '';
	
	// Build some publishing info
	$info  = JText::_('Created').': '.JHTML::_('date',$row->added, '%d&nbsp;%b&nbsp;%y').'<br />';
	$info .= JText::_('Created by').': '.$row->addedBy;
	$info .= $admin ? ' '.JText::_('(admin)') : '';
	$info .= '<br />';
	$info .= JText::_('Category').': '.$curcat.'<br />';
	$info .= JText::_('Type').': '.$curtype.'<br />';
	
	// Get the published status			
	switch ($row->status) 
	{
		case 0: 
			$alt   = 'Pending approval';
			$class = 'post_pending';
			break;
		case 1: 
			$alt 	=  $row->inactive 
					? JText::_('Invalid Subscription') 
					: JText::_('Active'); 
			$class  = $row->inactive 
					? 'post_invalidsub'
					: 'post_active';  
			break;
		case 2: 
			$alt   = 'Deleted';
			$class = 'post_deleted';
			break;
		case 3: 
			$alt   = 'Inactive';
			$class = 'post_inactive';
			break;
		case 4: 
			$alt   = 'Draft';
			$class = 'post_draft';
			break;
		default:
			$alt   = '-';
			$class = '';
			break;
	}
	
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo JHTML::_('grid.id', $i, $row->id, false, 'id' ); ?></td>
				<td><?php echo $row->code; ?></td>
				<td>
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $info; ?>"><?php echo stripslashes($row->title); ?></a>
				</td>
                <td><?php echo $row->companyName,', '.$row->companyLocation; ?></td>
                <td><span class="<?php echo $class;?>"><?php echo $alt; ?></span></td>	
                <td><span <?php echo $adminclass; ?>>&nbsp;</span></td>
                <td><?php echo JHTML::_('date',$row->added, '%d&nbsp;%b&nbsp;%y'); ?></td>											
				<td><?php echo $row->applications; ?></td>
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
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>