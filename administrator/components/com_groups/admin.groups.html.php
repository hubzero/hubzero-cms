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
	define("r","\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class GroupsHtml
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
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------
	
	public function browse( $rows, $option, $filters, $pageNav ) 
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
					<?php echo JText::_('SEARCH'); ?>: 
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				</label>
				<label>
					<?php echo JText::_('TYPE'); ?>:
					<select name="type">
						<option value="all"<?php echo ($filters['type'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('ALL'); ?></option>
						<option value="hub"<?php echo ($filters['type'] == 'hub') ? ' selected="selected"' : ''; ?>>hub</option>
						<option value="system"<?php echo ($filters['type'] == 'system') ? ' selected="selected"' : ''; ?>>system</option>
						<option value="project"<?php echo ($filters['type'] == 'project') ? ' selected="selected"' : ''; ?>>project</option>
					</select>
				</label>
				
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
			
			<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
				<thead>
				 	<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('ID'); ?></th>
						<th><?php echo JText::_('CN'); ?></th>
						<th><?php echo JText::_('NAME'); ?></th>
						<th><?php echo JText::_('TYPE'); ?></th>
						<th><?php echo JText::_('PUBLISHED'); ?></th>
						<th><?php echo JText::_('APPLICANTS'); ?></th>
						<th><?php echo JText::_('INVITEES'); ?></th>
						<th><?php echo JText::_('MANAGERS'); ?></th>
						<th><?php echo JText::_('TOTAL_MEMBERS'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$group = new XGroup();
			$group->gidNumber = $row->gidNumber;
			$group->cn = $row->cn;
			
			$applicants = count($group->get('applicants'));
			$invitees   = count($group->get('invitees'));
			$managers   = count($group->get('managers'));
			$members    = count($group->get('members'));
			
			switch ($row->type) 
			{
				case '0': $type = 'system';  break;
				case '1': $type = 'hub';     break;
				case '2': $type = 'project'; break;
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->cn ?>" onclick="isChecked(this.checked);" /></td>
						<td><?php echo $row->gidNumber; ?></td>
						<td><?php echo $row->cn; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<? echo $row->cn; ?>"><?php echo stripslashes($row->description); ?></a></td>
						<td><?php echo $type; ?></td>
						<td><?php echo ($row->published) ? '<span class="check">'.JText::_('YES').'</span>' : '&nbsp;'; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $applicants; ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $invitees; ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $managers; ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $members; ?></a></td>
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

	public function manage( $html, $group, $error='' )
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			// do field validation
			submitform( pressbutton );
		}
		</script>
		<h3><?php echo $group->description; ?> (<?php echo $group->cn; ?>)</h3>
		<?php
		if ($error) {
			echo '<p style="color: #c00;"><strong>'.$error.'</p>'.n;
		}
		echo $html;
	}

	//-----------

	public function members( $database, $option, $group, $invitees, $pending, $managers, $members, $authorized ) 
	{
		$html  = '<form action="index.php" name="adminForm" method="post">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.JText::_('ADD_USERNAME').' <input type="text" name="usernames" value="" /></label> '.n;
		$html .= t.t.' <label>'.JText::_('TO').' <select name="tbl">'.n;
		$html .= t.t.t.'<option value="invitees">'.JText::_('INVITEES').'</option>'.n;
		$html .= t.t.t.'<option value="applicants">'.JText::_('APPLICANTS').'</option>'.n;
		$html .= t.t.t.'<option value="members" selected="selected">'.JText::_('MEMBERS').'</option>'.n;
		$html .= t.t.t.'<option value="managers">'.JText::_('MANAGERS').'</option>'.n;
		$html .= t.t.'</select></label> '.n;
		$html .= t.t.' <input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_ADD').'" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<br />'.n;
		$html .= GroupsHtml::table( $option, $database, $invitees, $group->cn, $authorized, 'invitees' ).'<br /><br />';
		$html .= GroupsHtml::table( $option, $database, $pending, $group->cn, $authorized, 'pending' ).'<br /><br />';
		$html .= GroupsHtml::table( $option, $database, $managers, $group->cn, $authorized, 'managers', count($managers) ).'<br /><br />';
		$html .= GroupsHtml::table( $option, $database, $members, $group->cn, $authorized, 'members' ).'<br /><br />';
		$html .= t.'<input type="hidden" name="gid" value="'. $group->cn .'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="manage" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= '</form>'.n;
		
		return $html;
	}

	//-----------

	public function table( $option, $database, $groupusers, $gid, $authorized, $table, $nummanagers=0 )
	{
		$hidecheckbox = 0;
		
		
		$html  = t.'<fieldset>'.n;
		switch ($table)
		{
			case 'invitees':
				$html .= t.t.'<legend>'.JText::_('GROUPS_TBL_CAPTION_INVITEES').'</legend>'.n;
				break;
			case 'pending':
				$html .= t.t.'<legend>'.JText::_('GROUPS_TBL_CAPTION_PENDING').'</legend>'.n;
				break;
			case 'managers':
				$html .= t.t.'<legend>'.JText::_('GROUPS_TBL_CAPTION_MANAGERS').'</legend>'.n;
				break;
			case 'members':
				$html .= t.t.'<legend>'.JText::_('GROUPS_TBL_CAPTION_MEMBERS').'</legend>'.n;
				break;
		}
		$html .= t.'<table class="adminlist">'.n;
		$html .= t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<th>'.JText::_('NAME').'</th>'.n;
		$html .= t.t.t.t.'<th>'.JText::_('USERNAME').'</th>'.n;
		$html .= t.t.t.t.'<th>'.JText::_('EMAIL').'</th>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</thead>'.n;
		switch ($table)
		{
			case 'invitees':
				if (count($groupusers) > 0) {
					$html .= t.t.'<tfoot>'.n;
					$html .= t.t.t.'<tr>'.n;
					$html .= t.t.t.t.'<td colspan="3" style="text-align: right;">'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_ACCEPT').'" />'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_UNINVITE').'" />'.n;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tfoot>'.n;
				}
				break;
			case 'pending':
				if (count($groupusers) > 0) {
					$html .= t.t.'<tfoot>'.n;
					$html .= t.t.t.'<tr>'.n;
					$html .= t.t.t.t.'<td colspan="3" style="text-align: right;">'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_APPROVE').'" />'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_DENY').'" />'.n;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tfoot>'.n;
				}
				break;
			case 'managers':
				if (count($groupusers) > 1) {
					$html .= t.t.'<tfoot>'.n;
					$html .= t.t.t.'<tr>'.n;
					$html .= t.t.t.t.'<td colspan="3" style="text-align: right;">'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_DEMOTE').'" />'.n;
					//$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'.n;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tfoot>'.n;
				} else {
					$hidecheckbox = 1;
				}
				break;
			case 'members':
				if (count($groupusers) > 0) {
					$html .= t.t.'<tfoot>'.n;
					$html .= t.t.t.'<tr>'.n;
					$html .= t.t.t.t.'<td colspan="3" style="text-align: right;">'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_PROMOTE').'" />'.n;
					$html .= t.t.t.t.t.'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'.n;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tfoot>'.n;
				}
				break;
		}
		$html .= t.t.'<tbody>'.n;
		$row = 0;
		
		$cls = 'even';
		if ($groupusers) {
			foreach ($groupusers as $guser) 
			{
				$u =& JUser::getInstance($guser);
				if (!is_object($u)) {
					continue;
				}
				
				$row = new GroupsReason( $database );
				$row->loadReason($u->get('username'), $gid);

				if ($row) {
					$reasonforjoin = stripslashes($row->reason);
				} else {
					$reasonforjoin = '';
				}
					
				$cls = (($cls == 'even') ? 'odd' : 'even');
				
				$html .= t.t.t.'<tr class="'.$cls.'">'.n;
				if ($hidecheckbox == 1) {
					//$html .= t.t.t.t.'<td><input type="hidden" name="users[]" value="'.$u->get('id').'" /> ';
					$html .= t.t.t.t.'<td>';
				} else {
					$html .= t.t.t.t.'<td><input type="checkbox" name="users[]" value="'.$u->get('id').'"';
					//$html .= (count($groupusers) == 1) ? ' checked="checked"': '';
					$html .= ' /> ';
				}
				$html .= '<a href="'.JRoute::_('index.php?option=com_members&id='.$u->get('id')).'">'.htmlentities($u->get('name')).'</a>';
				$html .= '</td>'.n;
				if ($authorized == 'admin') {
					$login = '<a href="index.php?option=com_whois'.a.'task=view'.a.'username='. $u->get('username').'">'.htmlentities($u->get('username')).'</a>';
				} else {
					$login = htmlentities($u->get('username'));
				}
				$html .= t.t.t.t.'<td>'. $login .'</td>'.n;
				$html .= t.t.t.t.'<td><a href="mailto:'. htmlentities($u->get('email')) .'">'. htmlentities($u->get('email')) .'</a></td>'.n;
				$html .= t.t.t.'</tr>'.n;
				if ($table == 'pending' && $reasonforjoin) {
					$html .= t.t.t.'<tr class="'.$cls.'">'.n;
					$html .= t.t.t.t.'<td colspan="3">'.JText::_('APPROVE_GROUP_MEMBER_REASON').' '.n;
					$html .= $reasonforjoin;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
				}
				$row++;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			
			$html .= t.t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.t.'<td colspan="3">'.JText::_('NONE').'</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;
		$html .= '</fieldset>'.n;
		
		return $html;
	}

	//-----------

	public function denyHtml($option, $group, $users, $msg)
	{
		$html  = '<form action="index.php" method="post" id="hubForm" name="adminForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUP_DENY_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<legend>'.JText::_('GROUP_DENY_MEMBERSHIP').'</legend>';
		$html .= t.'<table class="admintable">'.n;
		$html .= t.t.'<tbody>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.'<td class="key">'.n;
		$html .= t.t.t.JText::_('GROUP_DENY_USERS').':<br />'.n;
		$logins = array();
		foreach ($users as $user) 
		{
			$u =& JUser::getInstance($user);
			$logins[] = $u->get('username');
			$html .= t.t.t.'<input type="hidden" name="users[]" value="'.$user.'" />'.n;
		}
		$html .= t.t.t.'<strong>'.implode(', ',$users).'</strong>';
		$html .= t.t.'</td></tr>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.'<td class="key">'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_DENY_REASON').':'.n;
		$html .= t.t.'</label></td>'.n;
		$html .= t.t.t.'<td><textarea name="reason" id="reason" rows="12" cols="50">'.JText::_('GROUP_DENY_REASON_DEFAULT').' ';
		if ($msg) {
			$html .= htmlentities(stripslasheS($msg));
		}
		$html .= '</textarea>'.n;
		$html .= t.t.'</td></tr>'.n;
		$html .= t.t.'</tbody>'.n;
		$html .= t.t.'</table>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="manage" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="action" value="confirmdeny" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function removeHtml($option, $group, $users)
	{
		$html  = '<form action="index.php" method="post" id="hubForm" name="adminForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUP_REMOVE_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<legend>'.JText::_('GROUP_REMOVE_MEMBERSHIP').'</legend>';
		$html .= t.'<table class="admintable">'.n;
		$html .= t.t.'<tbody>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.'<td class="key">'.n;
		$html .= t.t.t.JText::_('GROUP_REMOVE_USERS').':</td><td>'.n;
		$logins = array();
		foreach ($users as $user) 
		{
			$u =& JUser::getInstance($user);
			$logins[] = $u->get('username');
			$html .= t.t.t.'<input type="hidden" name="users[]" value="'.$user.'" />'.n;
		}
		$html .= t.t.t.'<strong>'.implode(', ',$users).'</strong>';
		$html .= t.t.'</td></tr>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.'<td class="key">'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_REMOVE_REASON').':'.n;
		$html .= t.t.'</label></td>'.n;
		$html .= t.t.t.'<td><textarea name="reason" id="reason" rows="12" cols="50"></textarea>'.n;
		$html .= t.t.'</td></tr>'.n;
		$html .= t.t.'</tbody>'.n;
		$html .= t.t.'</table>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="manage" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="action" value="confirmremove" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function edit( $group, $option, $errors=null ) 
	{
		jimport('joomla.html.editor');
		
		$editor =& JEditor::getInstance();
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.description.value == '') {
				alert( <?php echo JText::_('CONTRIBUTOR_MUST_HAVE_FIRST_NAME'); ?> );
			} else if( form.cn.value == '' ) {
				alert( <?php echo JText::_('CONTRIBUTOR_MUST_HAVE_LAST_NAME'); ?> );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
<?php
		if ($errors) {
			echo implode('<br />', $errors);
		}
?>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('DETAILS'); ?></legend>
					
					<input type="hidden" name="gidNumber" value="<?php echo $group->gidNumber; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="save" />
					
					<table class="admintable">
					 <tbody>
						<tr>
						   <td class="key"><label for="firstname"><?php echo JText::_('GROUPS_ID'); ?>:</label></td>
						   <td><?php echo $group->gidNumber; ?></td>
						  </tr>
						<tr>
						   <td class="key"><label for="type"><?php echo JText::_('TYPE'); ?>:</label></td>
						   <td>
						   	<select name="type">
								<option value="1"<?php echo ($group->type == '1') ? ' selected="selected"' : ''; ?>>hub</option>
								<option value="0"<?php echo ($group->type == '0') ? ' selected="selected"' : ''; ?>>system</option>
								<option value="2"<?php echo ($group->type == '2') ? ' selected="selected"' : ''; ?>>project</option>
							</select>
						   </td>
						  </tr>
					<tr>
					   <td class="key"><label for="firstname"><?php echo JText::_('CN'); ?>:</label></td>
					   <td><input type="text" name="cn" id="cn" value="<?php echo stripslashes($group->cn); ?>" size="50" /></td>
					  </tr>
					  <tr>
					   <td class="key"><label for="description"><?php echo JText::_('GROUPS_TITLE'); ?>:</label></td>
					   <td><input type="text" name="description" id="description" value="<?php echo htmlentities(stripslashes($group->description)); ?>" size="50" /></td>
					  </tr>
					<tr>
					   <td class="key"><label for="join_policy"><?php echo JText::_('GROUPS_JOIN_POLICY'); ?>:</label></td>
					   <td>
						<input type="radio" name="join_policy" value="0"<?php if ($group->join_policy == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_PUBLIC'); ?><br />
					<input type="radio" name="join_policy" value="1"<?php if ($group->join_policy == 1) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_RESTRICTED'); ?><br />
					<input type="radio" name="join_policy" value="2"<?php if ($group->join_policy == 2) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_INVITE'); ?><br />
					<input type="radio" name="join_policy" value="3"<?php if ($group->join_policy == 3) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_CLOSED'); ?>
					   </td>
					  </tr>
					<tr>
					   <td class="key"><label for="privacy"><?php echo JText::_('GROUPS_PRIVACY'); ?>:</label></td>
					   <td>
						<input type="radio" name="privacy" value="0"<?php if ($group->privacy == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?><br />
					<input type="radio" name="privacy" value="1"<?php if ($group->privacy == 1) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?><br />
					<input type="radio" name="privacy" value="4"<?php if ($group->privacy == 4) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?>
					   </td>
					  </tr>
					<tr>
					   <td class="key"><label for="access"><?php echo JText::_('GROUPS_CONTENT_PRIVACY'); ?>:</label></td>
					   <td>
						<input type="radio" name="access" value="0"<?php if ($group->access == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?><br />
					<input type="radio" name="access" value="3"<?php if ($group->access == 3) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?><br />
					<input type="radio" name="access" value="4"<?php if ($group->access == 4) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?>
					   </td>
					  </tr>
					<tr>
					   <td class="key" valign="top"><label for="restrict_msg"><?php echo JText::_('GROUPS_EDIT_CREDENTIALS'); ?>:</label></td>
					   <td>
					        <?php
							echo $editor->display('restrict_msg', htmlentities(stripslashes($group->restrict_msg)), '360px', '200px', '40', '10');
					        ?>
					  </td>
					  </tr>
				 	  <tr>
					   <td class="key" valign="top"><label for="public_desc"><?php echo JText::_('GROUPS_EDIT_PUBLIC_TEXT'); ?>:</label></td>
					   <td>
					        <?php
							echo $editor->display('public_desc', htmlentities(stripslashes($group->public_desc)), '360px', '200px', '40', '10');
					        ?>
					  </td>
					  </tr>
					<tr>
					   <td class="key" valign="top"><label for="private_desc"><?php echo JText::_('GROUPS_EDIT_PRIVATE_TEXT'); ?>:</label></td>
					   <td>
					        <?php
							echo $editor->display('private_desc', htmlentities(stripslashes($group->private_desc)), '360px', '200px', '40', '10');
					        ?>
					  </td>
					  </tr>
					 </tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<?php
				$html  = t.t.'<p>'.JText::_('GROUPS_ACCESS_EXPLANATION').'</p>'.n;
				$html .= t.t.'<dl><dt>'.JText::_('GROUPS_ACCESS_PUBLIC').'</dt>'.n;
				$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PUBLIC_EXPLANATION').'</dd>'.n;
				$html .= t.t.'<dt>'.JText::_('GROUPS_ACCESS_PROTECTED').'</dt>'.n;
				$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PROTECTED_EXPLANATION').'</p>'.n;
				$html .= t.t.'<dt>'.JText::_('GROUPS_ACCESS_PRIVATE').'</dt>'.n;
				$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PRIVATE_EXPLANATION').'</dd></dl>'.n;
				echo $html;
				?>
			</div>
			<div class="clr"></div>
		</form>
		<?php
	}
}
?>