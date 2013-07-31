<?php 
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
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
	
	/* Resume List */
	$seekers 	= $this->seekers;
	$filters 	= $this->filters;
	$emp 		= $this->emp;
	$admin 		= $this->admin;
	$pageNav 	= $this->pageNav;
	$cats 		= $this->cats;
	$types 		= $this->types;

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<?php if ($this->emp or $this->admin) {  ?>
<div id="content-header-extra">
	<ul id="useroptions">
	<?php if ($this->emp) {  ?>
		<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
		<?php if ($filters['filterby'] == 'shortlisted') { ?>
		<li><a class="complete btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resumes'); ?>"><?php echo JText::_('All Candidates'); ?></a></li>
		<?php } else { ?>
		<li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
		<?php } ?>
	<?php } else {  ?>
		<li>
			<!-- <?php echo JText::_('You are logged in as a site administrator.'); ?> -->
			<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo JText::_('Administrator Dashboard'); ?></a>
		</li>
	<?php } ?>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

<div class="main section">
	<form method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resumes'); ?>">
		<div class="aside">
		<?php if ($filters['filterby'] != 'shortlisted') { ?>
			<fieldset id="matchsearch">
				<label>
					<?php echo JText::_('Sort by'); ?>: 
					<div class="together">
						<input class="option" type="radio" name="sortby" value="lastupdate"<?php if ($filters['sortby']!='bestmatch') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('last update'); ?> &nbsp; 
						<input class="option" type="radio" name="sortby" value="bestmatch"<?php if ($filters['sortby']=='bestmatch') { echo ' checked="checked"'; } else if (!$filters['match']) { echo ' disabled="disabled"'; } ?> /> <?php echo JText::_('best match'); ?>
					</div>
				</label>
				<label>
					<?php echo JText::_('Keywords'); ?>: 
					<span class="questionmark tooltips" title="Keywords Search :: Use skill and action keywords separated by commas, e.g. XML, web, MBA etc."></span>
					<input name="q" maxlength="250" type="text" value="<?php echo $this->escape($filters['search']); ?>" />
				</label>
				<label>
					<?php echo JText::_('Category sought'); ?>:
					<?php echo JobsHtml::formSelect('category', $cats, $filters['category'], '', ''); ?>
				</label>
				<label>
					<?php echo JText::_('Type sought'); ?>:
					<?php echo JobsHtml::formSelect('type', $types, $filters['type'], '', ''); ?>
				</label>
				<label>
					<input class="option" type="checkbox" name="saveprefs" value="1" checked="checked" /> 
					<?php echo JText::_('Save my search preferences'); ?>
				</label>
				<input type="hidden" name="performsearch" value="1" />
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('Search'); ?>" />
				</p>
			</fieldset>
		<?php } else { ?>
			<p>
				<?php echo JText::_('The listed candidates are those you bookmarked for further contact. Return to a list of '); ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resumes'); ?>"><?php echo JText::_('All Candidates'); ?></a>.
			</p>
		<?php } ?>
		</div><!-- / .aside -->
		<div class="subject">
		<?php if ($filters['filterby']== 'shortlisted') { ?>
			<h4><?php echo JText::_('Candidate Shortlist '); ?></h4>
		<?php } ?>
		<?php if (count($seekers) > 0) { // show how many ?>
			<p class="note_total">
				<?php echo JText::_('Displaying '); ?>
				<?php 
				$html = '';
				if ($filters['start'] == 0) {
					$html .= $pageNav->total > count($seekers) ? ' top ' . count($seekers) . ' out of ' . $pageNav->total : strtolower(JText::_('all')) . ' ' . count($seekers);
				} else {
					$html .= ($filters['start'] + 1);
					$html .= ' - ' . ($filters['start'] + count($seekers)) . ' out of ' . $pageNav->total;
				} 
				$html .= ' ';
				$html .= $filters['filterby']=='shortlisted' ? JText::_('shortlisted').' ' : '';
				$html .= strtolower(JText::_('candidates'));
				echo $html;
				?>
			</p>
			
			<ul id="candidates">
			<?php 
			JPluginHelper::importPlugin( 'members','resume' );
			$dispatcher =& JDispatcher::getInstance();	
			foreach ($seekers as $seeker) 
			{
				?>
				<li>
				<?php
				$out = $dispatcher->trigger( 'showSeeker', array($seeker, $emp, $admin, 'com_members', $list=1) );  // show seeker info
				if (count($out) > 0) 
				{
					echo $out[0];
				}
				?>
				</li>
				<?php 
			}
			?>
			</ul>
		<?php } else { // no candidates found ?>
			<p>
				<?php echo $filters['filterby']=='shortlisted' ? JText::_('You haven\'t yet included any candidates on your shortlist. Keep searching!') : JText::_('Sorry, no resumes found at the moment.'); ?>
			</p>
		<?php } ?>
		
		<?php 
		// Insert page navigation
		$pageNav->setAdditionalUrlParam('task', 'resumes');
		$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
		$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
		$pageNav->setAdditionalUrlParam('category', $this->filters['category']);
		$pageNav->setAdditionalUrlParam('type', $this->filters['type']);
		$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
		echo $this->pageNav->getListFooter();
		?>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div>
 