<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class ResourcesHtml 
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

	public function statusKey()
	{
		?>
			<p><?php echo JText::_('Published status: (click icon above to toggle state)'); ?></p>
			<ul class="key">
				<li class="draftinternal"><span>draft (internal)</span> = <?php echo JText::_('Draft (internal production)'); ?></li>
				<li class="draftexternal"><span>draft (external)</span> = <?php echo JText::_('Draft (user created)'); ?></li>
				<li class="new"><span>new</span> = <?php echo JText::_('New, awaiting approval'); ?></li>
				<li class="pending"><span>pending</span> = <?php echo JText::_('Published, but is Coming'); ?></li>
				<li class="published"><span>current</span> = <?php echo JText::_('Published and is Current'); ?></li>
				<li class="expired"><span>finished</span> = <?php echo JText::_('Published, but has Finished'); ?></li>
				<li class="unpublished"><span>unpublished</span> = <?php echo JText::_('Unpublished'); ?></li>
				<li class="deleted"><span>deleted</span> = <?php echo JText::_('Delete/Removed'); ?></li>
			</ul>
		<?php
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

	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	public function build_path( $date, $id, $base='' )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = ResourcesHtml::niceidformat( $id );
		
		$path = $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	
		//return $base.DS.$dir_id;
		return $path;
	}
	
	//-----------

	public function writeRating( $rating ) 
	{
		switch ($rating) 
		{
			case 0.5: $class = ' half';      break;
			case 1:   $class = ' one';       break;
			case 1.5: $class = ' onehalf';   break;
			case 2:   $class = ' two';       break;
			case 2.5: $class = ' twohalf';   break;
			case 3:   $class = ' three';     break;
			case 3.5: $class = ' threehalf'; break;
			case 4:   $class = ' four';      break;
			case 4.5: $class = ' fourhalf';  break;
			case 5:   $class = ' five';      break;
			case 0:   
			default:  $class = ' none';      break;
		}

		return '<p class="avgrating'.$class.'"><span>Rating: '.$rating.' out of 5 stars</span></p>';		
	}

	//----------------------------------------------------------
	// Form <select> builders
	//----------------------------------------------------------
	
	public function selectAccess($as, $value)
	{
		$as = explode(',',$as);
		$html  = '<select name="access">'.n;
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			$html .= t.'<option value="'.$i.'"';
			if ($value == $i) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.trim($as[$i]).'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------
	
	public function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner"';
		if (!$groups) {
			$html .= ' disabled="disabled"';
		}
		$html .= '>'.n;
		$html .= ' <option value="">'.JText::_('Select group ...').'</option>'.n;
		if ($groups) {
			foreach ($groups as $group)
			{
				$html .= ' <option value="'.$group->cn.'"';
				if ($value == $group->cn) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$group->description .'</option>'.n;
			}
		}
		$html .= '</select>'.n;
		return $html;
	}

	//-----------

	public function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>'.JText::_('[ none ]').'</option>'.n;
		foreach ($array as $anode) 
		{
			$selected = ($anode->id == $value || $anode->type == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->type.'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}

	//-----------

	public function selectType($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		if ($shownone != '') {
			$html .= t.'<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>'.$shownone.'</option>'.n;
		}
		if ($skip) {
			$skips = explode(',',$skip);
		} else {
			$skips = array();
		}
		foreach ($arr as $anode) 
		{
			if (!in_array($anode->id, $skips)) {
				$selected = ($value && ($anode->id == $value || $anode->type == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= t.'<option value="'.$anode->id.'"'.$selected.'>'.$anode->type.'</option>'.n;
			}
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------

	public function selectAuthors($rows, $authnames, $attribs, $option)
	{
		$authIDs = array();
		
		$document =& JFactory::getDocument();
		$document->addScript('components/'.$option.'/admin.xsortables.js');
		$document->addScript('components/'.$option.'/admin.resources.js');
			
		$html = '';
		//$html  = '<script type="text/javascript" src="/components/'.$this->_option.'/admin.xsortables.js"></script>'.n;
		//$html .= '<script type="text/javascript" src="/components/'.$this->_option.'/admin.resources.js"></script>'.n;
		/*$html .= '<select name="authid" id="authid">'.n;
		$html .= t.'<option value="">'.JText::_('Select author...').'</option>'.n;
		if ($rows) {
			foreach ($rows as $row)
			{
				$html .= t.'<option value="'.$row->uidNumber.'">'.$row->surname.', '.$row->givenName;
				$html .= ($row->middleName) ? ' '.$row->middleName : '';
				$html .= ' ('.$row->organization.')';
				$html .= '</option>'.n;
			}
		}
		$html .= '</select> '.n;*/
		$html .= 'User ID: <input type="text" name="authid" id="authid" value="" /> ';
		$html .= t.t.'<select name="authrole" id="authrole">'.n;
		$html .= t.t.t.'<option value="">Role...</option>'.n;
		$html .= t.t.t.'<option value="submitter">submitter</option>'.n;
		$html .= t.t.t.'<option value="editor">editor</option>'.n;
		$html .= t.t.'</select>'.n;
		$html .= '<input type="button" name="addel" id="addel" onclick="HUB.Resources.addAuthor();" value="'.JText::_('Add').'" />';
		$html .= '<ul id="author-list">'.n;
		if ($authnames != NULL) {
			foreach ($authnames as $authname) 
			{
				$name = $authname->givenName .' ';
				if ($authname->middleName != null) {
					$name .= $authname->middleName .' ';
				}
				$name .= $authname->surname.' ('.$authname->id.') [ <a href="#" onclick="HUB.Resources.removeAuthor(this);return false;">'.JText::_('remove').'</a> ]';
			
				$authIDs[] = $authname->id;
			
				$html .= t.'<li id="author_'.$authname->id.'"><span class="handle">'.JText::_('DRAG HERE').'</span> '. $name;
				$html .= '<br />'.JText::_('Affiliation').': <input type="text" name="attrib['.$authname->id.']" value="'. $attribs->get( $authname->id, '' ) .'" />';
				$html .= t.t.'<select name="'.$authname->id.'_role">'.n;
				$html .= t.t.t.'<option value="">Role...</option>'.n;
				$html .= t.t.t.'<option value="submitter"';
				$html .= ($authname->role == 'submitter') ? ' selected="selected"' : '';
				$html .= '>submitter</option>'.n;
				$html .= t.t.t.'<option value="editor"';
				$html .= ($authname->role == 'editor') ? ' selected="selected"' : '';
				$html .= '>editor</option>'.n;
				$html .= t.t.'</select>'.n;
				$html .= '</li>'.n;
			}
		}
		$authIDs = implode(',',$authIDs);
		$html .= '</ul>'.n;
		$html .= '<input type="hidden" name="old_authors" id="old_authors" value="'.$authIDs.'" />'.n;
		$html .= '<input type="hidden" name="new_authors" id="new_authors" value="'.$authIDs.'" />'.n;
		
		return $html;
	}

	//----------------------------------------------------------
	// Browse Views
	//----------------------------------------------------------
	
	public function resources( $database, $rows, $pageNav, $option, $filters, $types ) 
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

		<form action="index.php" method="post" name="adminForm">
			<fieldset id="filter">
				<label for="search">
					<?php echo JText::_('Search'); ?>: 
					<input type="text" name="search" id="search" value="<?php echo $filters['search']; ?>" />
				</label>
			
				<label for="status">
					<?php echo JText::_('Status'); ?>:
					<select name="status" id="status">
						<option value="all"<?php echo ($filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('[ all ]'); ?></option>
						<option value="2"<?php echo ($filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Draft (user created)'); ?></option>
						<option value="5"<?php echo ($filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Draft (internal)'); ?></option>
						<option value="3"<?php echo ($filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Pending'); ?></option>
						<option value="0"<?php echo ($filters['status'] == 0 && $filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Unpublished'); ?></option>
						<option value="1"<?php echo ($filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Published'); ?></option>
						<option value="4"<?php echo ($filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Deleted'); ?></option>
					</select>
				</label>
			
				<label for="type">
					<?php echo JText::_('Type'); ?>:
					<?php echo $types; ?>
				</label>
			
				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
			</fieldset>

			<table class="adminlist" summary="<?php echo JText::_('A list of resources and their types, published status, access levels, and other relevant data'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
						<th><?php echo JText::_('Status'); ?></th>
						<th><?php echo JText::_('Access'); ?></th>
						<th><?php echo JText::_('License'); ?></th>
						<th><?php echo JText::_('Type'); ?></th>
						<th><?php echo JText::_('Children'); ?></th>
						<th><?php echo JText::_('Tags'); ?></th>
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
		$filterstring  = ($filters['sort'])   ? '&amp;sort='.$filters['sort']     : '';
		$filterstring .= '&amp;status='.$filters['status'];
		$filterstring .= ($filters['type'])   ? '&amp;type='.$filters['type']     : '';
		
		$rt = new ResourcesTags( $database );
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			
			$rparams =& new JParameter( $row->params );
			$license = $rparams->get('license');
			
			// Build some publishing info
			$info  = JText::_('Created').': '.$row->created.'<br />';
			$info .= JText::_('Created by').': '.$row->created_by.'<br />';
			
			// Get the published status
			$now = date( "Y-m-d H:i:s" );
			switch ($row->published) 
			{
				case 0: 
					$alt   = 'Unpublish';
					$class = 'unpublished';
					$task  = 'publish';
					break;
				case 1: 
					if ($now <= $row->publish_up) {
						$alt   = 'Pending';
						$class = 'pending';
						$task  = 'unpublish';
					} else if ($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") {
						$alt   = 'Published';
						$class = 'published';
						$task  = 'unpublish';
					} else if ($now > $row->publish_down) {
						$alt   = 'Expired';
						$class = 'expired';
						$task  = 'unpublish';
					}
					$info .= JText::_('Published').': '.$row->publish_up.'<br />';
					break;
				case 2: 
					$alt   = 'Draft (user created)';
					$class = 'draftexternal';
					$task  = 'publish';
					break;
				case 3: 
					$alt   = 'New';
					$class = 'new';
					$task  = 'publish';
					break;
				case 4: 
					$alt   = 'Delete';
					$class = 'deleted';
					$task  = 'publish';
					break;
				case 5: 
					$alt   = 'Draft (internal production)';
					$class = 'draftinternal';
					$task  = 'publish';
					break;
				default:
					$alt   = '-';
					$class = '';
					$task  = '';
					break;
			}
			
			switch ($row->access)
			{
				case 0: 
					$color_access = 'style="color: green;"';
					$task_access  = 'accessregistered';
					break;
				case 1: 
					$color_access = 'style="color: red;"';
					$task_access  = 'accessspecial';
					break;
				case 2:
					$color_access = 'style="color: black;"';
					$task_access  = 'accessprotected';
					break;
				case 3:
					$color_access = 'style="color: blue;"';
					$task_access  = 'accessprivate';
					$row->groupname = 'Protected';
					break;
				case 4:
					$color_access = 'style="color: red;"';
					$task_access  = 'accesspublic';
					$row->groupname = 'Private';
					break;
			}
			
			// Get the tags on this item
			$tags = count($rt->getTags($row->id, 0, 0, 1));
			
			// See if it's checked out or not
			if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00') {
				$checked = JHTMLGrid::_checkedOut( $row );
				$info .= ($row->checked_out_time != '0000-00-00 00:00:00') 
						 ? JText::_('Checked out').': '.JHTML::_('date', $row->checked_out_time, '%d %b, %Y').'<br />' 
						 : '';
				if ($row->editor) {
					$info .= JText::_('Checked out by').': '.$row->editor;
				}
			} else {
				$checked = JHTML::_('grid.id', $i, $row->id, false, 'id' );
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $checked; ?></td>
						<td><?php echo $row->id; ?></td>
						<td>
							<a class="editlinktip hasTip" href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $info; ?>"><?php echo stripslashes($row->title); ?></a><br />
							<!-- <small><strong>Tags:</strong> <?php //echo $tags; ?></small> -->
						</td>
						<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; echo $filterstring; ?>" title="Set this to <?php echo $task;?>"><span><?php echo $alt; ?></span></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; echo $filterstring; ?>" <?php echo $color_access; ?> title="Change Access"><?php echo $row->groupname;?></a></td>
						<td><?php echo $license; ?></td>
						<td><?php echo $row->typetitle; ?></td>
						<td><?php echo $row->children; if($row->children > 0) { ?> &nbsp; <a href="index.php?option=com_resources&amp;task=children&amp;pid=<?php echo $row->id; ?>" title="View this item's children">View</a><?php } else { ?> &nbsp; <a href="index.php?option=com_resources&amp;task=addchild&amp;pid=<?php echo $row->id;  ?>" title="Add a child">[ + ]</a><?php } ?></td>
						<td><?php echo $tags; if($tags > 0) { ?> &nbsp; <a href="index.php?option=com_resources&amp;task=edittags&amp;id=<?php echo $row->id; ?>" title="View this item's tags">View</a><?php } else { ?> &nbsp; <a href="index.php?option=com_resources&amp;task=edittags&amp;id=<?php echo $row->id;  ?>" title="Add a tag">[ + ]</a><?php } ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>

			<?php ResourcesHtml::statusKey(); ?>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	//-----------

	public function children( $rows, $pageNav, $option, $filters, $sections, $mtask, $pid, $parent=NULL ) 
	{
		JHTML::_('behavior.tooltip');
		include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');
		
		if ($pid != '-1') {
			$colspan = 9;
			if ($parent->type == 5) {
				$colspan = 10;
			}
		} else {
			$colspan = 7;
		}
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

		<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php
			if ($pid != '-1') {
				//echo '<h3 class="extranav" style="text-align: left;"><a href="index2.php?option='.$option.'&amp;task=edit&amp;id[]='.$pid.'" title="Edit this resource">'. stripslashes($parent->title) .'</a> <span>[ <a href="index2.php?option='.$option.'&amp;type='.$parent->type.'">'.$parent->type.'</a> ]</span></h3>'."\n";
				echo '<h3><a href="index.php?option='.$option.'&amp;task=edit&amp;id[]='.$pid.'">'. stripslashes($parent->title) .'</a></h3>'."\n";
			}
?>

			<!-- <fieldset id="filter">
				<label for="search">
					Search: 
					<input type="text" name="search" id="search" value="<?php echo $filters['search']; ?>" />
				</label>

				<label for="sort">
					Sort: 
					<select name="sort" id="sort">
						<option value="ordering"<?php if($filters['sort'] == 'ordering') { echo ' selected="selected"'; } ?>>Ordering</option>
						<option value="created DESC"<?php if($filters['sort'] == 'created') { echo ' selected="selected"'; } ?>>Date</option>
						<option value="title"<?php if($filters['sort'] == 'title') { echo ' selected="selected"'; } ?>>Title</option>
						<option value="id"<?php if($filters['sort'] == 'id') { echo ' selected="selected"'; } ?>>ID number</option>
					</select>
				</label>
			
				<label for="status">
					Status:
					<select name="status" id="status">
						<option value="all"<?php echo ($filters['status'] == 'all') ? ' selected="selected"' : ''; ?>>[ all ]</option>
						<option value="2"<?php echo ($filters['status'] == 2) ? ' selected="selected"' : ''; ?>>Draft (user created)</option>
						<option value="5"<?php echo ($filters['status'] == 5) ? ' selected="selected"' : ''; ?>>Draft (internal)</option>
						<option value="3"<?php echo ($filters['status'] == 3) ? ' selected="selected"' : ''; ?>>Pending</option>
						<option value="0"<?php echo ($filters['status'] == 0 && $filters['status'] != 'all') ? ' selected="selected"' : ''; ?>>Unpublished</option>
						<option value="1"<?php echo ($filters['status'] == 1) ? ' selected="selected"' : ''; ?>>Published</option>
						<option value="4"<?php echo ($filters['status'] == 4) ? ' selected="selected"' : ''; ?>>Deleted</option>
					</select>
				</label>

				<input type="submit" name="filter_submit" id="filter_submit" value="Go" />
			</fieldset> -->

			<table class="adminlist" summary="<?php echo JText::_('A list of resources and their types, published status, access levels, and other relevant data'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('ID'); ?></th>
						<th><?php echo JText::_('Title'); ?></th>
						<th><?php echo JText::_('Status'); ?></th>
						<th><?php echo JText::_('Access'); ?></th>
						<th><?php echo JText::_('Type'); ?></th>
<?php if ($pid != '-1') { ?>
						<th colspan="3"><?php echo JText::_('Reorder'); ?></th>
	<?php if ($parent->type == 4) { ?>
						<th><?php echo JText::_('Section'); ?></th>
	<?php } ?>
<?php } ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo $colspan; ?>"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		$filterstring  = '&amp;pid='.$pid;
		$filterstring .= ($filters['sort'])   ? '&amp;sort='.$filters['sort']     : '';
		$filterstring .= '&amp;status='.$filters['status'];

		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			
			// Build some publishing info
			$info  = JText::_('Created').': '.$row->created.'<br />';
			$info .= JText::_('Created by').': '.$row->created_by.'<br />';
			
			$now = date( "Y-m-d H:i:s" );
			switch ($row->published) 
			{
				case 0: 
					$alt   = 'Unpublish';
					$class = 'unpublished';
					$task  = 'publish';
					break;
				case 1: 
					if ($now <= $row->publish_up) {
						$alt   = 'Pending';
						$class = 'pending';
						$task  = 'unpublish';
					} else if ($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") {
						$alt   = 'Published';
						$class = 'published';
						$task  = 'unpublish';
					} else if ($now > $row->publish_down) {
						$alt   = 'Expired';
						$class = 'expired';
						$task  = 'unpublish';
					}
					$info .= JText::_('Published').': '.$row->publish_up.'<br />';
					break;
				case 2: 
					$alt   = 'Draft (user created)';
					$class = 'draftexternal';
					$task  = 'publish';
					break;
				case 3: 
					$alt   = 'New';
					$class = 'new';
					$task  = 'publish';
					break;
				case 4: 
					$alt   = 'Delete';
					$class = 'deleted';
					$task  = 'publish';
					break;
				case 5: 
					$alt   = 'Draft (internal production)';
					$class = 'draftinternal';
					$task  = 'publish';
					break;
				default:
					$alt   = '-';
					$class = '';
					$task  = '';
					break;
			}
			
			switch ($row->access)
			{
				case 0: 
					$color_access = 'style="color: green;"';
					$task_access = 'accessregistered';
					break;
				case 1: 
					$color_access = 'style="color: red;"';
					$task_access = 'accessspecial';
					break;
				case 2:
					$color_access = 'style="color: black;"';
					$task_access = 'accessprotected';
					break;
				case 3:
					$color_access = 'style="color: blue;"';
					$task_access = 'accessprivate';
					$row->groupname = 'Protected';
					break;
				case 4:
					$color_access = 'style="color: red;"';
					$task_access = 'accesspublic';
					$row->groupname = 'Private';
					break;
			}
			
			/*if ($pid != '-1') {
				if ($row->multiuse > 0) {
					$beingused = true;
				} else {
					$beingused = false;
				}
			}*/

			if ($row->logicaltitle) { 
				$typec  = $row->logicaltitle;
				$typec .= ' ('.$row->typetitle.')'; 
			} else { 
				$typec = $row->typetitle; 
			}
			
			// See if it's checked out or not
			if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00') {
				$checked = JHTMLGrid::_checkedOut( $row );
				$info .= ($row->checked_out_time != '0000-00-00 00:00:00') 
						 ? JText::_('Checked out').': '.JHTML::_('date', $row->checked_out_time, '%d %b, %Y').'<br />' 
						 : '';
				if ($row->editor) {
					$info .= JText::_('Checked out by').': '.$row->editor;
				}
			} else {
				$checked = JHTML::_('grid.id', $i, $row->id, false, 'id' );
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $checked; ?></td>
						<td><?php echo $row->id; ?></td>
						<td>
							<a class="editlinktip hasTip" href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id; echo $filterstring; ?>" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $info; ?>"><?php echo stripslashes($row->title); ?></a>
							<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>'.$row->path.'</small>': ''; ?>
						</td>
						<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; echo $filterstring; ?>" title="Set this to <?php echo $task;?>"><span><?php echo $alt; ?></span></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; echo $filterstring; ?>" <?php echo $color_access; ?> title="Change Access"><?php echo $row->groupname;?></a></td>
						<td><?php echo $typec; ?></td>
<?php if ($pid != '-1') { ?>
						<td><?php echo $pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?></td>
						<td><?php echo $pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?></td>
						<td><?php echo $row->ordering; ?></td>
	<?php if ($parent->type == 4) { ?>
						<td><?php echo ResourcesHtml::selectSection('grouping'.$row->id, $sections, $row->grouping, '', $i); ?></td>
	<?php } ?>
<?php } ?>
			  		</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>

			<?php ResourcesHtml::statusKey(); ?>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="viewtask" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
		</form>
		<?php
	}

	//-----------
	
	public function types( &$rows, &$cats, &$pageNav, $option, $filters ) 
	{
		?>
		<form action="index.php" method="post" name="adminForm">
			<fieldset id="filter">
				<label for="Category">
					<?php echo JText::_('Category'); ?>:
					<?php echo ResourcesHtml::selectType($cats, 'category', $filters['category'], 'Select...', '', '', ''); ?>
				</label>
			
				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
			</fieldset>
			
			<table class="adminlist" summary="<?php echo JText::_('A list of resource types and their grouping'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'type', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
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
			
			$cat_title = '';
			
			foreach ($cats as $cat)
			{
				if ($row->category == $cat->id) {
					$cat_title = $cat->type;
				}
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td><?php echo $row->id; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edittype&amp;id[]=<?php echo $row->id; ?>"><?php echo $row->type; ?></a></td>
						<td><?php echo $cat_title; ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="viewtypes" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	//-----------
	
	public function ratings( $database, $rows, $option, $id='' )
	{
?>
		<table class="adminform">
		 <thead>
		  <tr>
		   <th colspan="3">User ratings and comments</th>
		  </tr>
		 </thead>
		 <tbody>
<?php foreach($rows as $row) { 
		if (intval( $row->created ) <> 0) {
			$thedate = JHTML::_('date', $row->created );
		}
		$xuser =& XUser::getInstance($row->user_id);
?>
		  <tr>
		   <th>User:</th>
		   <td><?php echo $xuser->get('name'); ?></td>
		  </tr>
		  <tr>
		   <th>Rating:</th>
		   <td><?php echo ResourcesHtml::writeRating( $row->rating );?></td>
		  </tr>
		  <tr>
		   <th>Rated:</th>
		   <td><?php echo $thedate; ?></td>
		  </tr>
		  <tr>
		   <th style="border-bottom: 2px solid #999;vertical-align:top;">Comment:</th>
		   <td style="border-bottom: 2px solid #999;" class="aLeft"><?php 
		  if($row->comment) {
		   echo stripslashes($row->comment); 
		  } else {
		  	echo '[ no comment ]';
		  }
		   ?></td>
		  </tr>
<?php } ?>
		 </tbody>
		</table>
		<?php
	}

	//----------------------------------------------------------
	// Edit Views
	//----------------------------------------------------------

	public function addChild( $option, $task, $types, $pid, $parent, $err='' )
	{
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<h3><?php echo stripslashes($parent->title); ?></h3>
			
			<fieldset class="adminform">
				<legend><?php echo JText::_('Choose a method for adding a new child resource'); ?></legend>
				<?php if ($err) { echo ResourcesHtml::error($err); } ?>
				
				<table class="admintable">
					<tbody>
						<tr>
							<td>
								<input type="radio" name="method" id="child_create" value="create" checked="checked" /> 
								<label for="child_create"><?php echo JText::_('Create new'); ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<input type="radio" name="method" id="child_existing" value="existing" />
								<label for="child_existing"><?php echo JText::_('Add existing'); ?></label> - <?php echo JText::_('Resource ID'); ?>: <input type="text" name="childid" id="childid" value="" />
							</td>
						</tr>
						<tr>
							<td><input type="submit" name="Submit" value="<?php echo JText::_('Next >'); ?>" /></td>
						</tr>
					</tbody>
				</table>
			
				<input type="hidden" name="step" value="2" />
				<input type="hidden" name="task" value="<?php echo $task; ?>" />
				<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
			</fieldset>
		</form>
		<?php
	}
	
	//-----------
	
	public function editType( $row, $option, $categories ) 
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
			if (form.title.value == '') {
				alert( 'Type must have a title' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<p><?php echo JText::_('RESOURCES_REQUIRED_EXPLANATION'); ?></p>
			
			<fieldset class="adminform">
				<legend><?php echo JText::_('RESOURCES_TYPES_DETAILS'); ?></legend>
				
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="type"><?php echo JText::_('RESOURCES_TYPES_TITLE'); ?>: <span class="required">*</span></label></td>
							<td><input type="text" name="type" id="type" size="30" maxlength="100" value="<?php echo $row->type; ?>" /></td>
						</tr>
						<tr>
							<td class="key"><label><?php echo JText::_('RESOURCES_TYPES_CATEGORY'); ?>:</label></td>
							<td><?php echo $categories; ?></td>
						</tr>
						<tr>
							<td class="key"><label for="contributable"><?php echo JText::_('RESOURCES_TYPES_CONTRIBUTABLE'); ?>:</label></td>
							<td><input type="checkbox" name="contributable" id="contributable" value="1"<?php echo ($row->contributable) ? ' checked="checked"' : ''; ?> /> <?php echo JText::_('RESOURCES_TYPES_CONTRIBUTABLE_EXPLANATION'); ?></td>
						</tr>
						<tr>
							<td class="key"><label><?php echo JText::_('RESOURCES_TYPES_DESCIPTION'); ?>:</label></td>
							<td><?php 
								$editor =& JFactory::getEditor();
								echo $editor->display('description', stripslashes($row->description), '', '', '45', '10', false);
							?></td>
						</tr>
					</tbody>
				</table>
			
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="savetype" />
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('RESOURCES_TYPES_CUSTOM_FIELDS'); ?></legend>
				
				<table class="admintable">
					<thead>
						<tr>
							<th><?php echo JText::_('RESOURCES_TYPES_FIELD'); ?></th>
							<th><?php echo JText::_('RESOURCES_TYPES_TYPE'); ?></th>
							<th><?php echo JText::_('RESOURCES_TYPES_REQUIRED'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					//$fields = $row->customFields;
					$fields = array();
					if (trim($row->customFields) != '') {
						$fs = explode("\n", trim($row->customFields));
						foreach ($fs as $f) 
						{
							$fields[] = explode('=', $f);
						}
					}
					
					$r = count($fields);
					if ($r > 10) {
						$n = $r;
					} else {
						$n = 10;
					}
					for ($i=0; $i < $n; $i++)
					{
						if ($r == 0 || !isset($fields[$i])) {
							$fields[$i] = array();
							$fields[$i][0] = NULL;
							$fields[$i][1] = NULL;
							$fields[$i][2] = NULL;
							$fields[$i][3] = NULL;
							$fields[$i][4] = NULL;
						}
						?>
						<tr>
							<td><input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $fields[$i][1]; ?>" maxlength="255" /></td>
							<td><select name="fields[<?php echo $i; ?>][type]">
								<option value="text"<?php echo ($fields[$i][2]=='text') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXT'); ?></option>
								<option value="textarea"<?php echo ($fields[$i][2]=='textarea') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXTAREA'); ?></option>
							</select></td>
							<td><input type="checkbox" name="fields[<?php echo $i; ?>][required]" value="1"<?php echo ($fields[$i][3]) ? ' checked="checked"':''; ?> /></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</fieldset>
		</form>
		<?php
	}
	
	//-----------
	
	public function dateToPath( $date ) 
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year.DS.$dir_month;
	}
	
	//-----------
	
	public function editResource( $rconfig, &$row, &$lists, &$params, &$attribs, $myid, $option, $pid=0, $isnew=0, $return ) 
	{
		// Get the custom fields
		/*if ($row->type == 7) {
			$nbtags = $rconfig->get('tagstool');
		} else {
			$nbtags = $rconfig->get('tagsothr');
		}
		
		// This should be a string of comma separated items, so we need to turn it into an array
		$nbtags = explode(',',$nbtags);
		
		foreach ($nbtags as $nbtag)
		{
			$nbtag = trim($nbtag);
			
			// Explore the text and pull out all matches
			$allnbtags[$nbtag] = ResourcesHtml::parseTag($row->fulltext, $nbtag);
			
			// Clean the original text of any matches
			$row->fulltext = str_replace('<nb:'.$nbtag.'>'.$allnbtags[$nbtag].'</nb:'.$nbtag.'>','',$row->fulltext);
		}
		$row->fulltext = trim($row->fulltext);*/
		if ($row->standalone == 1) {
			$database =& JFactory::getDBO();
			
			$type = new ResourcesType( $database );
			$type->load( $row->type );

			$fields = array();
			if (trim($type->customFields) != '') {
				$fs = explode("\n", trim($type->customFields));
				foreach ($fs as $f) 
				{
					$fields[] = explode('=', $f);
				}
			} else {
				if ($row->type == 7) {
					$flds = $rconfig->get('tagstool');
				} else {
					$flds = $rconfig->get('tagsothr');
				}
				$flds = explode(',',$flds);
				foreach ($flds as $fld) 
				{
					$fields[] = array($fld, $fld, 'textarea', 0);
				}
			}

			if (!empty($fields)) {
				for ($i=0, $n=count( $fields ); $i < $n; $i++) 
				{
					// Explore the text and pull out all matches
					array_push($fields[$i], ResourcesHtml::parseTag($row->fulltext, $fields[$i][0]));

					// Clean the original text of any matches
					$row->fulltext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$row->fulltext);
				}
				$row->fulltext = trim($row->fulltext);
			}
		}

		// Build the path for uploading files
		$path = ResourcesHtml::dateToPath( $row->created );
		if ($row->id) {
			$dir_id = ResourcesHtml::niceidformat( $row->id );
		} else {
			$dir_id = time().rand(0,10000);
		}
		
		// Instantiate the sliders object
		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance('sliders');
		?>

		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'resethits') {
				if (confirm('Are you sure you want to reset the Hits to Zero? \nAny unsaved changes to this content will be lost.')){
					submitform( pressbutton );
					return;
				} else {
					return;
				}
			}

			if (pressbutton == 'resetrating') {
				if (confirm('Are you sure you want to reset the Rating to Unrated? \nAny unsaved changes to this content will be lost.')){
					submitform( pressbutton );
					return;
				} else {
					return;
				}
			}

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ''){
				alert( 'Content item must have a title' );
			} else if (form.type.value == "-1"){
				alert( 'You must select a Section.' );
			} else {
				submitform( pressbutton );
			}
		}

		function doFileoptions()
		{
			var fwindow = window.filer.window.imgManager;

			if(fwindow) {
				if(fwindow.document) {
					var fform = fwindow.document.forms['filelist'];

					if(fform) {
						//var filepath = fform.elements['listdir'];
						var slctdfiles = fform.slctdfile;
						if(slctdfiles.length > 1) {
							for(var i = 0; i < slctdfiles.length; i++) 
							{
								if(slctdfiles[i].checked) {
									var filepath = slctdfiles[i].value;
								}
							}
						} else {
							var filepath = slctdfiles.value;
						}

						box = document.adminForm.fileoptions;
	        			act = box.options[box.selectedIndex].value;

						//var selection = window.filer.document.forms[0].dirPath;
						//var dir = selection.options[selection.selectedIndex].value;

						if(act == '1') {
							document.forms['adminForm'].elements['params[series_banner]'].value = '<?php echo $rconfig->get('uploadpath').DS; ?>' + filepath;
						} else if(act == '2') {
							//if(filepath) {
							//document.forms['adminForm'].elements['path'].value = '<?php echo $rconfig->get('uploadpath').DS; ?>' + filepath;
							document.forms['adminForm'].elements['path'].value = filepath;
							//}
						} else if(act == '3') {
							text = '<img class="contentimg" src="<?php echo $rconfig->get('uploadpath').DS; ?>' + filepath + '" alt="image" />';
							document.forms['adminForm'].elements['fulltext'].focus();
							document.forms['adminForm'].elements['fulltext'].value  += text;
							document.forms['adminForm'].elements['fulltext'].focus();
						} else if(act == '4') {
							text = '<a href="<?php echo $rconfig->get('uploadpath').DS; ?>' + filepath + '">' + filepath + '</a>';
							document.forms['adminForm'].elements['fulltext'].focus();
							document.forms['adminForm'].elements['fulltext'].value  += text;
							document.forms['adminForm'].elements['fulltext'].focus();
						}
					}
				}
			}
		}
		function popratings() 
		{
			window.open('index3.php?option=<?php echo $option; ?>&task=ratings&id=<?php echo $row->id; ?>', 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
			return false;
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="resourceForm" class="editform">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top">
						<!-- <fieldset>
							<legend>Resource #<?php echo $row->id; ?></legend> -->
				<table class="adminform">
					<caption style="text-align: left; font-weight: bold;"><?php echo JText::sprintf('Resource #%s', $row->id); ?></caption>
					<tbody>
						<tr>
							<td class="key"><label for="title">Title:</label></td>
							<td colspan="3"><input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo htmlentities(stripslashes($row->title), ENT_QUOTES); ?>" /></td>
						</tr>
						<tr>
							<td class="key"><label>Type:</label></td>
							<td><?php echo $lists['type']; ?></td>
<?php if ($row->standalone == 1) { ?>
							<td class="key"><label for="alias">Alias:</label></td>
							<td><input type="text" name="alias" id="alias" size="25" maxlength="250" value="<?php echo stripslashes($row->alias); ?>" /></td>
						</tr>
						<tr>
							<td class="key"><label for="attrib[location]">Location:</label></td>
							<td><input type="text" name="attrib[location]" id="attrib[location]" size="25" maxlength="250" value="<?php echo $attribs->get( 'location', '' ); ?>" /></td>
							<td class="key"><label for="attrib[timeof]">Time:</label></td>
							<td><input type="text" name="attrib[timeof]" id="attrib[timeof]" size="25" maxlength="250" value="<?php echo $attribs->get( 'timeof', '' ); ?>" /></td>
						</tr>
<?php } else { ?>
							<td class="key"><label>Logical Type:</label></td>
							<td><?php echo $lists['logical_type']; ?><input type="hidden" name="alias" value="" /></td>
						</tr>
						<tr>
							<td class="key"><label for="path">File/URL:</label></td>
							<td colspan="3"><input type="text" name="path" id="path" size="60" maxlength="250" value="<?php echo $row->path; ?>" /></td>
						</tr>
						<!-- <tr>
							<td class="key"><label for="attrib[exclude]">Exclude from menu:</label></td>
							<td><input type="checkbox" name="attrib[exclude]" id="attrib[exclude]" value="1"<?php if($attribs->get( 'exclude', '' ) == 1) { echo ' checked="checked"'; } ?> /></td>
						</tr> -->
						<tr>
							<td class="key"><label for="attrib[duration]">Duration:</label></td>
							<td colspan="3"><input type="text" name="attrib[duration]" id="attrib[duration]" size="60" maxlength="100" value="<?php echo $attribs->get( 'duration', '' ); ?>" /></td>
						</tr>
						<tr>
							<td class="key"><label for="attrib[width]">Width:</label></td>
							<td><input type="text" name="attrib[width]" id="attrib[width]" size="5" maxlength="250" value="<?php echo $attribs->get( 'width', '' ); ?>" /></td>
							<td class="key"><label for="attrib[height]">Height:</label></td>
							<td><input type="text" name="attrib[height]" id="attrib[height]" size="5" maxlength="250" value="<?php echo $attribs->get( 'height', '' ); ?>" /></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
				
				<table class="adminform">
					<tbody>
						<tr>
							<td>
								<label>Intro Text:</label><br />
								<?php
								$editor =& JFactory::getEditor();
								echo $editor->display('introtext', stripslashes($row->introtext), '100%', '100px', '45', '10', false);
								?>
							</td>
						</tr>
						<tr>
							<td>
								<label>Main Text: (optional)</label><br />
								<?php
								echo $editor->display('fulltext', stripslashes($row->fulltext), '100%', '300px', '45', '10', false);
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- </fieldset> -->
<?php if ($row->standalone == 1) { ?>
				<!-- <fieldset>
					<legend><?php echo JText::_('Custom fields'); ?></legend> -->
				<table class="adminform">
					<caption style="text-align: left; font-weight: bold;"><?php echo JText::_('Custom fields'); ?></caption>
					<tbody>
	<?php
	$i = 3; 

	foreach ($fields as $field)
	{ 
		$i++;
		/*
		$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", end($field));
		*/
		$tagcontent = end($field);
	?>
					<tr>
						<td>
							<label><?php echo stripslashes($field[1]); ?>: <?php echo ($field[3] == 1) ? '<span class="required">'.JText::_('REQUIRED').'</span>': ''; ?></label><br />
							<?php if ($field[2] == 'text') { ?>
								<input type="text" name="<?php echo 'nbtag['.$field[0].']'; ?>" cols="50" rows="6"><?php echo stripslashes($tagcontent); ?></textarea>
							<?php
							} else {
								echo $editor->display('nbtag['.$field[0].']', stripslashes($tagcontent), '100%', '100px', '45', '10', false);
							} 
							?>
						</td>
					</tr>
					
	<?php 
	} 
	?>
					</tbody>
				</table>
				<!-- </fieldset> -->
<?php } ?>
			</td>
			<td valign="top" width="320" style="padding: 7px 0 0 5px">

				<!-- <table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
					<tbody>
						<tr>
							<td><strong>Resource ID:</strong></td>
							<td><?php echo $row->id; ?></td>
						</tr>

					</tbody>
				</table> -->
<?php if ($row->standalone == 1) { ?>
				<fieldset>
					<legend>Contributors</legend>
					<?php echo $lists['authors']; ?>
				</fieldset>
<?php }

				echo $tabs->startPane("content-pane");
				echo $tabs->startPanel('Publishing','publish-page');
?>
				<table width="100%" class="paramlist admintable" cellspacing="1">
					<tbody>
						<tr>
							<td class="paramlist_key"><label>Standalone:</label></td>
							<td><input type="checkbox" name="standalone" value="1" <?php echo ($row->standalone ==1) ? 'checked="checked"' : ''; ?> /> appears in searches, lists</td>
						</tr>
						<tr>
							<td class="paramlist_key"><label>Status:</label></td>
							<td>
								<select name="published">
									<option value="2"<?php echo ($row->published == 2) ? ' selected="selected"' : ''; ?>>Draft (user created)</option>
									<option value="5"<?php echo ($row->published == 5) ? ' selected="selected"' : ''; ?>>Draft (internal)</option>
									<option value="3"<?php echo ($row->published == 3) ? ' selected="selected"' : ''; ?>>Pending</option>
									<option value="0"<?php echo ($row->published == 0) ? ' selected="selected"' : ''; ?>>Unpublished</option>
									<option value="1"<?php echo ($row->published == 1) ? ' selected="selected"' : ''; ?>>Published</option>
									<option value="4"<?php echo ($row->published == 4) ? ' selected="selected"' : ''; ?>>Delete</option>
								</select>
							</td>
						</tr>
<?php if ($row->standalone == 1) { ?>
						<tr>
							<td class="paramlist_key"><label>Group:</label></td>
							<td><?php echo $lists['groups']; ?></td>
						</tr>
<?php } ?>
						<tr>
							<td class="paramlist_key"><label>Access Level:</label></td>
							<td><?php echo $lists['access']; ?></td>
						</tr>
						<tr>
							<td class="paramlist_key"><label>Change Creator:</label></td>
							<td><?php echo $lists['created_by']; ?></td>
						</tr>
						<!-- <tr>
							<td class="paramlist_key"><label for="created">Created Date:</label></td>
							<td>
								<input type="text" name="created" id="created" size="19" maxlength="19" value="<?php echo $row->created; ?>" />
								<input type="reset" name="reset" id="reset" onclick="return showCalendar('created', 'y-mm-dd');" value="..." />
							</td>
						</tr> -->
<?php if ($row->standalone == 1) { ?>
						<tr>
							<td class="paramlist_key"><label for="publish_up">Start Publishing:</label></td>
							<td>
								<?php echo JHTML::_('calendar', $row->publish_up, 'publish_up', 'publish_up', "%Y-%m-%d", array('class' => 'inputbox')); ?>
							</td>
						</tr>
						<tr>
							<td class="paramlist_key"><label for="publish_down">Finish Publishing:</label></td>
							<td>
								<?php echo JHTML::_('calendar', $row->publish_down, 'publish_down', 'publish_down', "%Y-%m-%d", array('class' => 'inputbox')); ?>
							</td>
						</tr>
<?php } ?>
						<tr>
							<td class="paramlist_key"><strong>Hits:</strong></td>
							<td>
								<?php echo $row->hits; ?>
								<?php if ( $row->hits ) { ?>
									<input type="button" name="reset_hits" id="reset_hits" value="Reset Hit Count" onclick="submitbutton('resethits');" />
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="paramlist_key"><strong>Created:</strong></td>
							<td><input type="hidden" name="created_by_id" value="<?php echo $row->created_by; ?>" /><?php echo ($row->created != '0000-00-00 00:00:00') ? $row->created.'</td></tr><tr><td class="paramlist_key"><strong>Created By:</strong></td><td>'.$row->created_by_name : 'New resource'; ?></td>
						</tr>
						<tr>
							<td class="paramlist_key"><strong>Modified:</strong></td>
							<td><input type="hidden" name="modified_by_id" value="<?php echo $row->modified_by; ?>" /><?php echo ($row->modified != '0000-00-00 00:00:00') ? $row->modified.'</td></tr><tr><td class="paramlist_key"><strong>Modified By:</strong></td><td>'.$row->modified_by_name : 'Not modified';?></td>
						</tr>
<?php if ($row->standalone == 1) { ?>
						<tr>
							<td class="paramlist_key"><strong>Ranking:</strong></td>
							<td>
								<?php echo $row->ranking; ?>/10
								<?php if ($row->ranking != '0') { ?>
									<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" /> 
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="paramlist_key"><strong>Rating:</strong></td>
							<td>
								<?php echo $row->rating.'/5.0 ('.$row->times_rated.' reviews)'; ?>
								<?php if ( $row->rating != '0.0' ) { ?>
									<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" /> 
									<a onclick="popratings();" href="#">View ratings</a>
								<?php } ?>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php
				echo $tabs->endPanel();
				echo $tabs->startPanel('Files','file-page');
?>
				<p>
					<label>
						<?php echo JText::_('With selected'); ?>:
						<select name="fileoptions" id="fileoptions">
							<option value="2">Set as main file</option>
							<option value="3">Insert HTML: image</option>
							<option value="4">Insert HTML: linked file</option>
						</select>
					</label>
					<input type="button" value="<?php echo JText::_('Apply'); ?>" onclick="doFileoptions();" />
				</p>
				<iframe width="100%" height="400" name="filer" id="filer" src="index3.php?option=com_resources&amp;task=media&amp;listdir=<?php echo $path.DS.$dir_id; ?>"></iframe>
				<input type="hidden" name="tmpid" value="<?php echo $dir_id; ?>" />
<?php
				echo $tabs->endPanel();
				
				if ($row->standalone == 1) {
					echo $tabs->startPanel('Tags','tags-page');
					?>
					<textarea name="tags" id="tags" cols="35" rows="6"><?php echo $lists['tags']; ?></textarea>
					<?php
					echo $tabs->endPanel();
				
					echo $tabs->startPanel('Parameters','params-page');
					echo $params->render();
					echo $tabs->endPanel();
				}
				
				echo $tabs->endPane();
?>

					</td>
				</tr>
			</table>
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
			<input type="hidden" name="isnew" value="<?php echo $isnew; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter[sort]" value="<?php echo $return['sort']; ?>" />
			<input type="hidden" name="filter[status]" value="<?php echo $return['status']; ?>" />
			<input type="hidden" name="filter[type]" value="<?php echo $return['type']; ?>" />
			
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
	
	//-----------
	
	public function imageStyle($listdir)
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo $listdir ;} else { echo '/';}  ?>')
				{
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if(confirm("Delete file \""+file+"\"?"))
				return true;

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if(numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if(confirm('Delete folder "'+folder+'"?'))
				return true;
	
			return false;
		}
		</script>
		<?php
	}

	//-----------

	public function edit_tags( &$database, &$objtags, $option, $title, $id, $tags, $mytagarray ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			submitform( pressbutton );
		}
		function addtag(tag)
		{
			var input = document.getElementById('tags-men');
			if(input.value == '') {
				input.value = tag;
			} else {
				input.value += ', '+tag;
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<h2>Edit Tags for this Resource</h2>
			<p>Create new tags and assign them to this resource by entering them below separated by commas (e.g. <em>negf theory, NEMS, ion transport</em>).</p>
				<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2"><?php echo $title; ?></th>
				  </tr>
				 </thead>
				 <tbody>
				  <tr>
				   <th><label for="tags-men">Create Tags:</label></th>
				   <td><input type="text" name="tags" id="tags-men" size="65" value="<?php //echo $objtags->tag_men; ?>" />
				   </td>
				  </tr>
				 </tbody>
				</table>

			<h3>Existing Tags</h3>
			<p>Add or remove tags assigned to this resource by checking or unchecking tags below.</p>
			<table class="adminlist" summary="A list of all tags">
			 <thead>
			  <tr>
			   <th style="width: 15px;"> </th>
			   <th>Raw Tag</th>
			   <th>Tag</th>
			   <th>Alias</th>
			   <th>Admin</th>
			  </tr>
			 </thead>
			 <tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $tags ); $i < $n; $i++) {
			$thistag = &$tags[$i];
			$check = '';
			if($thistag->admin == 1) {
				$check = '<span class="check">admin</span>';
			}
?>
			  <tr class="<?php echo "row$k"; ?>">
			   <td><input type="checkbox" name="tgs[]" id="cb<?php echo $i;?>" <?php if(in_array($thistag->tag,$mytagarray)) { echo 'checked="checked"'; } ?> value="<?php echo stripslashes($thistag->tag); ?>" /></td>
			   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->raw_tag); ?></a></td>
			   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->tag); ?></a></td>
			   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->alias); ?></a></td>
			   <td><?php echo $check; ?></td>
			  </tr>
<?php
			$k = 1 - $k;
		}
?>
			 </tbody>
			</table>
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savetags" />
		</form>
		<?php
	}
}
?>
