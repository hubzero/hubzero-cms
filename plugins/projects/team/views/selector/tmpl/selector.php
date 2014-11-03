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
	<?php if (count($this->team) > 0) {

	?>
		<ul class="team-selector" id="team-selector">
			<?php foreach ($this->team as $owner)
			{
				// Get profile thumb image
				$profile = \Hubzero\User\Profile::getInstance($owner->userid);
				$juser = JFactory::getUser();
				$actor   = \Hubzero\User\Profile::getInstance($juser->get('id'));
				$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);

				$org  = $owner->a_organization ? $owner->a_organization : $owner->organization;
				$name = $owner->a_name ? $owner->a_name : $owner->fullname;
				$name = trim($name) ? $name : $owner->invited_email;

				$username = $owner->username ? $owner->username : JText::_('PLG_PROJECTS_TEAM_SELECTOR_AUTHOR_UNCONFIRMED');

				// Already an author?
				$selected = !empty($this->selected) && in_array($owner->id, $this->selected) ? 1 : 0;
				$class = $selected ? '' : 'allowed';

				?>
				<li id="author-<?php echo $owner->id; ?>" class="type-author <?php echo $class; ?> <?php if ($selected) { echo ' selectedfilter preselected'; } ?>">
					<span class="item-info"><?php echo $org; ?></span>
					<img width="30" height="30" src="<?php echo $thumb; ?>" class="a-ima" alt="<?php echo htmlentities($name); ?>" />
					<span class="a-name"><?php echo $name; ?>
						<span class="a-username">(<?php echo $username; ?>)</span>
					</span>
				</li>
			<?php } ?>
		</ul>
	<?php } else {  ?>
		<p class="noresults"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_NO_MEMBERS'); ?></p>
	<?php } ?>
