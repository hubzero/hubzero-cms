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

$prov = $this->pub->_project->provisioned == 1 ? 1 : 0;

// Get block properties
$step 	  = $this->step;
$block	  = $this->pub->_curationModel->_progress->blocks->$step;
$complete = $block->status->status;
$name	  = $block->name;

$props = $name . '-' . $this->step;

// Build url
$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=publications&action=select'
		: JRoute::_( $route . '&active=publications&action=select') .'/?p=' . $props . '&pid='
		. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);

// Are we in draft flow?
$move = JRequest::getVar( 'move', '' );
$move = $move ? '&move=continue' : '';

$required 		= $this->manifest->params->required;

$elName = "licensePick";

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> freezeblock">
		<?php if ($this->license) {
			$info = $this->license->info;
			if ($this->license->url) {
				 $info .= ' <a href="' . $this->license->url . '" class="popup">Read license terms &rsaquo;</a>';
			}
			?>
			<div class="chosenitem">
				<p class="item-title">
			<?php if ($this->license) { echo '<img src="' . $this->license->icon . '" alt="' . htmlentities($this->license->title) . '" />'; } ?><?php echo $this->license->title; ?>
					<span class="item-details"><?php echo $info; ?></span>
				</p>
			</div>

		<?php } else { ?>
			<?php echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>'; ?>
		<?php } ?>
</div>