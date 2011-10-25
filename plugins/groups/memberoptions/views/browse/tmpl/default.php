<?php
/**
 * @package     hubzero-cms
 * @author      David Benham <dbenham@purdue.edu>
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
$juser =& JFactory::getUser();

?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=memberoptions'); ?>" method="post" id="memberoptionform">
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="savememberoptions" />
	<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID;?>" />

	<div>
		<input type="checkbox" id="recvpostemail" value="1" name="recvpostemail" <?php if($this->recvEmailOptionValue == 1) echo "checked"; else echo "";?> > 
		<label for="recvpostemail"><?php echo JText::_('PLG_GROUPS_RECEIVE_EMAILS_DISCUSSION_POSTS'); ?></label>
	</div>

	<input type="submit" value="Save">
	
</form>
	
