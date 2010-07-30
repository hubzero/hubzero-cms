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

?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=datasharing'); ?>" method="post" enctype="multipart/form-data" id="error-wrap">
	<fieldset id="error-box" class="code-300">
		<h2><?php echo JText::_('PLG_GROUPS_DROPBOX'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>

		<p><?php echo JText::_('PLG_GROUPS_DROPBOX_DESCRIPTION'); ?></p>
			
		<p class="submit">
			<?php echo JText::_('PLG_GROUPS_DROPBOX_DOES_NOT_EXIT'); ?> <input type="submit" value="<?php echo JText::_('PLG_GROUPS_DROPBOX_SUBMIT'); ?>" />
		</p>

		<input type="hidden" name="shareddir" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="createbox" />
		<input type="hidden" name="active" value="datasharing" />
	</fieldset><!-- / #error-box -->
</form><!-- / #error-wrap -->