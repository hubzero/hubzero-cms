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

$data 		= $this->data;
$row  		= $this->data->row;
$title 		= $row->title ? $row->title : $row->path;
$details 	= $row->title ? $row->path : NULL;
$viewer 	= $this->data->viewer;

?>
	<li>
		<span class="item-options">
		<?php if ($viewer == 'edit') { ?>
			<span>
				<a href="<?php echo $data->editUrl . '/?action=edititem' . a . 'aid=' . $data->id . a .'p=' . $data->props; ?>" class="showinbox item-edit" title="<?php echo strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_LINK_TITLE')); ?>">&nbsp;</a>
				<a href="<?php echo $data->editUrl . '/?action=deleteitem' . a . 'aid=' . $data->id . a . 'p=' . $data->props; ?>" class="item-remove" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
		<?php } ?>
		</span>
		<span class="item-title link-type">
			 <a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
			<span class="item-details"><?php echo $details; ?></span>
		</span>
	</li>