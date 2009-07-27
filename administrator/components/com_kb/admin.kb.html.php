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

class KbHtml 
{
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

	public function shortenText($text, $chars=300) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
		}

		return $text;
	}

	//-----------

	public function formSelect($name, $array, $value, $class='', $id)
	{
		$out  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$out .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		$out .= ' <option value="0"';
		$out .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
		$out .= '>'. JText::_('NONE') .'</option>'."\n";
		foreach ($array as $anode) 
		{
			$selected = ($anode->id == $value || $anode->title == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->title.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}
	
	//-----------
	
	public function sectionSelect( $categories, $val, $name ) 
	{
		$out  = '<select name="'.$name.'">'.n;
		$out .= t.'<option value="">'.JText::_('SELECT_CATEGORY') .'</option>'.n;
		foreach ($categories as $category) 
		{
			$selected = ($category->id == $val)
					  ? ' selected="selected"'
					  : '';
			$out .= t.'<option value="'.$category->id.'"'.$selected.'>'.$category->title.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}

	//-----------
	
	public function categories( &$database, &$rows, &$pageNav, $option, $filterby, $cid, $vtask ) 
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
			// do field validation
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset id="filter">
				<label>
					<?php echo JText::_('SORT_BY'); ?>:
					<select name="filterby" onchange="document.adminForm.submit( );">
						<option value="m.title"<?php if ($filterby == 'm.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
						<option value="m.id"<?php if ($filterby == 'm.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
					</select>
				</label> 
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('TITLE'); ?></th>
						<th><?php echo JText::_('PUBLISHED'); ?></th>
						<th><?php echo JText::_('ACCESS'); ?></th>
						<th><?php echo JText::_('SUB_CATEGORIES'); ?></th>
						<th><?php echo JText::_('QUESTIONS'); ?></th>
					</tr>
				</thead>
				<tfoot>
		 			<tr>
		 				<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
		 			</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			switch ($row->state) 
			{
				case 1:
					$class = 'published';
					$task = 'unpublishc';
					$alt = JText::_('PUBLISHED');
					break;
				case 2:
					$class = 'expired';
					$task = 'publishc';
					$alt = JText::_('TRASHED');
					break;
				case 0:
					$class = 'unpublished';
					$task = 'publishc';
					$alt = JText::_('UNPUBLISHED');
					break;
			}
			
			if (!$row->access) {
				$color_access = 'style="color: green;"';
				$task_access = 'accessregistered';
			} elseif ($row->access == 1) {
				$color_access = 'style="color: red;"';
				$task_access = 'accessspecial';
			} else {
				$color_access = 'style="color: black;"';
				$task_access = 'accesspublic';
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editcat&amp;id[]=<?php echo $row->id; echo ($cid) ? '&amp;cid='.$cid : ''; ?>" title="<?php echo JText::_('EDIT_CATEGORY'); ?>"><?php echo stripslashes($row->title); ?></a></td>
						<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; echo ($cid) ? '&amp;cid='.$cid : ''; ?>" title="<?php echo JText::sprintf('SET_TASK',$task);?>"><span><?php echo $alt; ?></span></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; ?>" <?php echo $color_access; ?> title="<?php echo JText::_('CHANGE_ACCESS'); ?>"><?php echo $row->groupname;?></a></td>
<?php if ($row->cats > 0) { ?>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=categories&amp;id=<? echo $row->id; ?>" title="<?php echo JText::_('VIEW_CATEGORIES_FOR_CATEGORY'); ?>"><?php echo $row->cats; ?></a></td>
<?php } else { ?>
						<td><?php echo $row->cats; ?></td>
<?php } ?>
<?php if ($row->total > 0) { ?>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=category&amp;id=<? echo $row->id; echo ($cid) ? '&amp;cid='.$cid : ''; ?>" title="<?php echo JText::_('VIEW_ARTICLES_FOR_CATEGORY'); ?>"><?php echo $row->total; ?></a></td>
<?php } else { ?>
						<td><?php echo $row->total; ?></td>
<?php } ?>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="<?php echo $vtask; ?>" />
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>

		<p><?php echo JText::_('PUBLISH_KEY'); ?></p>
		<?php
	}

	//-----------
	
	public function articles( &$database, &$rows, &$pageNav, $option, $filterby, $out, $id, $mtask ) 
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
			// do field validation
			submitform( pressbutton );
		}
		</script>


		<form action="index.php" method="post" name="adminForm">
			<fieldset id="filter">
				<label>
					<?php echo JText::_('CATEGORY'); ?>: 
					<?php echo $out; ?>
				</label>
			
				<label>
					<?php echo JText::_('SORT_BY'); ?>: 
					<select name="filterby" onchange="document.adminForm.task='articles';document.adminForm.submit();">
						<option value="m.modified"<?php if ($filterby == 'm.modified') { echo ' selected="selected"'; } ?>><?php echo JText::_('MODIFIED'); ?></option>
						<option value="m.title"<?php if ($filterby == 'm.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
						<option value="m.id"<?php if ($filterby == 'm.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
					</select>
				</label>
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
		 				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
		 				<th><?php echo JText::_('QUESTION'); ?></th>
		 				<th><?php echo JText::_('PUBLISHED'); ?></th>
		 				<th><?php echo JText::_('CATEGORY'); ?></th>
		 				<th><?php echo JText::_('HELPFUL'); ?></th>
		 				<th><?php echo JText::_('CHECKED_OUT'); ?></th>
					</tr>
				</thead>
				<tfoot>
		 			<tr>
		 				<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
		 			</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];

			switch($row->state) 
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
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editfaq&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('EDIT_ARTICLE'); ?>"><?php echo stripslashes($row->title); ?></a></td>
						<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;cid=<?php echo $id; ?>" title="<?php echo JText::sprintf('SET_TASK',$task);?>"><span><?php echo $alt; ?></span></a></td>
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

			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="<?php echo $mtask ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="cid" value="<?php echo $filters['cid']; ?>" />
		</form>

		<p><?php echo JText::_('PUBLISH_KEY'); ?></p>
		<?php
	}

	//-----------
	
	public function deleteOptions( $id, $option, $task )
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
		}
		</script>
		<form action="index.php?option=<?php echo $option; ?>&amp;task=<?php echo $task; ?>&amp;step=2" method="post" name="adminForm">
			<table class="adminform">
				<thead>
		 			<tr>
						<th><?php echo JText::_('CHOOSE_DELETE_OPTION'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="radio" name="action" id="action_delete" value="deletefaqs" checked="checked" /> <label for="action_delete"><?php echo JText::_('DELETE_ALL'); ?></label></td>
					</tr>
					<tr>
						<td><input type="radio" name="action" id="action_remove" value="removefaqs" /> <label for="action_remove"><?php echo JText::_('DELETE_ONLY_CATEGORY'); ?></label></td>
					</tr>
					<tr>
						<td><input type="submit" name="Submit" value="<?php echo JText::_('NEXT'); ?>" /></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="task" value="<?php echo $task; ?>" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>">
		</form>
		<?php
	}

	//-----------
	
	public function editFaqForm( &$row, &$lists, &$params, $myid, $option, $cid=0 ) 
	{
		$mod_date = NULL;
		$create_date = NULL;
		if (intval( $row->modified ) <> 0) {
			$mod_date = JHTML::_('date',$row->modified, '%Y-%m-%d');
		}
		if (intval( $row->created ) <> 0) {
			$create_date = JHTML::_('date',$row->created, '%Y-%m-%d');
		}
		
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton =='resethits') {
				if (confirm( <?php echo JText::_('RESET_HITS_WARNING'); ?> )){
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
<?php if ( !$cid && $row->category == 1 ) { ?>
			if (form.title.value == ''){
				alert( <?php echo JText::_('ERROR_MISSING_TITLE'); ?> );
<?php } else { ?>
			if (form.title.value == ''){
				alert( <?php echo JText::_('ERROR_MISSING_QUESTION'); ?> );
			} else if (form.fulltext.value == ''){
				alert( <?php echo JText::_('ERROR_MISSING_ANSWER'); ?> );
<?php } ?>
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('DETAILS'); ?></legend>
				
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
								<td><input type="text" name="alias" size="30" maxlength="100" value="<?php echo stripslashes($row->alias); ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('CATEGORY'); ?>: *</label></td>
								<td><?php echo $lists['sections']; ?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('SUB_CATEGORY'); ?>:</label></td>
								<td><?php echo $lists['categories']; ?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('QUESTION'); ?>: *</label></td>
								<td><input type="text" name="title" size="30" maxlength="100" value="<?php echo stripslashes($row->title); ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('LONG_QUESTION'); ?>:</label></td>
								<td><?php
								echo $editor->display('introtext', stripslashes($row->introtext), '360px', '200px', '50', '10');
								?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('ANSWER'); ?>: *</label></td>
								<td><?php
								echo $editor->display('fulltext', stripslashes($row->fulltext), '360px', '200px', '50', '10');
								?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PARAMETERS'); ?></legend>
					
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="state"><?php echo JText::_('PUBLISHED'); ?>:</label></td>
								<td><input type="checkbox" name="state" value="1" <?php echo $row->state ? 'checked="checked"' : ''; ?> /></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('ACCESS_LEVEL'); ?>:</label></td>
								<td><?php echo $lists['access']; ?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('CHANGE_CREATOR'); ?>:</label></td>
								<td><?php echo $lists['created_by']; ?></td>
							</tr>
							<tr>
								<td class="key"><label for="created"><?php echo JText::_('CREATED'); ?>:</label></td>
								<td><input type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('STATE'); ?>:</td>
								<td><?php echo ($row->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED'); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('HITS'); ?>:</td>
								<td><?php echo $row->hits; ?>
								<?php if ( $row->hits ) { ?>
								<input type="button" name="reset_hits" id="reset_hits" value="<?php echo JText::_('RESET_HITS'); ?>" onclick="submitbutton('resethits');" />
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('HELPFUL'); ?>:</td>
								<td>+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?>
								<?php if ( $row->helpful > 0 || $row->nothelpful > 0 ) { ?>
								<input type="button" name="reset_helpful" value="<?php echo JText::_('RESET_HELPFUL'); ?>" onclick="submitbutton('resethelpful');" />
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('CREATED'); ?>:</td>
								<td><?php echo ($row->created != '0000-00-00 00:00:00') ? $create_date.'</td></tr><tr><td class="key">'.JText::_('BY').':</td><td>'.$row->created_by : JText::_('NEW'); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('LAST_MODIFIED'); ?>:</td>
								<td><?php echo ($row->modified != '0000-00-00 00:00:00') ? $mod_date.'</td></tr><tr><td class="key">'.JText::_('BY').':</td><td>'.$row->modified_by : JText::_('NOT_MODIFIED');?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savefaq" />
		</form>
		<?php
	}

	//-----------
	
	public function editCatForm( &$row, &$lists, $myid, $option, $cid ) 
	{
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton =='resethits') {
				if (confirm( <?php echo JText::_('RESET_HITS_WARNING'); ?> )){
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
				alert( <?php echo JText::_('ERROR_MISSING_TITLE'); ?> );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" class="editform">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('DETAILS'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label><?php echo JText::_('PARENT_CATEGORY'); ?>:</label></td>
								<td><?php echo $lists['categories']; ?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
								<td><input type="text" name="title" size="30" maxlength="100" value="<?php echo stripslashes($row->title); ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
								<td><input type="text" name="alias" size="30" maxlength="100" value="<?php echo stripslashes($row->alias); ?>" /></td>
							</tr>
							<tr>
								<td class="key" style="vertical-align: top;"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
								<td><?php
								echo $editor->display('description', stripslashes($row->description), '360px', '200px', '50', '10'); 
								?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PARAMETERS'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="state"><?php echo JText::_('PUBLISH'); ?>:</label></td>
								<td><input type="checkbox" name="state" value="1" <?php echo $row->state ? 'checked="checked"' : ''; ?> /></td>
							</tr>
							<tr>
								<td class="key" style="vertical-align: top;"><label><?php echo JText::_('ACCESS_LEVEL'); ?>:</label></td>
								<td><?php echo $lists['access']; ?></td>
							</tr>
							<tr>
								<td class="key"><label><?php echo JText::_('STATE'); ?>:</label></td>
								<td><?php echo ($row->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED'); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>
			
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savecat" />
		</form>
		<?php
	}

	//-----------

	public function autop($pee, $br = 1) 
	{
		// converts paragraphs of text into xhtml
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
		$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
		$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee); 
		if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
		$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
		
		return $pee; 
	}

	//-----------
	
	public function unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', '', $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee; 
	}
}
?>