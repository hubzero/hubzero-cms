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
$html  = '';

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = rtrim(stripslashes(ProjectsHtml::cleanText($this->project->about)));

// Transform the wikitext to HTML
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

//import the wiki parser
$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => $this->project->alias.DS.'notes',
	'pagename' => 'projects',
	'pageid'   => $this->project->id,
	'filepath' => $this->config->get('webpath'),
	'domain'   => $this->project->alias
);
$this->project->about = $p->parse( $this->project->about, $wikiconfig );
?>
<div id="project-wrap">
 <div class="main section">
	<?php echo ProjectsHtml::writeProjectHeader($this, '', 1); ?>	
	<div id="confirm-invite">	
		<div class="columns three first">
			<h3><?php echo JText::_('COM_PROJECTS_INVITED_CONFIRM'); ?></h3>
			<p class="invitation"><?php echo JText::_('COM_PROJECTS_INVITED_CONFIRM_SCREEN').' "'.$this->project->title.'" '. JText::_('COM_PROJECTS_INVITED_NEED_ACCOUNT').' <a href="/register">'.JText::_('COM_PROJECTS_INVITED_CREATE_NEW_ACCOUNT').'</a> '.JText::_('COM_PROJECTS_INVITED_IF_NO_ACCOUNT'); ?>
			</p>
		</div>
		<div class="columns three second third">
		<?php
		ximport('Hubzero_Module_Helper');
		echo Hubzero_Module_Helper::displayModules('force_mod');
		?>
		</div>
		<div class="clear"></div>
	</div>
 </div><!-- / .main section -->
</div>