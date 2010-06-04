<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$database =& JFactory::getDBO();
$hidecheckbox = 0;

$html  = '<fieldset>'."\n";
switch ($this->table)
{
	case 'invitees':
		$html .= "\t\t".'<legend>'.JText::_('GROUPS_TBL_CAPTION_INVITEES').'</legend>'."\n";
		break;
	case 'pending':
		$html .= "\t\t".'<legend>'.JText::_('GROUPS_TBL_CAPTION_PENDING').'</legend>'."\n";
		break;
	case 'managers':
		$html .= "\t\t".'<legend>'.JText::_('GROUPS_TBL_CAPTION_MANAGERS').'</legend>'."\n";
		break;
	case 'members':
		$html .= "\t\t".'<legend>'.JText::_('GROUPS_TBL_CAPTION_MEMBERS').'</legend>'."\n";
		break;
}
$html .= "\t".'<table class="adminlist">'."\n";
$html .= "\t\t".'<thead>'."\n";
$html .= "\t\t\t".'<tr>'."\n";
$html .= "\t\t\t\t".'<th>'.JText::_('NAME').'</th>'."\n";
$html .= "\t\t\t\t".'<th>'.JText::_('USERNAME').'</th>'."\n";
$html .= "\t\t\t\t".'<th>'.JText::_('EMAIL').'</th>'."\n";
$html .= "\t\t\t".'</tr>'."\n";
$html .= "\t\t".'</thead>'."\n";
switch ($this->table)
{
	case 'invitees':
		if (count($this->groupusers) > 0) {
			$html .= "\t\t".'<tfoot>'."\n";
			$html .= "\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t".'<td colspan="3" style="text-align: right;">'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_ACCEPT').'" />'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_UNINVITE').'" />'."\n";
			$html .= "\t\t\t\t".'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t".'</tfoot>'."\n";
		}
		break;
	case 'pending':
		if (count($this->groupusers) > 0) {
			$html .= "\t\t".'<tfoot>'."\n";
			$html .= "\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t".'<td colspan="3" style="text-align: right;">'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_APPROVE').'" />'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_DENY').'" />'."\n";
			$html .= "\t\t\t\t".'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t".'</tfoot>'."\n";
		}
		break;
	case 'managers':
		if (count($this->groupusers) > 1) {
			$html .= "\t\t".'<tfoot>'."\n";
			$html .= "\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t".'<td colspan="3" style="text-align: right;">'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_DEMOTE').'" />'."\n";
			//$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'."\n";
			$html .= "\t\t\t\t".'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t".'</tfoot>'."\n";
		} else {
			$hidecheckbox = 1;
		}
		break;
	case 'members':
		if (count($this->groupusers) > 0) {
			$html .= "\t\t".'<tfoot>'."\n";
			$html .= "\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t".'<td colspan="3" style="text-align: right;">'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_PROMOTE').'" />'."\n";
			$html .= "\t\t\t\t\t".'<input type="submit" name="action" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'."\n";
			$html .= "\t\t\t\t".'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
			$html .= "\t\t".'</tfoot>'."\n";
		}
		break;
}
$html .= "\t\t".'<tbody>'."\n";
$row = 0;

$cls = 'even';
if ($this->groupusers) {
	foreach ($this->groupusers as $guser) 
	{
		$u =& JUser::getInstance($guser);
		if (!is_object($u)) {
			continue;
		}
		
		$row = new GroupsReason( $database );
		$row->loadReason($u->get('username'), $this->gid);

		if ($row) {
			$reasonforjoin = stripslashes($row->reason);
		} else {
			$reasonforjoin = '';
		}
			
		$cls = (($cls == 'even') ? 'odd' : 'even');
		
		$html .= "\t\t\t".'<tr class="'.$cls.'">'."\n";
		if ($hidecheckbox == 1) {
			//$html .= "\t\t\t\t".'<td><input type="hidden" name="users[]" value="'.$u->get('id').'" /> ';
			$html .= "\t\t\t\t".'<td>';
		} else {
			$html .= "\t\t\t\t".'<td><input type="checkbox" name="users[]" value="'.$u->get('id').'"';
			//$html .= (count($groupusers) == 1) ? ' checked="checked"': '';
			$html .= ' /> ';
		}
		$html .= '<a href="'.JRoute::_('index.php?option=com_members&id='.$u->get('id')).'">'.htmlentities($u->get('name')).'</a>';
		$html .= '</td>'."\n";
		if ($this->authorized == 'admin') {
			$login = '<a href="index.php?option=com_whois&amp;task=view&amp;username='. $u->get('username').'">'.htmlentities($u->get('username')).'</a>';
		} else {
			$login = htmlentities($u->get('username'));
		}
		$html .= "\t\t\t\t".'<td>'. $login .'</td>'."\n";
		$html .= "\t\t\t\t".'<td><a href="mailto:'. htmlentities($u->get('email')) .'">'. htmlentities($u->get('email')) .'</a></td>'."\n";
		$html .= "\t\t\t".'</tr>'."\n";
		if ($this->table == 'pending' && $reasonforjoin) {
			$html .= "\t\t\t".'<tr class="'.$cls.'">'."\n";
			$html .= "\t\t\t\t".'<td colspan="3">'.JText::_('APPROVE_GROUP_MEMBER_REASON').' '."\n";
			$html .= $reasonforjoin;
			$html .= "\t\t\t\t".'</td>'."\n";
			$html .= "\t\t\t".'</tr>'."\n";
		}
		$row++;
	}
} else {
	$html .= "\t\t\t".'<tr class="odd">'."\n";
	$html .= "\t\t\t\t".'<td colspan="3">'.JText::_('NONE').'</td>'."\n";
	$html .= "\t\t\t".'</tr>'."\n";
}
$html .= "\t\t".'</tbody>'."\n";
$html .= "\t".'</table>'."\n";
$html .= '</fieldset><br /><br />'."\n";

echo $html;
?>