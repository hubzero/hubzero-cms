<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class JobsHtml 
{
	//----------------------------------------------------------
	// Misc. 
	//----------------------------------------------------------
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	
	//-----------

	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}
	
	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//-----------

	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'" style"width:100%;">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}
	
	//-----------

	public function submenu($selected)
	{
		$subs = array('jobs'=>JText::_('Jobs'),'categories'=>JText::_('Categories'),'types'=>JText::_('Types'));
		
		$out = '<ul class="submenu">'.n;
		foreach($subs as $sub) {
			$out.= '<li>'.n;
			$out.= $sub == $selected ?  $sub : '<a href="">'.$sub.'</a>';
			$out.= '</li>'.n;
		}
		
		$out.= '</ul>'.n;
		return $out;
	
	}
	

	//----------------------------------------------------------
	// Browse Views
	//----------------------------------------------------------
	
	public function jobs ( $database, $rows, $pageNav, $option, $filters, $config ) 
	{
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
					<input type="text" name="search" id="search" value="<?php echo $filters['search']; ?>" />
				</label>
			
				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
			</fieldset>

			<table class="adminlist" summary="<?php echo JText::_('A list of jobs and their relevant data'); ?>">
				<thead>
					<tr>
						<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('Code'); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$filters['sort_Dir'], @$filters['sortby']); ?></th>
						<th><?php echo JText::_('Company & Location'); ?></th>
                        <th><?php echo JHTML::_('grid.sort', 'Status', 'status', @$filters['sort_Dir'], @$filters['sortby']); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Owner', 'adminposting', @$filters['sort_Dir'], @$filters['sortby']); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Added', 'added', @$filters['sort_Dir'], @$filters['sortby']); ?></th>
						<th><?php echo JText::_('Applications'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		$filterstring  = ($filters['sortby'])   ? '&amp;sort='.$filters['sortby']     : '';
		$filterstring .= '&amp;category='.$filters['category'];
		
		ximport('wiki.parser');
		$p = new WikiParser( 'jobs', $option, 'jobs.browse', 'jobs', 1);
		$now = date( "Y-m-d H:i:s" );
		
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
			
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			
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
							<a class="editlinktip hasTip" href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $info; ?>"><?php echo stripslashes($row->title); ?></a>
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

	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sortby']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	
	//-----------
	
	public function types( $rows, &$pageNav, $option, $filters ) 
	{

		?>
         <h3><?php echo JText::_('Job Types'); ?></h3>
		<form action="index.php" method="post" name="adminForm">
			
			<table class="adminlist" summary="<?php echo JText::_('A list of job types'); ?>">
				<thead>
					<tr>
						<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'category', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		$i = 0;
		foreach ($rows as $avalue => $alabel) 
		{
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $avalue ?>" onclick="isChecked(this.checked);" /></td>
						<td><?php echo $avalue; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edittype&amp;id[]=<?php echo $avalue; ?>"><?php echo $alabel; ?></a></td>
					</tr>
<?php
			$k = 1 - $k;
			$i++;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="types" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}


	//-----------
	
	public function categories( $rows, &$pageNav, $option, $filters ) 
	{

		?>
         <h3><?php echo JText::_('Job Categories'); ?></h3>
		<form action="index.php" method="post" name="adminForm">
			
			<table class="adminlist" summary="<?php echo JText::_('A list of job categories'); ?>">
				<thead>
					<tr>
						<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
                        <th width="8%" nowrap="nowrap">
							<?php echo JHTML::_('grid.sort', JText::_('Order'), 'ordernum', @$filters['sort_Dir'], @$filters['sort'] ); ?>
                        </th>
						<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'category', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td class="order"><?php echo $row->id; ?></td>
                        <td class="order" nowrap="nowrap">
							<input type="text" name="order[<?php echo $row->id; ?>]" size="5" value="<?php echo $row->ordernum; ?>"  class="text_area" style="text-align: center" />
                        </td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editcat&amp;id[]=<?php echo $row->id; ?>"><?php echo $row->category; ?></a></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="categories" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	
	//----------------------------------------------------------
	// Edit Views
	//----------------------------------------------------------
	
	public function editType( $row, $option) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			
			if (pressbutton == 'canceltype') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.category.value == '') {
				alert( 'Type must have a title' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<p style="color:#FF0000;"><?php echo JText::_('Warning: changing the type title will affect all currently available job postings with this type.'); ?></p>
		<form action="index.php" method="post" id="adminForm" name="adminForm">			
			<fieldset class="adminform">
				<legend><?php echo JText::_('Edit type title'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="type"><?php echo JText::_('Type Title'); ?>: <span class="required">*</span></label></td>
							<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $row->category; ?>" /></td>
						</tr>
					</tbody>
				</table>
			
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="savetype" />
			</fieldset>
		</form>
		<?php
	}
	
	//-----------
	
	public function editCat( $row, $option) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			
			if (pressbutton == 'canceltype') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.category.value == '') {
				alert( 'Category must have a title' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<p style="color:#FF0000;"><?php echo JText::_('Warning: changing the category title will affect all currently available job postings in this category.'); ?></p>
		<form action="index.php" method="post" id="adminForm" name="adminForm">			
			<fieldset class="adminform">
				<legend><?php echo JText::_('Edit category title'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="type"><?php echo JText::_('Category Title'); ?>: <span class="required">*</span></label></td>
							<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $row->category; ?>" /></td>
						</tr>
                        <tr>
							<td class="key"><label for="description"><?php echo JText::_('Description'); ?>: </label></td>
							<td><input type="text" name="description" id="description"  maxlength="255" value="<?php echo $row->description; ?>" /></td>
						</tr>
					</tbody>
				</table>
			
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="savecat" />
			</fieldset>
		</form>
		<?php
	}
	
	//-----------
	
	public function editJob( $config, $row, $job, $types, $cats, $employer, $option, $isnew=0, $return, $subscription ) 
	{
		
		$usonly = $config->get('usonly');
		$row->companyLocationCountry = !$isnew ? $row->companyLocationCountry : htmlentities(JText::_('United States'));
		$row->code = !$isnew ? $row->code : JText::_('N/A (new job)');
		
		$startdate = ($row->startdate && $row->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$row->startdate, '%Y-%m-%d') : '';
		$closedate = ($row->closedate && $row->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$row->closedate, '%Y-%m-%d') : '';
		$opendate = ($row->opendate && $row->opendate !='0000-00-00 00:00:00') ? JHTML::_('date',$row->opendate, '%Y-%m-%d') : '';
		
		$status = !$isnew ? $row->status : 4; // draft mode
		
		$row->description = trim(stripslashes($row->description));
		$row->description = preg_replace('/<br\\s*?\/??>/i', "", $row->description);
		$row->description = JobsHtml::txt_unpee($row->description);
		$employerid = $isnew ? 1 : $job->employerid;
		
		// Get the published status			
			switch ($row->status) 
			{
				case 0: 
					$alt   = 'Pending approval';
					$class = 'post_pending';
					break;
				case 1: 
					$alt 	=  $job->inactive 
							? JText::_('Invalid Subscription') 
							: JText::_('Active'); 
					$class  = $job->inactive 
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

		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('jobForm');

			if (pressbutton == 'cancel') {
				form.task.value = 'cancel';
				form.submit();
				return;
			}
			
			// do field validation
			if (form.title.value == ''){
				alert( 'Job must have a title.' );
			} else if (form.description.value == ''){
				alert( 'Job must have a description.' );
			} else if (form.companyLocation.value == ''){
				alert( 'Job must have a location.' );
			} else if (form.companyName.value == ''){
				alert( 'Job must have a company name.' );
			} else {
				form.task.value = 'save';
				form.submit();
				return;
			}
		}

		</script>

		<form action="index.php" method="post" id="jobForm" name="jobForm" >
        <div class="col width-60">
				<fieldset class="adminform">
		
				<table class="admintable">
					<caption style="text-align: left; font-weight: bold;"><?php echo JText::sprintf('Job #%s', $row->code); ?></caption>
					<tbody>
						<tr>
							<td class="key"><label for="title"><?php echo JText::_('Title'); ?>:</label></td>
							<td><input type="text" name="title" id="title" size="60" maxlength="200" value="<?php echo htmlentities(stripslashes($row->title), ENT_QUOTES); ?>" /></td>
						</tr>
						
						<tr>
							<td class="key"><label for="companyName"><?php echo JText::_('Company Name'); ?>:</label></td>
							<td><input type="text" name="companyName" id="companyName" size="60" maxlength="200" value="<?php echo $row->companyName; ?>" /></td>
                        </tr>
                        <tr>
							<td class="key"><label for="companyWebsite"><?php echo JText::_('Company URL'); ?>:</label></td>
							<td><input type="text" name="companyWebsite" id="companyWebsite" size="60" maxlength="200" value="<?php echo $row->companyWebsite; ?>" /></td>
                        </tr>
                        <tr>
							<td class="key"><label for="companyLocation"><?php echo JText::_('Company Location'); ?> <br />(<?php echo JText::_('City, State'); ?>):</label></td>
							<td><input type="text" name="companyLocation" id="companyLocation" size="60" maxlength="200" value="<?php echo $row->companyLocation; ?>" /></td>
						</tr>
                        <tr>
							<td class="key"><label for="companyLocationCountry"><?php echo JText::_('Country'); ?>:</label></td>
							<td>
                            <?php if($usonly) { ?>
                            	<?php echo JText::_('United States'); ?>
                            	<p class="hint"><?php echo JText::_('Only US-based jobs can be advertised on this site.'); ?></p>
                                <input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
                             <?php } else { 
							 	$out = "\t\t\t\t".'<select name="companyLocationCountry" id="companyLocationCountry">'."\n";
							 	$out .= "\t\t\t\t".' <option value="">(select from list)</option>'."\n";
							 	$countries = getcountries();
								foreach($countries as $country) {
									$out .= "\t\t\t\t".' <option value="' . htmlentities($country['name']) . '"';
									if($country['name'] == $row->companyLocationCountry) {
										$out .= ' selected="selected"';
									}
								$out .= '>' . htmlentities($country['name']) . '</option>'."\n";
								}
								$out .= t.t.t.t.'</select>'.n;
							 	echo $out;
							 ?>
                             <?php } ?>
                            </td>
						</tr>
                        <tr>
				    		<td class="key" style="vertical-align:top;"><label for="description"><?php echo JText::_('Job Description'); ?>:</label></td>
				   			<td>
                            	<p class="hint"><?php echo JText::_('Wiki formatting is enabled.'); ?></p>
                            	<textarea name="description" id="description"  cols="55" rows="30"><?php echo (stripslashes($row->description)); ?></textarea>
                            </td>
				  		</tr>
                         <tr>
							<td colspan="2"><h4><?php echo JText::_('Job Specifics'); ?></h4></td>
						</tr>
                         <tr>
							<td class="key"><label for="cid"><?php echo JText::_('Job Category'); ?>:</label></td>
							<td><?php echo JobsHtml::formSelect('cid', $cats, $row->cid, '', ''); ?></td>
						</tr>
                         <tr>
							<td class="key"><label for="type"><?php echo JText::_('Job Type'); ?>:</label></td>
							<td><?php echo JobsHtml::formSelect('type', $types, $row->type, '', ''); ?></td>
						</tr>
                         <tr>
							<td class="key"><label for="startdate"><?php echo JText::_('Position Start Date'); ?>:</label></td>
							<td>
                            	<p class="hint"><?php echo JText::_('Date format: yyyy-mm-dd.'); ?></p>
                            	<input type="text" name="startdate" id="startdate" size="60" maxlength="10" value="<?php echo $startdate; ?>" />
                            </td>
                        </tr>
                         <tr>
							<td class="key"><label for="closedate"><?php echo JText::_('Applications Due'); ?>:</label></td>
							<td>
                            	<p class="hint"><?php echo JText::_('Date format: yyyy-mm-dd.'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('- Will default to \'ASAP\' when left blank'); ?></p>
                            	<input type="text" name="closedate" id="closedate" size="60" maxlength="10" value="<?php echo $closedate; ?>" />
                            </td>
                        </tr>
                        <tr>
							<td class="key"><label for="applyExternalUrl"><?php echo JText::_('External URL <br />for a job application <br />(optional)'); ?>:</label></td>
							<td>
                            	<p class="hint"><?php echo JText::_('Include http://'); ?></p>
                            	<input  type="text" name="applyExternalUrl" size="60" maxlength="100" value="<?php echo $row->applyExternalUrl; ?>" />
                            </td>
						</tr>
                       <tr>
							<td class="key"><label><?php echo JText::_('Allow internal application'); ?>:</label></td>
							<td><input type="checkbox" class="option" name="applyInternal"  size="10" maxlength="10" value="1" <?php if($row->applyInternal) { echo 'checked="checked"'; } ?>  /></td>
						</tr>
                         <tr>
							<td colspan="2"><h4><?php echo JText::_('Contact Information').'<br />'.JText::_('(optional)'); ?></h4></td>
						</tr>
                        <tr>
							<td class="key"><label for="contactName"><?php echo JText::_('Contact Name'); ?>:</label></td>
							<td><input type="text" name="contactName" id="contactName" size="60" maxlength="100" value="<?php echo $row->contactName; ?>" /></td>
                        </tr>
                         <tr>
							<td class="key"><label for="contactEmail"><?php echo JText::_('Contact Email'); ?>:</label></td>
							<td><input type="text" name="contactEmail" id="contactEmail" size="60" maxlength="100" value="<?php echo $row->contactEmail; ?>" /></td>
                        </tr>
                        <tr>
							<td class="key"><label for="contactPhone"><?php echo JText::_('Contact Phone'); ?>:</label></td>
							<td><input type="text" name="contactPhone" id="contactPhone" size="60" maxlength="100" value="<?php echo $row->contactPhone; ?>" /></td>
                        </tr>
						
					</tbody>
				</table>
				
			</fieldset>
			
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('Manage this Job'); ?></legend>

				<table class="admintable">
					<tbody>
					<?php if(!$isnew) { ?>
                        <tr>
							<td class="key"><label><?php echo JText::_('Added'); ?>:</label></td>
							<td><?php echo $row->added ?></td>
						</tr>
                        <tr>
							<td class="key"><label><?php echo JText::_('Added by'); ?>:</label></td>
							<td><?php echo $row->addedBy; if($job->employerid == 1) { echo ' '.JText::_('(admin subscription)') ; } ?></td>
						</tr>
                        <tr>
							<td class="key"><label><?php echo JText::_('Last changed'); ?>:</label></td>
							<td>
							<?php echo ($job->edited && $job->edited !='0000-00-00 00:00:00') ? $job->edited : 'N/A'; ?>
                            </td>
						</tr>
                        <tr>
							<td class="key"><label><?php echo JText::_('Last changed by'); ?>:</label></td>
							<td>
                            <?php echo ($job->editedBy) ? $job->editedBy : 'N/A'; ?>
							</td>
						</tr>
                        <?php if(isset($subscription->id)) { ?>
                        <tr>
							<td class="key"><label><?php echo JText::_('User subscription'); ?>:</label></td>
							<td>
								<?php echo $subscription->code; 
								if(!$job->inactive) { echo ' '.JText::_('(active').' '.JText::_(', expires').' '.$subscription->expires.')';  } ?>
                            </td>
						</tr>
                        <?php } ?>
						<tr>
							<td class="key"><label><?php echo JText::_('Job Ad Status'); ?>:</label></td>
							<td><?php echo $alt; ?></td>
						</tr>
                         <?php if($opendate) { ?>
                        <tr>
							<td class="key"><label><?php echo JText::_('Job Ad Published'); ?>:</label></td>
							<td><?php echo $row->opendate; ?></td>
						</tr>
                        <?php } ?>
                        <tr>
							<td class="key"><label><?php echo JText::_('Change Status / Take Action'); ?>:</label></td>
							<td><input type="radio" name="action" value="message" /><?php echo JText::_('No action / Send message to author'); ?></td>
						</tr>
                         <tr>
                            <th></th>
                            <td>
                            	<?php if($row->status != 1) { ?>
                            	<input type="radio" name="action" value="publish" /> <?php echo JText::_('Publish Ad'); ?>
                                <?php } else { ?>
                                <input type="radio" name="action" value="unpublish" /> <?php echo JText::_('Unpublish Ad'); ?>
                                <?php } ?>
                            </td>
                         </tr>
                         <tr>
                            <th></th>
                            <td><input type="radio" name="action" value="delete" /> <?php echo JText::_('Delete Ad'); ?></td>
                         </tr>
                         <tr>
                        	<th></th>
                        	<td><?php echo JText::_('Message to author'); ?>: <br /><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
                      	</tr>
                       <?php } else { ?>
                       	<tr>
                            <td><?php echo JText::_('This is a new job ad. Please save it as draft before admin option become available.'); ?></td>
                         </tr>
                       <?php } ?>
                      
					</tbody>
				</table>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="isnew" value="<?php echo $isnew; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="employerid" value="<?php echo $employerid; ?>" />
            <input type="hidden" name="status" value="<?php echo $status; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="sortby" value="<?php echo $return['sortby']; ?>" />

			</fieldset>
			</div>
			<div class="clr"></div>		
            	
			
		</form>
		<?php
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function media( $dirPath, $listdir, $subdir, $path, $error='' ) 
	{
		if ($error) {
			echo ResourcesHtml::error($error);
		}
		?>
		<script type="text/javascript">
		function dirup()
		{
			var urlquery = frames['imgManager'].location.search.substring(1);
			var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
			var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
			frames['imgManager'].location.href='index3.php?option=com_resources&task=listfiles&listdir=' + listdir;
		}

		function goUpDir()
		{
			var listdir = document.getElementById('listdir');
			var selection = document.forms[0].dirPath;
			var dir = selection.options[selection.selectedIndex].value;
			frames['imgManager'].location.href='index3.php?option=com_resources&task=listfiles&listdir=' + listdir.value +'&subdir='+ dir;
		}
		</script>
		
		<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
			<p>path = <?php echo $path; ?></p>
			
			<fieldset>
				<label>
					Directory
					<?php echo $dirPath;?>
				</label>
		
				<div id="themanager" class="manager">
					<iframe src="index.php?option=com_resources&amp;task=listfiles&amp;tmpl=component&amp;listdir=<?php echo $listdir; ?>&amp;subdir=<?php echo $subdir; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
				</div>
			</fieldset>
			
			<fieldset>
				<table>
					<tbody>
						<tr>
							<td><label for="upload"><?php echo JText::_('Upload'); ?></label></td>
							<td><input type="file" name="upload" id="upload" /></td>
						</tr>
						<tr>
							<td> </td>
							<td><input type="checkbox" name="batch" id="batch" value="1" /> <label for="batch"><?php echo JText::_('Unpack file (.zip, .tar, etc)'); ?></label></td>
						</tr>
						<tr>
							<td><label for="foldername"><?php echo JText::_('Create Directory'); ?></label></td>
							<td><input type="text" name="foldername" id="foldername" /></td>
						</tr>
						<tr>
							<td> </td>
							<td><input type="submit" value="<?php echo JText::_('Create or Upload'); ?>" /></td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="option" value="com_resources" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $listdir; ?>" />
				<input type="hidden" name="task" value="upload" />
			</fieldset>
		</form>
		<?php
	}

	//-----------

	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		echo '<p>'.JText::_('No Files Found').'</p>'.n;
	}

	//-----------

	public function draw_no_dir() 
	{
		echo ResourcesHtml::error( JText::_('Configuration Problem: base directory does not exist.') ).n;
	}

	//-----------

	public function draw_table_header() 
	{
		echo t.'<form action="index2.php" method="post" name="filelist" id="filelist">'.n;
		echo t.t.'<table border="0" cellpadding="0" cellspacing="0">'.n;
		echo t.t.t.'<tbody>'.n;
	}

	//-----------

	public function draw_table_footer() 
	{
		echo t.t.t.'</tbody>'.n;
		echo t.t.'</table>'.n;
		echo t.'</form>'.n;
	}

	//-----------

	public function show_dir( $option, $base, $path, $dir, $listdir, $subdir ) 
	{
		$num_files = ResourcesHtml::num_files($base.$path);

		// Fix for Bug [0000577]
		if ($listdir=='/') {
			$listdir='';
		}
		?>
			<tr>
				<td><img src="components/<?php echo $option; ?>/images/folder.gif" alt="<?php echo $dir; ?>" width="16" height="16" /></td>
				<td width="100%" style="padding-left: 0;"><?php echo $dir; ?></td>
				<td><a href="index3.php?option=com_resources&amp;task=deletefolder&amp;delFolder=<?php echo $path; ?>&amp;listdir=<?php echo $listdir; ?>&amp;subdir=<?php echo $subdir; ?>" target="filer" onClick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);" title="<?php echo JText::_('Delete'); ?>"><img src="components/<?php echo $option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('Delete'); ?>" /></a></td>
			</tr>
		<?php
	}

	//-----------

	public function show_doc( $option, $doc, $listdir, $icon, $subdir ) 
	{
		$subdird = ($subdir && $subdir != '/') ? $subdir.'/' : '/';
		?>
			<tr>
				<td><input type="radio" name="slctdfile" value="<?php echo $listdir.$subdird.$doc; ?>" /></td>
				<!-- <td><img src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" width="16" height="16" /></td> -->
				<td width="100%" style="padding-left: 0;"><?php echo $doc; ?></td>
				<td><a href="index3.php?option=com_resources&amp;task=deletefile&amp;delFile=<?php echo $doc; ?>&amp;listdir=<?php echo $listdir; ?>&amp;subdir=<?php echo $subdir; ?>" target="filer" onclick="return deleteImage('<?php echo $doc; ?>');" title="<?php echo JText::_('Delete'); ?>"><img src="components/<?php echo $option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('Delete'); ?>" /></a></td>
			</tr>
		<?php
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} else if ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' <abbr title="kilobytes">Kb</abbr>';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' <abbr title="megabytes">Mb</abbr>';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) {
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}

}
?>
