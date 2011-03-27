<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//define base link
$base_link = 'index.php?option=com_groups&gid='.$this->group->get('cn').'&task=managepages';

//from vars
$form_btn = "Add Module";
$form_title = "Add a New Group Module";
$new = 1;

$id = '';
$type = '';
$gid = '';
$order = '';
$active = '';
$content = '';

//get the type of module
$type = JRequest::getVar('type');

//if we are in edit mode
if($this->module) {
	$form_btn = "Update Module";
	$form_title = "Update the Group Module";
	$new = '';
	
	$id = $this->module['id'];
	$gid = $this->module['gid'];
	$order = $this->module['morder'];
	$active = $this->module['active'];
	$type = $this->module['type'];
	$content = $this->module['content'];
}

//group asset browser folder
$lid = $this->group->get('gidNumber');

//path to the modules
$path = JPATH_COMPONENT . DS . 'modules' . DS;

//declare empty array to hold mod details
$module_details = array(
	'name' => '',
	'title' => '',
	'input_title' => '',
	'input' => ''
);

if($type) {
	if(is_file($path . $type.'.php')) {
		//include the php file
		include_once($path . $type.'.php');
		$class_name = ucfirst($type.'Module');
		$class = new $class_name($this->group);
		$module_details = $class->onManageModules();
	}
}
?>

<div id="content-header" class="full">
	<h2><?php echo $form_title; ?></h2>
</div>
<div id="content-header-extra">
	<p class="manage"><a href="<?php echo JRoute::_($base_link); ?>">Back to Manage Custom Content</a></p>
</div>

	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
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
			<h3><?php echo $form_title; ?></h3>
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
			<h3><?php echo $form_title; ?></h3>
			
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
						echo $editor->display('module[content]', 'module[content]', stripslashes($content), '', '50', '15');
						echo "<a href=\"\">Wiki Formatting</a> is allowed.";
					} else {
						echo str_replace('{{VALUE}}',$content,$module_details['input']); 
					}
				?>
			</label>
			<input type="hidden" name="module[id]" value="<?php echo $id; ?>" />
			<input type="hidden" name="module[gid]" value="<?php echo $gid; ?>" />
			<input type="hidden" name="module[morder]" value="<?php echo $order; ?>" />
			<input type="hidden" name="module[active]" value="<?php echo $active; ?>" />
			<input type="hidden" name="module[new]" value="<?php echo $new; ?>" />
			<input type="hidden" name="sub_task" value="save_module" />
		</fieldset>
		<p class="submit"><input type="submit" name="module_submit" value="<?php echo $form_btn; ?>" /></p>
	<?php } ?>
</form>
