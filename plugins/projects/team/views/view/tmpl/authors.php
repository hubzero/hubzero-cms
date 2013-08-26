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

// Build url
$route = $this->project->provisioned 
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
$p_url = JRoute::_($route . a . 'active=team');

// Get image path
$mconfig =& JComponentHelper::getParams( 'com_members' );
$path  = $mconfig->get('webpath');
if (substr($path, 0, 1) != DS) {
	$path = DS.$path;
}
if (substr($path, -1, 1) == DS) {
	$path = substr($path, 0, (strlen($path) - 1));
}

// Get image handler
$ih = new ProjectsImgHandler();

// Get default profile thumb
$default_thumb = $mconfig->get('defaultpic');
if (substr($default_thumb, 0, 1) != DS) {
	$default_thumb = DS.$default_thumb;
}
$shown = array();
?>
<div>	
		<ul id="c-browser" 	<?php if(count($this->team) == 0) { echo 'class="hidden"'; } ?>>
			<?php 
			if(count($this->team) > 0) {		
				$i = 0;
				foreach($this->team as $owner) {
					
					// Get profile thumb image 
					$thumb = '';					
					if($owner->picture) {
						$curthumb = $ih->createThumbName($owner->picture);
						$thumb = $path.DS.Hubzero_View_Helper_Html::niceidformat($owner->userid).DS.$curthumb;
					}
					if (!$thumb or !is_file(JPATH_ROOT.$thumb)) {
						$thumb = $path . DS . Hubzero_View_Helper_Html::niceidformat($owner->userid) . DS . 'thumb.png';
					}
					if (!$thumb or !is_file(JPATH_ROOT.$thumb)) {
						$thumb = $default_thumb;
					}
					
					if(in_array($owner->userid, $this->exclude)) {
						// Skip certain team members if necessary
						continue;
					}
					$shown[] = $owner->id;
					$org = $owner->a_organization ? $owner->a_organization : $owner->organization;
					$name = $owner->a_name ? $owner->a_name : $owner->fullname;
					$name = trim($name) ? $name : $owner->invited_email;

					$username = $owner->username ? $owner->username : JText::_('COM_PROJECTS_AUTHOR_UNCONFIRMED');
					
					 ?>
					<li id="owner:<?php echo $owner->id; ?>" class="c-click  user:<?php echo $owner->userid; ?> owner:<?php echo $owner->id; ?>  name:<?php echo urlencode(htmlspecialchars($name)); ?> org:<?php echo urlencode(htmlspecialchars($org)); ?> credit:<?php echo urlencode(htmlspecialchars($owner->credit)); ?>">
						<img width="30" height="30" src="<?php echo $thumb; ?>" class="a-ima" alt="<?php echo htmlentities($name); ?>" />
						<span class="a-name"><?php echo $name; ?> <span class="block prominent"><?php echo $username; ?></span></span>
					</li>
			<?php		
				$i++;
			?>
			<?php }
			} 
			
			$missing = array();

			// Check for missing items
			if($this->authors) {
				if(count($this->authors) > 0) {
					foreach($this->authors as $member) {
						if($member->project_owner_id && !in_array($member->project_owner_id, $shown)) {
							// Found missing
							$miss = array();
							$miss['owner'] = $member->project_owner_id;
							$miss['userid'] = $member->user_id;
							$miss['picture'] = $member->picture;
							$miss['name'] = stripslashes($member->name);
							$miss['username'] = $member->username;
							$miss['organization'] = stripslashes($member->organization);
							$miss['credit'] = stripslashes($member->credit);
							
							// Get profile thumb image 
							$thumb = '';					
							if($member->picture) {
								$curthumb = $ih->createThumbName($member->picture);
								$thumb = $path.DS.Hubzero_View_Helper_Html::niceidformat($member->user_id).DS.$curthumb;
							}
							if (!$thumb or !is_file(JPATH_ROOT.$thumb)) {
								$thumb = $default_thumb;
							}
							$miss['thumb'] = $thumb;
							$missing[] = $miss;
						}
					}
				}
			}
			
			// Add missing items
			if(count($missing) > 0) {
				foreach ($missing as $miss) { ?>
					<li id="owner:<?php echo $miss['owner']; ?>" class="c-click  user:<?php echo $miss['userid']; ?> owner:<?php echo $miss['owner']; ?>  name:<?php echo urlencode($miss['name']); ?> org:<?php echo urlencode($miss['organization']); ?> credit:<?php echo urlencode($miss['credit']); ?> i-missing">
						<img width="30" height="30" src="<?php echo $miss['thumb']; ?>" class="a-ima" alt="<?php echo htmlentities($miss['name']); ?>" />
						<span class="a-name"><?php echo $miss['name']; ?> <span class="block prominent"><?php echo $miss['username']; ?></span></span>
						<span class="c-missing"><?php echo JText::_('COM_PROJECTS_AUTHORS_MISSING'); ?></span>
					</li>
			<?php	}
			}	
			?>
		</ul>
</div>
