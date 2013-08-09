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

$dateFormat = '%d %b. %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M. Y';
	$tz = false;
}

$html  = '';

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = rtrim(stripslashes(ProjectsHtml::cleanText($this->project->about)));

// Transform the wikitext to HTML
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

ximport('Hubzero_Plugin_View');	

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

$privacy = $this->project->private ? JText::_('COM_PROJECTS_PROJECT_PRIVATE_SEARCH') : JText::_('COM_PROJECTS_PROJECT_PUBLIC_SEARCH');
$typetitle = $this->project->projecttype;

if($this->project->state == 1)
{
	$class = 'active';
	$note  = '<span class="' . $class . '" >' 
	. JText::_('COM_PROJECTS_ACTIVE') . '</span> ' . JText::_('COM_PROJECTS_SINCE') . ' '
	. JHTML::_('date', $this->project->created, $dateFormat, $tz);
}
else if($this->project->state == 5)
{
	$class = 'pending';
	$note  = '<span class="' . $class . '" >' 
	. JText::_('COM_PROJECTS_STATUS_PENDING') . '</span> ' . JText::_('COM_PROJECTS_SINCE') . ' '
	. JHTML::_('date', $this->project->created, $dateFormat, $tz);
}
else 
{
	$class = 'inactive';
	$note  = JText::_('COM_PROJECTS_INACTIVE');
}

?>

   <?php if(!$this->reviewer) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
	  	<li><a class="browse" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=browse'); ?>"><?php echo JText::_('COM_PROJECTS_ALL_PROJECTS'); ?></a></li>	
		<li><a class="add" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=start'); ?>"><?php echo JText::_('COM_PROJECTS_START_NEW'); ?></a></li>		
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>
<div id="project-wrap">
 <div class="main section">
	<?php if(($this->authorized or $this->project->owner) && !$this->reviewer) {
		// Public preview for authorized users
	?>
		<div id="project-preview">
			<p><?php echo JText::_('COM_PROJECTS_THIS_IS_PROJECT_PREVIEW'); ?> <span><?php echo JText::_('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias); ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_PAGE'); ?></a></span></p>
		</div>
	<?php 	
	} else if($this->reviewer) { ?>
		<div id="project-preview">
			<p><?php echo JText::_('COM_PROJECTS_REVIEWER_PROJECT_PREVIEW'); ?> <span><?php echo JText::_('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=browse').'?reviewer='. $this->reviewer; ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_LIST'); ?></a></span></p>
		</div>	
	<?php } ?>
  <div class="aside">
	
	<div class="clear"></div>
	<div class="external">
		<ul class="statusbox">
			<li class="pstatus <?php echo $class; ?>"><?php echo $note; ?></li>
			<?php if($this->guest) { ?>
			<li class="expanded"><?php echo JText::_('COM_PROJECTS_ARE_YOU_MEMBER'); ?> <span class="block"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias='.$this->project->alias .a .'task=view') . '?action=login'; ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_LOGIN')).'</a> '.JText::_('COM_PROJECTS_LOGIN_TO_PRIVATE_AREA'); ?></span></li>
			<?php } ?>
		</ul>
	</div>
  </div><!-- / .aside -->
  <div class="subject">	
	<?php echo ProjectsHtml::writeProjectHeader($this, 0, 0, 2); ?>	
	<div id="basic_info">
		<table id="infotbl">
			<tbody>
				<tr>
					<td class="htd"><?php echo JText::_('COM_PROJECTS_TITLE'); ?></td>
					<td><?php echo $this->project->title; ?></td>
						<?php if($this->reviewer && $this->config->get('grantinfo', 0)) { ?>				
							<td <?php if($this->project->about) { echo 'rowspan="2"'; } ?> class="grantinfo">
								<h4><?php echo JText::_('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
								<p>
									<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:</span> <?php echo $this->params->get( 'grant_title'); ?></span>
									<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:</span> <?php echo $this->params->get( 'grant_PI', 'N/A'); ?></span>
									<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:</span> <?php echo $this->params->get( 'grant_agency', 'N/A'); ?></span>
									<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:</span> <?php echo $this->params->get( 'grant_budget', 'N/A'); ?></span>
									<?php if($this->project->role == 1) { ?>
										<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=edit'.a.$goto).'/?edit=settings'; ?>"><?php echo JText::_('COM_PROJECTS_EDIT_THIS'); ?></a>
									<?php } ?>
								</p>
							</td>
						<?php } ?>
				</tr>
				<?php if($this->project->about) { ?>
				<tr>
					<td class="htd"><?php echo JText::_('COM_PROJECTS_ABOUT'); ?></td>
					<td><?php echo $this->project->about; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	
	<?php if ($this->params->get('publications_public', 0)) 
	{ 
		// Show team		
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'publist'
			)
		);
		$view->option 	= $this->option;
		$view->project 	= $this->project;
		echo $view->loadTemplate();
	 } ?>
	
	<?php if ($this->params->get('files_public', 0)) 
	{ 
		// Show team	
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'publist'
			)
		);
		$view->option 	= $this->option;
		$view->project 	= $this->project;
		echo $view->loadTemplate();
	 } ?>
	
	<?php if ($this->params->get('notes_public', 0)) 
	{ 
		// Show team	
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'notes',
				'name'=>'publist'
			)
		);
		$view->option 	= $this->option;
		$view->project 	= $this->project;
		echo $view->loadTemplate();
	 } ?>
	
	<?php if ($this->params->get('team_public', 0)) 
	{ 
		// Show team	
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'team',
				'name'=>'view',
				'layout'=>'horizontal'
			)
		);
		$view->option 	= $this->option;
		$view->project 	= $this->project;
		$view->goto 	= 'alias='.$this->project->alias;
		$view->team 	= $this->team;
		echo $view->loadTemplate();
	 } ?>
  </div><!-- / .subject -->
 </div><!-- / .main section -->
</div>
