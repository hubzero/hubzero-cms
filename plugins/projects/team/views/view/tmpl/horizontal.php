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
?>
<div class="public-list-header">
	<h3><?php echo JText::_('COM_PROJECTS_TEAM'); ?></h3>
</div>
<div id="team-horiz" class="public-list-wrap">
	<?php
	if(count($this->team) > 0) { 	?>		
		<ul>
			<?php foreach($this->team as $owner) { 
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
					$thumb = $mconfig->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$thumb;
					}
				}
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
