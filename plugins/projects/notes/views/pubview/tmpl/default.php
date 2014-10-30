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

$this->css()
	 ->js();

$html = $this->revision->get('pagehtml');

// Com_wiki adds /projects - strip it out
$html = str_replace('projects/projects/', 'projects/', $html);

// Fix up images
$html = str_replace($this->page->get('scope') . DS . $this->page->get('pagename') , 'wiki/' . $this->page->get('id'), $html);

/*
// Replace internal links so that these pages are accessible
$html = projectsHelper::parseNoteRefs($this->page, $this->project->id, $this->masterscope, NULL, $html );

*/
?>

<div class="wiki-wrap">
	<p class="wiki-back"><?php echo JText::_('PLG_PROJECTS_NOTES_PUBLIC_VIEW'); ?> <span class="goback"><a href="<?php echo $this->url; ?>"><?php echo JText::_('PLG_PROJECTS_NOTES_BACK_TO_PROJECT'); ?></a></span></p>
	<div class="wiki-content">
		<h1 class="page-title"><?php echo $this->page->get('title'); ?></h1>
		<div class="wikipage"><?php echo $html; ?></div>
	</div>
</div>
