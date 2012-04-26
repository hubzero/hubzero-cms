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

//instantiate autocompleter
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

//is the autocompleter disabled
$disabled = ($this->tos) ? true : false; 

//get autocompleter
$tos = $dispatcher->trigger( 'onGetMultiEntry', array(array('members', 'mbrs', 'members', '', $this->tos, '', $disabled)) );

?>
<form action="<?php echo JRoute::_('index.php?option='.$option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
	<fieldset class="hub-mail">
		<div class="cont">
			<h3>Compose Message</h3>
			<label <?php if ($this->no_html){ echo "class=\"width-65\""; } ?> >
				<?php echo JText::_('PLG_MEMBERS_MESSAGES_TO'); ?> 
				<span class="required">Required</span>
				<?php 
					if (count($tos) > 0) 
					{
						echo $tos[0];
					} 
					else 
					{ 
						echo "<input type=\"text\" name=\"mbrs\" id=\"members\" value=\"\" />";
					} 
				?>
			</label>
			<label>
				<?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?>
				<input type="text" name="subject" id="msg-subject" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT_MESSAGE'); ?>"  />
			</label>
			<label>
				<?php echo JText::_('PLG_MEMBERS_MESSAGES_MESSAGE'); ?> <span class="required">Required</span>
				<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
			</label>
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_SEND'); ?>" />
			</p>
		</div>
	</fieldset>
	
	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="messages" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="action" value="send" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
</form>

<?php if($this->no_html && !JPluginHelper::isEnabled('system', 'jquery')) : ?>
	<script>
		var dochead = document.head,
			scripts = [
				"/plugins/hubzero/autocompleter/textboxlist.js",
				"/plugins/hubzero/autocompleter/observer.js",
				"/plugins/hubzero/autocompleter/autocompleter.js",
			];

		for(i=0;i<scripts.length; i++)
		{
			var include = document.createElement('script');
			include.src = scripts[i];
			dochead.appendChild(include);
		}
	</script>
<?php endif; ?>