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

//tag editor
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)) );

//are we using the email gateway for group forum
$params = &JComponentHelper::getParams('com_groups');
$allowEmailResponses = $params->get('email_comment_processing');
$autoEmailResponses  = $params->get('email_member_groupsidcussionemail_autosignup');

//build back link
$host = JRequest::getVar("HTTP_HOST", "", "SERVER");
$referrer = JRequest::getVar("HTTP_REFERER", "", "SERVER");

//check to make sure referrer is a valid url
//check to make sure the referrer is a link within the HUB
if(filter_var($referrer, FILTER_VALIDATE_URL) === false || $referrer == "" || strpos($referrer, $host) === false)
{
	$link = JRoute::_('index.php?option='.$this->option);
}
else
{
	$link = $referrer;
}

//if we are in edit mode we want to redirect back to group
if($this->task == "edit") 
{
	$link = JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn'));
	$title = "Back to Group";
}
else
{
	$title = "Back";
}
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-group group btn" href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<?php if ($this->task != 'new' && !$this->group->get('published')) : ?>
		<p class="warning">
			<?php echo JText::_('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?>
		</p>
	<?php endif; ?>
	
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination asset-browser-parent">
			<div id="asset_browser">
				<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
				<iframe 
					width="100%" 
					height="310" 
					name="filer" 
					id="filer" 
					src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=filebrowser&amp;listdir=<?php echo $this->lid; ?>&amp;tmpl=component"></iframe>
			</div><!-- / .asset_browser -->
		</div>
		<fieldset id="top_box">
			<legend><?php echo JText::_('COM_GROUPS_DETAILS_FIELD_TITLE'); ?></legend>
			
			<?php if ($this->task != 'new') : ?>
				<input name="cn" type="hidden" value="<?php echo $this->group->get('cn'); ?>" />
			<?php else : ?>
				<label class="group_cn_label">
					<?php echo JText::_('COM_GROUPS_DETAILS_FIELD_CN'); ?> <span class="required"><?php echo JText::_('COM_GROUPS_REQUIRED'); ?></span>
					<input name="cn" id="group_cn_field" type="text" size="35" value="<?php echo $this->group->get('cn'); ?>" autocomplete="off" /> 
					<span class="hint"><?php echo JText::_('COM_GROUPS_DETAILS_FIELD_CN_HINT'); ?></span>
				</label>
			<?php endif; ?>
			
			<label>
				<?php echo JText::_('COM_GROUPS_DETAILS_FIELD_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('COM_GROUPS_REQUIRED'); ?></span>
				<input type="text" name="description" size="35" value="<?php echo stripslashes($this->group->get('description')); ?>" />
			</label>
			<label>
				<?php echo JText::_('COM_GROUPS_DETAILS_FIELD_TAGS'); ?> <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span>
				
				<?php if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" value="<?php echo $this->tags; ?>" />
				<?php } ?>

				<span class="hint"><?php echo JText::_('COM_GROUPS_DETAILS_FIELD_TAGS_HINT'); ?></span>
			</label>

			<label for="public_desc">
				<?php echo JText::_('COM_GROUPS_DETAILS_FIELD_PUBLIC'); ?> <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span>
				
				<?php
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('public_desc', 'public_desc', stripslashes($this->group->get('public_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
			<label for="private_desc">
				<?php echo JText::_('COM_GROUPS_DETAILS_FIELD_PRIVATE'); ?> <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span>
				<?php
					echo $editor->display('private_desc', 'private_desc', stripslashes($this->group->get('private_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination asset-browser-parent">&nbsp;</div>
		<fieldset>
			<legend><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_TITLE'); ?></legend>
			<p><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_DESC'); ?></p>
			<fieldset>
				<legend><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_LEGEND'); ?> <span class="required"><?php echo JText::_('COM_GROUPS_REQUIRED'); ?></span></legend>
				<label>
					<input type="radio" class="option" name="join_policy" value="0"<?php if ($this->group->get('join_policy') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING_DESC'); ?></span>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="1"<?php if ($this->group->get('join_policy') == 1) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_DESC'); ?></span>
				</label>
				<label class="indent">
					<strong><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS'); ?></strong>
					(<?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS_DESC'); ?>) <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span>
					<textarea name="restrict_msg" rows="5" cols="50"><?php echo htmlentities(stripslashes($this->group->get('restrict_msg'))); ?></textarea>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="2"<?php if ($this->group->get('join_policy') == 2) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING_DESC'); ?></span>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="3"<?php if ($this->group->get('join_policy') == 3) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING_DESC'); ?></span>
				</label>
			</fieldset>
		</fieldset>
		<div class="clear"></div>
		
		<div class="explaination asset-browser-parent">&nbsp;</div>
		<fieldset <?php if(!$allowEmailResponses) { echo 'id="bottom_box"'; } ?>>
			<legend><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_TITLE'); ?></legend>
			<p>
				<?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_DESC'); ?>
			</p>
			<fieldset>
				<legend><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_LEGEND'); ?> <span class="required"><?php echo JText::_('COM_GROUPS_REQUIRED'); ?></span></legend>
				<label>
					<input type="radio" class="option" name="discoverability" value="0"<?php if ($this->group->get('discoverability') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING_DESC'); ?></span>
				</label>
				<label>
					<input type="radio" class="option" name="discoverability" value="1"<?php if ($this->group->get('discoverability') == 1) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING'); ?></strong>
					<br /><span class="indent"><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING_DESC'); ?></span>
				</label>
			</fieldset>
		</fieldset>

		<?php if ($allowEmailResponses) : ?>
			<div class="explaination asset-browser-parent">&nbsp;</div>
			<fieldset id="bottom_box">
			<legend><?php echo JText::_('COM_GROUPS_EMAIL_SETTINGS_TITLE'); ?></legend>
			<p><?php echo JText::_('COM_GROUPS_EMAIL_SETTINGS_DESC'); ?></p>
				<fieldset>
					<legend><?php echo JText::_('COM_GROUPS_EMAIL_SETTING_FORUM_SECTION_LEGEND'); ?> <span class="optional"><?php echo JText::_('COM_GROUPS_OPTIONAL'); ?></span></legend>
					<label>
						<input type="checkbox" class="option" name="discussion_email_autosubscribe" value="1"
							<?php if ($this->group->get('discussion_email_autosubscribe') == 1) { echo ' checked="checked"'; } ?> /> 
						<strong><?php echo JText::_('COM_GROUPS_EMAIL_SETTING_FORUM_AUTO_SUBSCRIBE'); ?></strong> <br />
						<span class="indent">
							<?php echo JText::_('COM_GROUPS_EMAIL_SETTINGS_FORUM_AUTO_SUBSCRIBE_NOTE'); ?>
						</span>
					</label>
				</fieldset>
			</fieldset>
		<?php endif; ?>
		
		<div class="clear"></div>
		<input type="hidden" name="published" value="<?php echo $this->group->get('published'); ?>" />
		<input type="hidden" name="lid" value="<?php echo $this->lid; ?>" />
		<input type="hidden" name="gidNumber" value="<?php echo $this->group->get('gidNumber'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_GROUPS_EDIT_SUBMIT_BTN_TEXT'); ?>" />
		</p>
	</form>
</div><!-- / .section -->
