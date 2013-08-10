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

$dateFormat = '%d/%m/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd/m/Y';
	$tz = false;
}

$mconfig =& JComponentHelper::getParams( 'com_members' );
$path  = $mconfig->get('webpath');
if (substr($path, 0, 1) != DS) {
	$path = DS.$path;
}
if (substr($path, -1, 1) == DS) {
	$path = substr($path, 0, (strlen($path) - 1));
}
$ih = new ProjectsImgHandler();

// List sorting
$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];
$urlbit = $this->task == 'edit' ? 'edit=team' : 'step=1';

// Use alias or id in urls?
$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

?>
<?php if (!$this->setup) { ?>
	<h5><?php echo JText::_('COM_PROJECTS_ADD_NEW_MEMBERS').' '.JText::_('COM_PROJECTS_AS').':'; ?></h5>
<?php } ?>

<div class="combine_options">
	 <label>
		<?php if ($this->setup) { ?>
			<?php echo JText::_('COM_PROJECTS_AS'); ?>:
		<?php } ?>
		 <input class="option" name="role" id="role_owner" type="radio" value="1"  />
		<?php echo JText::_('COM_PROJECTS_LABEL_OWNERS'); ?>  
	 </label>
	 <label>
		<span class="and_or"><?php echo JText::_('COM_PROJECTS_OR'); ?></span>
		<input class="option" name="role" id="role_collaborator" type="radio" value="0" checked="checked" />
		<?php echo JText::_('COM_PROJECTS_LABEL_COLLABORATORS'); ?>
	</label>
</div>
<p class="hint"><?php echo JText::_('COM_PROJECTS_ADD_TEAM_HINT'); ?></p>

