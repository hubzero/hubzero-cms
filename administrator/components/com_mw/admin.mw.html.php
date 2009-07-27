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
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class MwHtml
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function hInput($name, $value='', $id='')
	{
		$html  = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= ' />'.n;
		return $html;
	}

	//-----------

	public function sInput($name, $value='')
	{
		return '<input type="submit" name="'.$name.'" value="'.$value.'" />'.n;
	}

	//-----------

	public function td($content, $attribs='')
	{
		$html  = '   <td';
		$html .= ($attribs) ? ' '.$attribs : '';
		$html .= '>'.$content.'</td>'.n;
		return $html;
	}
	
	//----------------------------------------------------------
	// An administrative active link.
	//----------------------------------------------------------

	public function admlink( $name, $vars, $text, $option ) 
	{
		$html = '';
		if (0) { // use POST
			$html .= '<a href="#"'."\n";
			$html .= '   onclick="document.adm.action=\'index.php\';'."\n";
			foreach ($vars as $k => $v) 
			{
				$html .= '            document.adm.'.$k.'.value=\''.$v.'\';'."\n";
			}
			$html .= '            document.adm.submit();"'."\n";
			$html .= '   title="'.$text.'">'.$name.'</a>';
		} else { // use GET
			$url = 'index.php?option='.$option;
			foreach ($vars as $k => $v) 
			{
				$url .= '&'.$k.'='.$v;
			}
			$html .= '<a href="'.$url.'" title="'.$text.'">'.$name.'</a>';
		}
		return $html;
	}
	
	//----------------------------------------------------------
	// ListEdit widget.
	//----------------------------------------------------------
	
	public function listedit( $list, $hidden ) 
	{
		$html = '<ul class="ntools">'.n;
		foreach ($list as $key => $value) 
		{
			$html .= ' <li>';
			if ($value != '0') { 
				$html .= '<b>'; 
			}
			if (0) { // POST
				$html .= "<a href='#'\n";
				$html .= "onclick=\"document.adm.action='index.php';\n";
				$html .= "         document.adm.item.value='$key';\n";
				foreach ($hidden as $k => $v) 
				{
					$html .= "         document.adm.$k.value='$v';\n";
				}
				$html .= "         document.adm.submit();\"\n";
			} else { // GET
				$html .= '<a href="index.php';
				$prefix = '?';
				foreach ($hidden as $k => $v) 
				{
					if ($v != '') {
						$html .= $prefix.$k.'='.$v;
						$prefix = '&';
					}
				}
				$html .= $prefix.'item='.$key.'" ';
			}
			$html .= 'title="Toggle '.$key.' ('.$value.')">'.$key.'</a>';
			if ($value != '0') { 
				$html .= '</b>'; 
			}
			$html .= '</li>'.n;
		}
		$html .= '</ul>'.n;
		return $html;
	}

	//----------------------------------------------------------
	// Table widget.
	//----------------------------------------------------------

	public function table( $rows, $header, $middle, $trailer, $tail_row ) 
	{
		$html  = '<table>'.n;
		$html .= '  <tr>'.n; 
		$html .= $header(); 
		$html .= '  </tr>'.n;
		$html .= ' <tbody>'.n; 
		for($i=0; $i < count($rows); $i++) 
		{
			$html .= '  <tr>'.n;
			$html .= $middle($rows[$i]); 
			$html .= '  </tr>'.n;
		}
		if ($tail_row != '') {
			$html .= '  <tr>'.n;
			$html .= $trailer($tail_row); 
			$html .= '  </tr>'.n;
		}
		$html .= ' </tbody>'.n; 
		$html .= '</table>'.n;
		return $html;
	}
	
	//-----------
	
	public function updateform($table, $bit, $refs, &$row, $option)
	{
		$html  = '<tr>'.n;
		$html .= '<form name="update_'.$table.'" method="get" action="index.php">'.n;
		$html .= t.MwHtml::hInput('option',$option);
		$html .= t.MwHtml::hInput('admin',1);
		$html .= t.MwHtml::hInput('table',$table);
		$html .= t.MwHtml::hInput('op','update');
		$html .= t.MwHtml::hInput('filter_'.$table,$row->name);
		$html .= '<td><input type="text" name="name" size="10" value="'.$row->name.'" />'.n;
		$html .= '<td> '.$bit.n;
		$html .= '<td><input type="text" name="description" size="20" value="'.$row->description.'" />'.n;
		$html .= '<td> '.$refs.n;
		$html .= '<td><input type="submit" name="update" value="Update" />'.n;
		$html .= '<form>'.n;
		$html .= '</tr>'.n;
		
		return $html;
	}
	
	//-----------

	public function tableHeader($headers)
	{
		$html  = ' <thead>'.n;
		$html .= '  <tr>'.n;
		for ($i=0, $n=count( $headers ); $i < $n; $i++) 
		{
			$html .= '   <th>'.$headers[$i].'</th>'.n;
		}
		$html .= '  </tr>'.n;
		$html .= ' </thead>'.n;
		return $html;
	}
	
	//-----------
	
	public function delete_button($name, $table, $value, $option)
	{
		$html  = '<form name="delete_'.$value.'" method="get" action="index.php">'.n;
		$html .= t.MwHtml::hInput('option',$option);
		$html .= t.MwHtml::hInput('admin',1);
		$html .= t.MwHtml::hInput('table',$table);
		$html .= t.MwHtml::hInput('op','delete');
		$html .= t.MwHtml::hInput($name,$value);
		$html .= t.'<input type="submit" name="delete" value="Delete" />'.n;
		$html .= '</form>'.n;
		return $html;
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
	
	public function licenses( &$rows, &$pageNav, $option, $filters ) 
	{
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
			<!-- <fieldset id="filter">
				<label>
					<?php echo JText::_('SEARCH'); ?>: 
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				</label>
			
				<label>
					<?php echo JText::_('SORT'); ?>:
					<select name="sortby">
						<option value="id DESC"<?php if ($filters['sortby'] == 'id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID_DESC'); ?></option>
						<option value="id ASC"<?php if ($filters['sortby'] == 'id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID_ASC'); ?></option>
						<option value="alias DESC"<?php if ($filters['sortby'] == 'alias DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALIAS_DESC'); ?></option>
						<option value="alias ASC"<?php if ($filters['sortby'] == 'alias ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALIAS_ASC'); ?></option>
					</select>
				</label>
				
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset> -->
		
				<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
					<thead>
				 		<tr>
							<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
							<th><?php echo JText::_('ID'); ?></th>
							<th><?php echo JText::_('ALIAS'); ?></th>
							<th><?php echo JText::_('LICENSE'); ?></th>
							<th><?php echo JText::_('LICENSE_USERS'); ?></th>
							<th><?php echo JText::_('LICENSE_TOOLS'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
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
							<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
							<td><?php echo $row->id; ?></td>
							<td><a href="index.php?option=<?php echo $option ?>&amp;task=editlicense&amp;id[]=<? echo $row->id; ?>"><?php echo stripslashes($row->alias); ?></a></td>
							<td><?php echo MwHtml::shortenText(stripslashes($row->description)); ?></td>
							<td><a href="index.php?option=<?php echo $option ?>&amp;task=licenseassoc&amp;lid=<? echo $row->id; ?>&amp;tbl=user"><?php echo $row->ucount.' '.JText::_('LICENSE_USERS'); ?></a></td>
							<td><a href="index.php?option=<?php echo $option ?>&amp;task=licenseassoc&amp;lid=<? echo $row->id; ?>&amp;tbl=tool"><?php echo $row->tcount.' '.JText::_('LICENSE_TOOLS'); ?></a></td>
						</tr>
<?php
			$k = 1 - $k;
		}
?>
					</tbody>
				</table>
		
				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}

	//-----------

	public function licenseassoc( &$rows, &$pageNav, $option, $filters, $database ) 
	{
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
			<fieldset id="filter">
				<label>
					<?php echo JText::_('SEARCH'); ?>
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				</label>
			
				<label>
					<?php echo JText::_('SORT_BY'); ?>:
					<select name="sortby">
						<option value=""<?php if ($filters['sortby'] == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Select...'); ?></option>
						<?php if ($filters['tbl'] == 'user') { ?>
						<option value="user_id DESC"<?php if ($filters['sortby'] == 'user_id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('User ID DESC'); ?></option>
						<option value="user_id ASC"<?php if ($filters['sortby'] == 'user_id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('User ID ASC'); ?></option>
						<option value="username ASC"<?php if ($filters['sortby'] == 'username ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Username'); ?></option>
						<option value="name"<?php if ($filters['sortby'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('Name'); ?></option>
						<?php } else { ?>
						<option value="tool_id DESC"<?php if ($filters['sortby'] == 'tool_id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Tool ID DESC'); ?></option>
						<option value="tool_id ASC"<?php if ($filters['sortby'] == 'tool_id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Tool ID ASC'); ?></option>
						<option value="alias ASC"<?php if ($filters['sortby'] == 'alias ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Alias'); ?></option>
						<option value="title"<?php if ($filters['sortby'] == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('Title'); ?></option>
						<?php } ?>
					</select>
				</label>
				
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
			
			<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('LICENSE'); ?></th>
						<?php if ($filters['tbl'] == 'user') { 
							$col = 5;
							?>
						<th><?php echo JText::_('USER_ID'); ?></th>
						<th><?php echo JText::_('Username'); ?></th>
						<th><?php echo JText::_('Name'); ?></th>
						<?php } else { 
							$col = 5;
							?>
						<th><?php echo JText::_('TOOL_ID'); ?></th>
						<th><?php echo JText::_('Title'); ?></th>
						<th><?php echo JText::_('Alias'); ?></th>
						<?php } ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo $col; ?>">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
	<?php
			$k = 0;
			
			$obj = new License( $database );
			$obj->load( $filters['lid'] );
			
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				$row = &$rows[$i];
				
				if ($filters['tbl'] == 'user') {
					$oid = $row->user_id;
				} else {
					$oid = $row->tool_id;
				}
	?>
							<tr class="<?php echo "row$k"; ?>">
								<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $oid; ?>" onclick="isChecked(this.checked);" /></td>
								<td><?php echo $obj->alias; ?></td>
								<!-- <td><a href="index.php?option=<?php echo $option ?>&amp;task=editlicenseassoc&amp;lid=<?php echo $filters['lid']; ?>&amp;tbl=<?php echo $filters['tbl']; ?>&amp;id[]=<?php echo $oid; ?>"><?php echo $oid; ?></a></td> -->
								<td><?php echo $oid; ?></td>
							<?php if ($filters['tbl'] == 'user') { ?>
								<td><?php echo $row->username; ?></td>
								<td><?php echo $row->name; ?></td>
							<?php } else { ?>
								<td><?php echo $row->title; ?></td>
								<td><?php echo $row->alias; ?></td>
							<?php }?>
							</tr>
	<?php
				$k = 1 - $k;
			}
	?>
						</tbody>
					</table>

					<input type="hidden" name="lid" value="<?php echo $filters['lid']; ?>" />
					<input type="hidden" name="tbl" value="<?php echo $filters['tbl']; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="licenseassoc" />
					<input type="hidden" name="boxchecked" value="0" />
			</form>
	<?php
		}

	//-----------
	
	public function editlicense( $license, $option ) 
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
		</script>

		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('LICENSE'); ?></legend>
					
					<input type="hidden" name="id" value="<?php echo $license->id; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="savelicense" />
					
					<table class="admintable">
					 <tbody>
					  <tr>
				 	   <td class="key"><label for="alias"><?php echo JText::_('ALIAS'); ?>:</label></td>
				 	   <td><input type="text" name="alias" id="alias" value="<?php echo stripslashes($license->alias); ?>" size="50" /></td>
				 	  </tr>
				 	  <tr>
					   <td class="key" valign="top"><label for="description"><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
					   <td>
					        <?php
							jimport('joomla.html.editor');
							$editor = &JEditor::getInstance();
							echo $editor->display('description', stripslashes($license->description), '360px', '200px', '40', '10');
					        ?>
					  </td>
					  </tr>
					</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<p>Spaces and non-alphanumeric characters are stripped from the alias.</p>
			</div>
			<div class="clr"></div>
		</form>
		<?php
	}
	
	//-----------
	
	public function editlicenseassoc( $licenses, $option, $tbl, $lid, $id ) 
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
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
				<legend><?php echo JText::_('LICENSE'); ?></legend>
				
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="tbl" value="<?php echo $tbl; ?>" />
				<input type="hidden" name="task" value="savelicenseassoc" />
					
				<table class="admintable">
					<tbody>
						<tr>
				 			<td class="key"><label for="license_id"><?php echo JText::_('LICENSE_ID'); ?>:</label></td>
				 			<td>
				 				<select name="license_id">
								<?php
								foreach ($licenses as $license) 
								{
									echo '<option value="'.$license->id.'"';
									if ($lid == $license->id) {
										echo ' selected="selected"';
									}
									echo '>'.$license->alias.'</option>'.n;
								}
								?>
				 				</select>
				 			</td>
				 		</tr>
						<tr>
							<?php if ($tbl == 'user') { ?>
							<td class="key"><label for="user_id"><?php echo JText::_('USER_ID'); ?>:</label></td>
				 			<td><input type="text" name="user_id" id="user_id" value="<?php echo $id; ?>" size="50" /></td>
							<?php } else { ?>
							<td class="key"><label for="tool_id"><?php echo JText::_('TOOL_ID'); ?>:</label></td>
					 		<td><input type="text" name="tool_id" id="tool_id" value="<?php echo $id; ?>" size="50" /></td>
							<?php } ?>
				 		</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
		<?php
	}
}
?>