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

$base_link = 'index.php?option=com_groups&gid='.$this->group->get('cn').'&task=managepages';

if($this->module) {
	$btn = "Update Module";
	$title = "Update the Group Module";
	$new = '';
} else {
	$btn = "Add Module";
	$title = "Add a New Group Module";
	$new = 1;	
}

if ($this->group->get('gidNumber')) {
	$lid = $this->group->get('gidNumber');
} else {
	$lid = time().rand(0,10000);
}

$path = JPATH_COMPONENT . DS . 'modules' . DS;

$type = JRequest::getVar('type');
if($type) {
	$this->module['type'] = $type;
}

if(is_file($path . $this->module['type'].'.php')) {
	//include the php file
	include_once($path . $this->module['type'].'.php');
	
	$class_name = ucfirst($this->module['type'].'Module');
	
	$class = new $class_name($this->group);
	
	$module_details = $class->onManageModules();
}
?>

<div id="content-header" class="full">
	<h2><?php echo 'Modules: '.$title; ?></h2>
</div>
<div id="content-header-extra">
	<p class="manage"><a href="<?php echo JRoute::_($base_link); ?>">Back to Manage Custom Content</a></p>
</div>


<?php
	foreach($this->notifications as $notification) {
		echo $notification;
	}
?>

<form action="<?php echo JRoute::_($base_link); ?>" method="POST" id="hubForm">
	<div class="explaination">
		<?php if($module_details['name'] == 'custom') { ?>
		<div id="asset_browser">
			<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
			<iframe width="100%" height="300" name="filer" id="filer" src="index.php?option=com_groups&amp;no_html=1&amp;task=media&amp;listdir=<?php echo $lid; ?>"></iframe>
		</div><!-- / .asset_browser -->
		<?php } ?>
	</div>
	
	<?php if(!$type && !$this->module) { ?>
		<fieldset>
			<h3><?php echo $title; ?></h3>
			<p>Click on one of following links to add it as a module to your group. You will be taken to another screen to finish adding content if necessary.</p>
			<ul>
				<?php foreach($this->all_mod_details as $amd) { ?>
					<?php if($amd['name'] != '') { ?>
						<li><a href="<?php echo JRoute::_($base_link.'&sub_task=add_module&type='.$amd['name']); ?>"><?php echo $amd['title']; ?></a></li>
					<?php } ?>
				<?php } ?>
			</ul>
		</fieldet>
	<?php } else { ?>
		<fieldset>
			<h3><?php echo $title; ?></h3>
			
			<label>Module Type:
				<?php echo '<strong>'.$module_details['title'].'</strong>'; ?>
				<?php if($new) { ?>
					<input type="hidden" name="module[type]" value="<?php echo $type; ?>" />
				<?php } ?>
			</label>

			<label><?php echo $module_details['input_title']; ?>
				<?php
					if($module_details['name'] == 'custom') {
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('module[content]', 'module[content]', stripslashes($this->module['content']), '', '50', '15');
						echo "<a href=\"\">Wiki Formatting</a> is allowed.";
					} else {
						echo str_replace('{{VALUE}}',$this->module['content'],$module_details['input']); 
					}
				?>
			</label>
			<input type="hidden" name="module[id]" value="<?php echo $this->module['id']; ?>" />
			<input type="hidden" name="module[gid]" value="<?php echo $this->module['gid']; ?>" />
			<input type="hidden" name="module[morder]" value="<?php echo $this->module['morder']; ?>" />
			<input type="hidden" name="module[active]" value="<?php echo $this->module['active']; ?>" />
			<input type="hidden" name="module[new]" value="<?php echo $new; ?>" />
			<input type="hidden" name="sub_task" value="save_module" />
		</fieldset>
		<p class="submit"><input type="submit" name="module_submit" value="<?php echo $btn; ?>" /></p>
	<?php } ?>
</form>