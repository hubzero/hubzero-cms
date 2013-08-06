<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//default logo
$default_logo = DS.'components'.DS.$this->option.DS.'assets'.DS.'img'.DS.'group_default_logo.png';

//access levels
$levels = array(
	//'anyone' => 'Enabled/On',
	'anyone' => 'Any HUB Visitor',
	'registered' => 'Only Registered User of the HUB',
	'members' => 'Only Group Members',
	'nobody' => 'Disabled/Off'
);
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="icon-group group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>"><?php echo JText::_('Back to Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
<div class="main section">
	<form name="customize" method="POST" action="index.php" id="hubForm">
		<div class="explaination asset-browser-parent">
			<div id="asset_browser">
				<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
				<iframe 
					width="100%" 
					height="310" 
					name="filer" 
					id="filer" 
					src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=filebrowser&amp;listdir=<?php echo $this->group->get('gidNumber'); ?>&amp;tmpl=component"></iframe>
			</div><!-- / .asset_browser -->
		</div>
		
		<fieldset id="top_box">
			<legend>Group Logo</legend>
			<p>Upload your logo using the file upload browser to the right first then refresh your browser and select it in the drop down below.</p>
			<label>
				<select name="group[logo]" id="group_logo" rel="<?php echo $this->group->get('gidNumber'); ?>">
					<option value="">Select a group logo...</option>
					<?php foreach($this->logos as $logo) { ?>
						<?php 
							$remove = JPATH_SITE . DS . 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS;
							$sel = (str_replace($remove,"",$logo) == $this->group->get('logo')) ? 'selected' : '';
						?>
						<option <?php echo $sel; ?> value="<?php echo str_replace(JPATH_SITE,"",$logo); ?>"><?php echo str_replace($remove,"",$logo); ?></option>
					<?php } ?>
				</select>
			</label>
			<label>
				<div class="preview" id="logo">
					<div id="logo_picked">
						<?php if($this->group->get('logo')) { ?>
							<img src="/site/groups/<?php echo $this->group->get('gidNumber'); ?>/<?php echo $this->group->get('logo'); ?>" alt="<?php echo $this->group->get('cn') ?>" />
						<?php } else { ?>
							<img src="<?php echo $default_logo; ?>" alt="<?php echo $this->group->get('cn') ?>" >
						<?php } ?>
					</div>
				</div>
			</label>
		</fieldset>
		
		<div class="explaination asset-browser-parent">&nbsp;</div>
		<fieldset>
			<legend>Group Main Content</legend>
			<p>This is the content that appears on the main (overview tab) for each group. You can choose to use the default which is your group description and a selection of group members or you can also place custom content using wiki-syntax</p>
			<div class="preview">
				<img src="/components/com_groups/assets/img/group_overview_preview.jpg" alt="Group Overview Content" />
			</div>
			
			<fieldset>
				<legend>Overview Content</legend>
				<p class="side-by-side<?php if($this->group->get('overview_type') == 0) { echo ' checked'; } ?>">
					<label>
						<input type="radio" name="group[overview_type]" id="group_overview_type_default" value="0" <?php if($this->group->get('overview_type') == 0) { echo 'checked'; } ?>> Default Content
					</label>
				</p>
				<p class="side-by-side<?php if($this->group->get('overview_type') == 1) { echo ' checked'; } ?>">
					<label>
						<input type="radio" name="group[overview_type]" id="group_overview_type_custom" value="1" <?php if($this->group->get('overview_type') == 1) { echo 'checked'; } ?>> Custom Content
					</label>
				</p>
				<br class="clear" />
				<label for="group[overview_content]" id="overview_content">
					<strong>Custom Content</strong> <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span>
					<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('group[overview_content]', 'group[overview_content]', stripslashes($this->group->get('overview_content')), '', '50', '15');
					?>
					<span class="hint"><a class="popup" href="/wiki/Help:WikiFormatting">Wiki formatting</a> is allowed.</span>
				</label>
			</fieldset>
		</fieldset>
		
		<div class="explaination asset-browser-parent">&nbsp;</div>
		<fieldset>
			<legend>Group Access</legend>
			<p>Below is a list of all tabs available to groups on this HUB. You can set access permissions on a per group basis by changing the value in the dropdown corresponding with each link. If you have not previously set permissions but notice that some are pre-selected, that is because those are the defaults set until a group manager overrides them.</p>

			<fieldset class="preview">
				<legend>Set Permissions for each Tab</legend>
				<ul id="access">
					<img src="<?php echo $default_logo; ?>" alt="<?php echo $this->group->get('cn') ?>" >
					<?php for($i=0; $i<count($this->hub_group_plugins); $i++) { ?>
						<?php if ($this->hub_group_plugins[$i]['display_menu_tab']) { ?>
							<li class="group_access_control_<?php echo strtolower($this->hub_group_plugins[$i]['title']); ?>">
								<input type="hidden" name="group_plugin[<?php echo $i; ?>][name]" value="<?php echo $this->hub_group_plugins[$i]['name']; ?>">
								<span class="menu_item_title"><?php echo $this->hub_group_plugins[$i]['title']; ?></span>
								<select name="group_plugin[<?php echo $i; ?>][access]">
									<?php foreach($levels as $level => $name) { ?>
										<?php $sel = ($this->group_plugin_access[$this->hub_group_plugins[$i]['name']] == $level) ? 'selected' : ''; ?>
										<?php if(($this->hub_group_plugins[$i]['name'] == 'overview' && $level != 'nobody') || $this->hub_group_plugins[$i]['name'] != 'overview') { ?>
											<option <?php echo $sel; ?> value="<?php echo $level; ?>"><?php echo $name; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</fieldset>
		</fieldset>
		
		<div class="explaination asset-browser-parent">&nbsp;</div>
		<fieldset id="bottom_box">
			<legend>Group Custom Content</legend>
			<p>Group Custom Content includes all the group pages and any group modules at also appear on those pages. Clicking the link below will take you to a different interface where you can add, edit, reorder, turn on/off any group page or module.</p>
			<p>
				<a class="leave_area" rel="You are about to leave the group customization area, and any changes you have made will not be saved. Are you sure you want to continue?" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=pages'); ?>">
					Manage Group Pages
				</a>
			</p>
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>">
		<input type="hidden" name="controller" value="groups" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>">
		<input type="hidden" name="task" value="docustomize">
		<p class="submit">
			<input type="submit" name="group[submit]" value="Save Group Customization" />
		</p>
	</form>
</div>
