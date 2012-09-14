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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//tag editor
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)) );

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
	$link = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn'));
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
			<a class="group btn" href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<?php if ($this->task != 'new' && !$this->group->get('published')) { ?>
		<p class="warning"><?php echo JText::_('GROUPS_STATUS_NEW_GROUP'); ?></p>
	<?php } ?>
	
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<div id="asset_browser">
				<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
				<iframe width="100%" height="310" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;task=media&amp;listdir=<?php echo $this->lid; ?>"></iframe>
			</div><!-- / .asset_browser -->
		</div>
		<fieldset id="top_box">
			<legend><?php echo JText::_('GROUPS_EDIT_DETAILS'); ?></legend>
<?php if ($this->task != 'new') { ?>
			<input name="cn" type="hidden" value="<?php echo $this->group->get('cn'); ?>" />
<?php } else { ?>
			<label class="group_cn_label">
				<?php echo JText::_('GROUPS_ID'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span>
				<input name="cn" id="group_cn_field" type="text" size="35" value="<?php echo $this->group->get('cn'); ?>" autocomplete="off" /> 
				<span class="hint"><?php echo JText::_('GROUPS_ID_HINT'); ?></span>
			</label>
<?php } ?>

			<label>
				<?php echo JText::_('GROUPS_TITLE'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span>
				<input type="text" name="description" size="35" value="<?php echo stripslashes($this->group->get('description')); ?>" />
			</label>
			<label>
				<?php echo JText::_('GROUPS_FIELD_TAGS'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
				
				<?php if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" value="<?php echo $this->tags; ?>" />
				<?php } ?>

				<span class="hint"><?php echo JText::_('GROUPS_FIELD_TAGS_HINT'); ?></span>
			</label>

			<label for="public_desc">
				<?php echo JText::_('GROUPS_EDIT_PUBLIC_TEXT'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
				
				<?php
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('public_desc', 'public_desc', stripslashes($this->group->get('public_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
			<label for="private_desc">
				<?php echo JText::_('GROUPS_EDIT_PRIVATE_TEXT'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
				<?php
					echo $editor->display('private_desc', 'private_desc', stripslashes($this->group->get('private_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
		</fieldset>
		<div class="clear"></div>

		<fieldset>
			<legend><?php echo JText::_('GROUPS_EDIT_MEMBERSHIP'); ?></legend>
			<p><?php echo JText::_('GROUPS_EDIT_CREDENTIALS_EXPLANATION'); ?></p>
			<fieldset>
				<legend><?php echo JText::_('Who can join?'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span></legend>
				<label>
					<input type="radio" class="option" name="join_policy" value="0"<?php if ($this->group->get('join_policy') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('Anyone'); ?></strong> <span class="indent">Membership requests are automatically accepted (no pending status).</span>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="1"<?php if ($this->group->get('join_policy') == 1) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('Restricted'); ?></strong> <span class="indent">Membership requests are pending and must be approved/denied by a manager.</span>
				</label>
				<label class="indent">
					<?php echo JText::_('GROUPS_EDIT_CREDENTIALS'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
					<textarea name="restrict_msg" rows="5" cols="50"><?php echo htmlentities(stripslashes($this->group->get('restrict_msg'))); ?></textarea>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="2"<?php if ($this->group->get('join_policy') == 2) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('Invite Only'); ?></strong> <span class="indent">Membership can only be gained through an invite.</span>
				</label>
				<label>
					<input type="radio" class="option" name="join_policy" value="3"<?php if ($this->group->get('join_policy') == 3) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('Closed'); ?></strong> <span class="indent">Membership cannot be requested.</span>
				</label>
			</fieldset>
		</fieldset>
		<div class="clear"></div>
		
		<?php
			$params = &JComponentHelper::getParams('com_groups');
			$allowEmailResponses = $params->get('email_member_groupsidcussionemail_autosignup');
		?>
		
		<fieldset <?php if(!$allowEmailResponses) { echo 'id="bottom_box"'; } ?>>
			<legend><?php echo JText::_('Discoverability Settings'); ?></legend>
			<p><?php echo JText::_('GROUPS_ACCESS_EXPLANATION'); ?></p>
			<fieldset>
				<legend><?php echo JText::_('GROUPS_PRIVACY'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span></legend>
				<label>
					<input type="radio" class="option" name="privacy" value="0"<?php if ($this->group->get('privacy') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_VISIBLE'); ?></strong> <br /><span class="indent">Group can be found in searches and by browsing groups.</span>
				</label>
				<label>
					<input type="radio" class="option" name="privacy" value="1"<?php if ($this->group->get('privacy') == 1) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_HIDDEN'); ?></strong> <br /><span class="indent">Group can not be found through searches and only viewable by group members.</span>
				</label>
			</fieldset>
		</fieldset>

		<?php if ($allowEmailResponses) : ?>
			<fieldset id="bottom_box">
			<legend><?php echo JText::_('GROUPS_SETTINGS'); ?></legend>
			<p><?php echo JText::_('GROUP_LEVEL_CONFIGURATION_OPTIONS'); ?></p>
				<fieldset>
					<legend><?php echo JText::_('GROUPS_SETTING_EMAIL'); ?></legend>
					<label>
						<input type="checkbox" class="option" name="discussion_email_autosubscribe" value="1"
							<?php if ($this->group->get('discussion_email_autosubscribe') == 1) { echo ' checked="checked"'; } ?> /> 
						<strong><?php echo JText::_('GROUPS_SETTING_AUTO_SUBSCRIBE_NEW_USERS_TO_FORUM_EMAIL'); ?></strong> <br />
						<span class="indent">
							<?php echo JText::_('GROUPS_SETTING_AUTO_SUBSCRIBE_NEW_USERS_TO_FORUM_EMAIL_NOTE'); ?>
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
			<input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
		</p>
	</form>
</div><!-- / .section -->