<div class="add-team">
	<label id="add-users">
		 <span class="instr i_user"><?php echo JText::_('COM_PROJECTS_ADD_IND_USER'); ?>:</span>
		<?php 
			$mc = $dispatcher->trigger( 'onGetMultiEntry', array(array('members', 'newmember', 'newmember')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="newmember" id="newmember" value="" size="35" />
			<?php } ?>
	</label>
	<span class="or_separator"><?php echo strtoupper(JText::_('COM_PROJECTS_OR')); ?></span>
	<label id="add-groups">
		 <span class="instr i_group"><?php echo JText::_('COM_PROJECTS_ADD_GROUP_OF_USERS'); ?>:</span>
		<?php 
			$mc = $dispatcher->trigger( 'onGetMultiEntry', array(array('groups', 'newgroup', 'newgroup')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="newgroup" id="newgroup" value="" size="35" maxlength="200" />
			<?php } ?>
	</label>
	 <input type="submit" id="team-save" value="<?php echo JText::_('COM_PROJECTS_ADD'); ?>" class="btn yesbtn" />
</div>
<div id="team-spacer">
	<?php if ($this->project->owned_by_group) { ?>
	<p class="notice"><?php echo JText::_('COM_PROJECTS_TEAM_GROUP_PROJECT_EDITING'); ?></p>
	<?php } ?>
	<div class="list-editing">
	 <p>
		<span><?php echo ucfirst(JText::_('COM_PROJECTS_TEAM_TOTAL_MEMBERS')); ?>: <span class="prominent"><?php echo $this->total; ?></span></span>
	 	<span id="team-manage" class="manage-options hidden">
		<span class="faded"><?php echo JText::_('COM_PROJECTS_TEAM_EDIT_ROLE'); ?></span>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=team').'?action=delete'; ?>" class="manage" id="t-delete" ><?php echo JText::_('COM_PROJECTS_DELETE'); ?></a>
		</span>	
	</p>
	</div>
</div>
	<table id="teamlist" class="listing">
		<thead>
			<tr>
				<th class="checkbox"></th>
				<th class="th_image"></th>
				<th class="th_user i_user <?php if($this->filters['sortby'] == 'name') { echo 'activesort'; } ?>"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task='.$this->task).'/?'.$urlbit.a.'t_sortby=name'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort"><?php echo JText::_('COM_PROJECTS_NAME'); ?></a></th>
				<th<?php if($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task='.$this->task).'/?'.$urlbit.a.'t_sortby=role'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort"><?php echo JText::_('COM_PROJECTS_ROLE'); ?></a></th>
				<th<?php if($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task='.$this->task).'/?'.$urlbit.a.'t_sortby=status'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort"><?php echo JText::_('COM_PROJECTS_JOINED'); ?></a></th>
				<th class="i_group <?php if($this->filters['sortby'] == 'group') { echo 'activesort'; } ?>"><?php if( $this->count_groups > 0 ) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task='.$this->task).'/?'.$urlbit.a.'t_sortby=group'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" ><?php } ?><?php echo JText::_('COM_PROJECTS_ADDED_AS_PART_OF_GROUP'); ?><?php if( $this->count_groups > 0 ) { ?></a><?php } ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->team as $owner) 
	{
					$thumb = '';
					
					if($owner->picture) {
						$curthumb = $ih->createThumbName($owner->picture);
						$thumb = $path.DS.Hubzero_View_Helper_Html::niceidformat($owner->userid).DS.$curthumb;
					}
					if (!$thumb or !is_file(JPATH_ROOT.$thumb)) {
						$thumb = $mconfig->get('defaultpic');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
					}
					$username = $owner->username ? $owner->username : $owner->invited_email;
					$creator = $this->project->created_by_user == $owner->userid ? 1 : 0;
					
					// Determine css class for user
					$usr_class = $owner->status == 0 ? ' class="userinvited"' : ' class="useractive"';
					$usr_class = ($creator || ($this->project->owned_by_group && $owner->native)) 
						? ' class="userowner"' : $usr_class;
					if($owner->role == 1)
					{
						$role = JText::_('COM_PROJECTS_LABEL_OWNER');	
					}
					else 
					{
						$role = JText::_('COM_PROJECTS_LABEL_COLLABORATOR');
					}					
?>
			<tr class="mline <?php if($owner->userid == $this->uid) { echo 'native'; } else if($owner->status == 0) { echo 'u_invited'; } ?>" id="tr_<?php echo $owner->id; ?>">
				<td><input type="checkbox" value="<?php echo $owner->id?>" name="owner[]" class="checkmember <?php if($owner->groupid) { echo 'group:'.$owner->groupid; } ?>"  <?php if($owner->native && ($this->project->owned_by_group or ($this->managers_count == 1 && $owner->role == 1) or $this->setup)) { echo 'disabled="disabled"'; } ?> /></td>
				<td <?php echo $usr_class; ?>><img width="30" height="30" src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname; ?>" /></td>
				<td><?php echo $owner->fullname; ?><span class="block mini short prominent"><?php echo $username; ?></span></td>
				<td class="mini nobsp"><?php if(!$creator) { ?><span class="frole owner:<?php echo $owner->id; ?> role:<?php echo $owner->role; ?>" id="r<?php echo $owner->id; ?>"><?php } ?><?php echo $role; ?><?php if(!$creator) { ?></span><?php } ?></td>
				<td class="mini"><?php echo $owner->status == 1 ? JHTML::_('date', $owner->added, $dateFormat, $tz) : '<span class="invited">'.JText::_('COM_PROJECTS_INVITED').'</span>';  ?></td>				
				<td><?php echo $owner->groupdesc ? Hubzero_View_Helper_Html::shortenText($owner->groupdesc, 30, 0) : ''; ?><span class="block mini short prominent"><?php echo $owner->groupname; ?></span></td>
			</tr>
<?php } ?>
			</tbody>
			</table>
	<div class="nav_pager"><p>
		<?php 
		if($this->filters['start'] == 0) {	?>
			<span>&laquo; <?php echo JText::_('COM_PROJECTS_PREVIOUS'); ?></span>
		<?php	} else {  ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task=edit').'/?edit=team'.a.'t_sortby='.$this->filters['sortby'].a.'t_limitstart='.$prev_start.a.'t_sortdir='.$this->filters['sortdir']; ?>">&laquo; <?php echo JText::_('COM_PROJECTS_PREVIOUS'); ?></a>
		<?php } ?><span>&nbsp; | &nbsp;</span>
		<?php 
		if( $whatsleft <= 0 or $this->filters['limit'] == 0 ) { ?>
			<span><?php echo JText::_('COM_PROJECTS_NEXT'); ?> &raquo;</span>
		<?php	} else { ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task=edit').'/?edit=team'.a.'t_sortby='.$this->filters['sortby'].a.'t_limitstart='.$next_start.a.'t_sortdir='.$this->filters['sortdir']; ?>"><?php echo JText::_('COM_PROJECTS_NEXT'); ?> &raquo;</a>
		<?php } ?></p>
	</div>
