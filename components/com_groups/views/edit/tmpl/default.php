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

if ($this->group->get('gidNumber')) {
	$lid = $this->group->get('gidNumber');
} else {
	$lid = time().rand(0,10000);
}

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->tags)) );
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('GROUPS_ALL_GROUPS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->task != 'new' && !$this->group->get('published')) { ?>
	<p class="warning"><?php echo JText::_('GROUPS_STATUS_NEW_GROUP'); ?></p>
<?php } ?>
	
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('Upload files or images to use in your description:'); ?></p>
			<iframe width="100%" height="370" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=media&amp;listdir=<?php echo $lid; ?>"></iframe>
		</div>
		<fieldset>
			<legend><?php echo JText::_('GROUPS_EDIT_DETAILS'); ?></legend>
<?php if ($this->task != 'new') { ?>
			<input name="cn" type="hidden" value="<?php echo $this->group->get('cn'); ?>" />
<?php } else { ?>
			<label<?php if ($this->task == 'save' && !$this->group->get('cn')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('GROUPS_ID'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span>
				<input name="cn" type="text" size="35" value="<?php echo $this->group->get('cn'); ?>" /> <span class="hint"><?php echo JText::_('GROUPS_ID_HINT'); ?></span>
			</label>
<?php } ?>
<?php if ($this->task == 'save' && !$this->group->get('cn')) { ?>
			<p class="error"><?php echo JText::_('GROUPS_ERROR_PROVIDE_ID'); ?></p>
<?php } ?>
<?php if ($this->task == 'save' && $this->group->get('cn') && !$this->valid_cn) { ?>
			<p class="error"><?php echo JText::_('GROUPS_ERROR_INVALID_ID'); ?></p>
<?php } ?>

			<label<?php if ($this->task == 'save' && !$this->group->get('description')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('GROUPS_TITLE'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span>
				<input type="text" name="description" size="35" value="<?php echo htmlentities(stripslashes($this->group->get('description'))); ?>" />
			</label>
<?php if ($this->task == 'save' && !$this->group->get('description')) { ?>
			<p class="error"><?php echo JText::_('GROUPS_ERROR_PROVIDE_TITLE'); ?></p>
<?php } ?>
			<label>
				<?php echo JText::_('GROUPS_FIELD_TAGS'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
<?php if (count($tf) > 0) {
	echo $tf[0];
} else { ?>
				<input type="text" name="tags" value="<?php echo $this->tags; ?>" />
<?php } ?>
				<span class="hint"><?php echo JText::_('GROUPS_FIELD_TAGS_HINT'); ?></span>
			</label>

			<label>
				<?php echo JText::_('GROUPS_EDIT_PUBLIC_TEXT'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
				<?php
				ximport('Hubzero_Wiki_Editor');
				$editor =& Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('public_desc', 'public_desc', stripslashes($this->group->get('public_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup 400x500" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
			<label>
				<?php echo JText::_('GROUPS_EDIT_PRIVATE_TEXT'); ?> <span class="optional"><?php echo JText::_('GROUPS_OPTIONAL'); ?></span>
				<?php
				echo $editor->display('private_desc', 'private_desc', stripslashes($this->group->get('private_desc')), '', '50', '15');
				?>
				<span class="hint"><a class="popup 400x500" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination">
			<p><?php echo JText::_('GROUPS_EDIT_CREDENTIALS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('GROUPS_EDIT_MEMBERSHIP'); ?></legend>
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

		<div class="explaination">
			<p><?php echo JText::_('GROUPS_ACCESS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('Access Settings'); ?></legend>
			<fieldset>
				<legend><?php echo JText::_('GROUPS_PRIVACY'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span></legend>
				<label>
					<input type="radio" class="option" name="privacy" value="0"<?php if ($this->group->get('privacy') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?></strong> <span class="indent">Group is discoverable (searches, etc.) and viewable by anyone.</span>
				</label>
				<label>
					<input type="radio" class="option" name="privacy" value="1"<?php if ($this->group->get('privacy') == 1) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?></strong> <span class="indent">Group is discoverable (searches, etc.) and viewable by registered users only.</span>
				</label>
				<label>
					<input type="radio" class="option" name="privacy" value="4"<?php if ($this->group->get('privacy') == 4) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?></strong> <span class="indent">Group is not discoverable (searches, etc.) and viewable only by members.</span>
				</label>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('Content Privacy'); ?> <span class="required"><?php echo JText::_('GROUPS_REQUIRED'); ?></span></legend>
				<p><?php echo JText::_('GROUPS_PRIVACY_HINT'); ?></p>
				<label>
					<input type="radio" class="option" name="access" value="0"<?php if ($this->group->get('access') == 0) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?></strong> <span class="indent"><?php echo JText::_('GROUPS_ACCESS_PUBLIC_EXPLANATION'); ?></span>
				</label>
				<label>
					<input type="radio" class="option" name="access" value="3"<?php if ($this->group->get('access') == 3) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?></strong> <span class="indent"><?php echo JText::_('GROUPS_ACCESS_PROTECTED_EXPLANATION'); ?></span>
				</label>
				<label>
					<input type="radio" class="option" name="access" value="4"<?php if ($this->group->get('access') == 4) { echo ' checked="checked"'; } ?> /> 
					<strong><?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?></strong> <span class="indent"><?php echo JText::_('GROUPS_ACCESS_PRIVATE_EXPLANATION'); ?></span>
				</label>
			</fieldset>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="gidNumber" value="<?php echo $this->group->get('gidNumber'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
		</p>
	</form>
</div><!-- / .section -->