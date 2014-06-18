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

$project = $this->pub->_project;

$prov = $this->pub->_project->provisioned ? 1 : 0;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$url = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

$move = '';
$title = $this->manifest->title;

?>

<div id="pub-editor" class="pane-desc freeze">
 	<div id="c-pane" class="columns">
		 <div class="c-inner">
			<h4><?php echo $title; ?></h4>
			<div class="block-aside">
				<div class="block-info">
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LOCKED'); ?>
					<?php if ($this->pub->state == 1) {
						echo ' <a href="'.$url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; } ?>
					</p>
				</div>
			</div>
			<div class="block-subject">
			<?php echo $this->content; ?>
			</div>
		 </div>
	</div>
</div>