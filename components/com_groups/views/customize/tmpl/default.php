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
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn')); ?>"><?php echo JText::_('Back to Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
<div class="main section">
	<form name="customize" method="POST" action="index.php" id="hubForm">
		<div class="explaination">
			<div id="asset_browser">
				<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
				<iframe width="100%" height="300" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=media&amp;listdir=<?php echo $this->group->get('gidNumber'); ?>"></iframe>
			</div><!-- / .asset_browser -->
		</div>
		
		<fieldset id="top_box">
			<h3>Group Logo</h3>
			<p>Upload your logo using the file upload browser to the right first then refresh your browser and select it in the drop down below.</p>
			<label>
				<select name="group[logo]" id="group_logo" rel="<?php echo $this->group->get('gidNumber'); ?>">
					<option value="">Select a group logo...</option>
					<?php for($i=0; $i<count($this->logo_names); $i++) { ?>
						<?php $sel = ($this->logo_names[$i] == $this->group->get('logo')) ? 'selected' : ''; ?>
						<option <?php echo $sel; ?> value="<?php echo $this->logo_fullpaths[$i]; ?>"><?php echo $this->logo_names[$i]; ?></option>
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
		
		<fieldset>
			<h3>Group Main Content</h3>
			<p>This is the content that appears on the main (overview tab) for each group. You can choose to use the default which is your group description and a selection of group members or you can also place custom content using wiki-syntax</p>
			<div class="preview">
				<img  src="/components/com_groups/assets/img/group_overview_preview.jpg" alt="Group Overview Content" />
			</div>
			
			<br />
			<h4>Pick Overview Content Type</h4>
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

			<div id="overview_content">
				<br />
				<h4>Enter Custom Overview Content</h4>
				<label>
					<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('group[overview_content]', 'group[overview_content]', stripslashes($this->group->get('overview_content')), '', '50', '15');
					?>
					<span class="hint"><a class="popup 400x500" href="/topics/Help:WikiFormatting">Wiki formatting</a> is allowed.</span>
				</label>
			</div>	
		</fieldset>
		
		<fieldset>
			<h3>Group Access</h3>
			<p>Below is a list of all tabs available to groups on this HUB. You can set access permissions on a per group basis by changing the value in the dropdown corresponding with each link. If you have not previously set permissions but notice that some are pre-selected, that is because those are the defaults set until a group manager overrides them.</p>
			<br>
			<h4>Set Permissions for each Tab</h4>
			<div class="preview">
				<ul id="access">
					<img src="<?php echo $default_logo; ?>" alt="<?php echo $this->group->get('cn') ?>" >
					<?php for($i=0; $i<count($this->hub_group_plugins); $i++) { ?>
						<li>
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
				</ul>
			</div>
		</fieldset>
		
		<fieldset id="bottom_box">
			<h3>Group Custom Content</h3>
			<p>Group Custom Content includes all the group pages and any group modules at also appear on those pages. Clicking the link below will take you to a different interface where you can add, edit, reorder, turn on/off any group page or module.</p>
			<a class="leave_area" rel="You are about to leave the group customization area, and any changes you have made will not be saved. Are you sure you want to continue?" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages'); ?>">Manage Group Pages</a>
		</fieldset>
		
		<p class="submit"><input type="submit" name="group[submit]" value="Save Group Customization" /></p>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>">
		<input type="hidden" name="task" value="savecustomization">
		<input type="hidden" name="gidNumber" value="<?php echo $this->group->get('gidNumber'); ?>">
	</form>
</div>