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

$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->option . '&task=' . $this->task), 'server');

?>
<div id="project-wrap">
 <div class="main section">
	<?php echo ProjectsHtml::writeProjectHeader($this, '', 1); ?>
	<h3><?php echo JText::_('COM_PROJECTS_INVITED_CONFIRM'); ?></h3>	
	<div id="confirm-invite" class="invitation">	
		<div class="columns two first">
			<p><?php echo JText::_('COM_PROJECTS_INVITED_CONFIRM_SCREEN').' "'.$this->project->title.'". '. JText::_('COM_PROJECTS_INVITED_NEED_ACCOUNT_TO_JOIN'); ?>
			</p>
		</div>
		<div class="columns two second">
			<p>
			<?php echo JText::_('COM_PROJECTS_INVITED_HAVE_ACCOUNT') . ' <a href="' . JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)) .  '">' . JText::_('COM_PROJECTS_INVITED_PLEASE_LOGIN') . '</a>'; ?>
			</p>
			<p>
			<?php echo JText::_('COM_PROJECTS_INVITED_DO_NOT_HAVE_ACCOUNT') . ' <a href="' . JRoute::_('index.php?option=com_register&return=' . base64_encode($rtrn)) .  '">' . JText::_('COM_PROJECTS_INVITED_PLEASE_REGISTER') . '</a>'; ?>
			</p>
		</div>
		<div class="clear"></div>
	</div>
 </div><!-- / .main section -->
</div>