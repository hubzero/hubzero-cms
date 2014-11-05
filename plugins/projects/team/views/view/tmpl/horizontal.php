<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
<div class="public-list-header">
	<h3><?php echo JText::_('COM_PROJECTS_TEAM'); ?></h3>
</div>
<div id="team-horiz" class="public-list-wrap">
	<?php
	if (count($this->team) > 0) { 	?>
		<ul>
			<?php foreach ($this->team as $owner)
			{
				// Get profile thumb image
				$profile = \Hubzero\User\Profile::getInstance($owner->userid);
				$juser 	 = JFactory::getUser();
				$actor   = \Hubzero\User\Profile::getInstance($juser->get('id'));
				$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);
			?>
			<li>
				<img width="50" height="50" src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname; ?>" />
				<span class="block"><a href="/members/<?php echo $owner->userid; ?>"><?php echo $owner->fullname; ?></a></span>
			</li>
			<?php }	?>
			<li class="clear">&nbsp;</li>
		</ul>
	<?php } else { ?>
		<div class="noresults"><?php echo JText::_('COM_PROJECTS_EXTERNAL_NO_TEAM'); ?></div>
	<?php }	?>
	<div class="clear"></div>
</div>
