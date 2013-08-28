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

$yearFormat = '%Y';
$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat = 'Y';
	$dateFormat = 'm/d/Y';
	$tz = false;
}

// Version status
$status = PublicationHelper::getPubStateProperty($this->pub, 'status');
$class = PublicationHelper::getPubStateProperty($this->pub, 'class');

$v = $this->version == 'default' ? '' : '?v='.$this->version;

// Get hub config
$jconfig =& JFactory::getConfig();
$site = $jconfig->getValue('config.live_site');

$now = date( 'Y-m-d H:i:s', time() );

// Build our citation object
$citation = '';
if ($this->pub->doi)
{
	include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	$cite = new stdClass();
	$cite->title = $this->pub->title;
	$cite->year = JHTML::_('date', $this->pub->published_up, $yearFormat, $tz);

	$cite->location = '';
	$cite->date = '';
	
	// Get version authors
	$pa = new PublicationAuthor( $this->database );
	$authors = $pa->getAuthors($this->pub->version_id);

	$cite->url = $site . DS . 'publications' . DS . $this->pub->id.'?v='.$this->pub->version_number;
	$cite->type = '';
	$helper = new PublicationHelper($this->database, $this->pub->version_id, $this->pub->id);
	$cite->author = $helper->getUnlinkedContributors( $this->authors);
	$cite->doi = $this->pub->doi;
	$citation = CitationFormat::formatReference($cite);
}

// Get creator name
$profile = Hubzero_User_Profile::getInstance($this->pub->created_by);
$creator = $profile->get('name') . ' (' . $profile->get('username') . ')';

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">	
	<?php echo $this->project->provisioned == 1 
				? PublicationHelper::showPubTitleProvisioned( $this->pub, $this->route)
				: PublicationHelper::showPubTitle( $this->pub, $this->route, $this->title); ?>
	<fieldset>	
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
		<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="active" value="publications" />					
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
		<?php if($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>

<?php
// Include status bar - publication steps/sections/version navigation
$view = new Hubzero_Plugin_View(
	array(
		'folder'=>'projects',
		'element'=>'publications',
		'name'=>'edit',
		'layout'=>'statusbar'
	)
);
$view->row = $this->row;
$view->version = $this->version;
$view->panels = $this->panels;
$view->active = $this->active;
$view->move = $this->move;
$view->lastpane = $this->lastpane;
$view->option = $this->option;
$view->project = $this->project;
$view->current_idx = $this->current_idx;
$view->last_idx = $this->last_idx;
$view->checked = $this->checked;
$view->url = $this->url;
$view->display();

// Section body starts:
?>
<div id="pub-body" class="<?php echo $this->version; ?>">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
			<h4><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$this->row->version_label.' ('.$status.')'; ?></h4>
			<table class="tbl-panel">
				<tbody>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>:</td>
						<td class="tbl-input"><span><?php echo $this->row->title; ?></span></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL'); ?>:</td>
						<td class="tbl-input"><span <?php if(($this->version == 'dev' || $this->row->state == 4) && $this->task != 'edit') { echo 'id="edit-vlabel" class="pub-edit"'; } ?>><?php echo $this->row->version_label;  ?></span> <?php if($this->pub->main == 1) { echo '<span id="v-label">('.JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_DEFAULT').')</span>'; } ?></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_NUMBER'); ?>:</td>
						<td class="tbl-input"><span><?php echo $this->row->version_number;  ?></span><?php if($this->pub->versions) { ?> &nbsp; &nbsp;<span >[<a href="<?php echo $this->url . '/?action=versions'; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_VERSIONS'); ?></a>]</span><?php } ?></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CREATED')); ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $this->row->created, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->row->created).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CREATED_BY')); ?>:</td>
						<td class="tbl-input"><?php echo $creator; ?></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT')); ?>:</td>
						<td class="tbl-input"><?php echo strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_'.strtoupper($this->pub->base))); ?></td>
					</tr>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>:</td>
						<td class="tbl-input">
							<span class="<?php echo $class; ?>"> <?php echo $status; ?></span>
							<?php if ($this->row->published_up > $now ) { ?>
							<span class="embargo"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_EMBARGO') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_UNTIL') . ' ' . JHTML::_('date', $this->row->published_up, $dateFormat, $tz); ?></span>	
							<?php } ?>
						</td>
					</tr>
					<?php if($this->row->doi) { ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOI'); ?>:</td>
						<td class="tbl-input"><?php echo $this->row->doi ? $this->row->doi : JText::_('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
						<?php if($this->row->doi) { echo ' <a href="' . $this->config->get('doi_verify', 'http://n2t.net/ezid/id/') . 'doi:' . $this->row->doi . '" rel="external">[&rarr;]</a>'; } ?>
						</td>
					</tr>
					<?php } ?>
					<?php if(($this->pubconfig->get('issue_arch') && $this->pub->state == 6) || $this->row->ark) { ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ARCH'); ?>:</td>
						<td class="tbl-input"><?php echo $this->row->ark ? $this->row->ark : JText::_('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
						<?php if($this->row->ark) { echo ' <a href="' . $this->config->get('doi_verify', 'http://n2t.net/ezid/id/') . 'ark:' . $this->row->ark . '" rel="external">[&rarr;]</a>'; } ?>
						</td>
					</tr>
					<?php } ?>
					<?php if ($this->pub->state == 1 || $this->pub->state == 0) { ?>
					<?php 
						if ($this->row->published_up > $now && $this->row->submitted != '0000-00-00 00:00:00')  { ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'); ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $this->row->submitted, $dateFormat, $tz); ?></td>
					</tr>		
							
					<?php } elseif ($this->row->published_up <= $now) { ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLISH_FROM'); ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $this->row->published_up, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->row->published_up).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
					</tr>
					<?php } ?>
					<?php if($this->row->accepted != '0000-00-00 00:00:00') { ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCEPTED'); ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $this->row->accepted, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->row->accepted).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
					</tr>	
					<?php } ?>
					<?php } elseif ($this->pub->state != 3) { 
						$date = $this->row->published_up;
						if($this->pub->state == 5) {
							$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED');
							$date = $this->row->submitted != '0000-00-00 00:00:00' ? $this->row->submitted : $this->row->published_up;
						}
						elseif ($this->pub->state == 4) 
						{
							$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_FINALIZED');	
						}
						elseif ($this->pub->state == 6) 
						{
							$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_ARCHIVED');	
						}
						else {
							$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASED');
						}
					?>
					<tr>
						<td class="tbl-lbl"><?php echo $show_action; ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $date, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($date).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
					</tr>
					<?php } ?>
					<?php if($this->pub->state == 0) { ?>
					<tr>
						<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_UNPUBLISHED')); ?>:</td>
						<td class="tbl-input"><?php echo JHTML::_('date', $this->row->published_down, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->row->published_down).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_URL'); ?>:</td>
						<td class="tbl-input"><a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'id='.$this->pub->id.$v); ?>"><?php echo trim($site, DS) .'/publications/'.$this->pub->id.$v; ?></a></td>
					</tr>
				</tbody>
			</table>
			<?php if($this->version == 'dev') { ?>
				<p class="c-instruct js"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_HINT_LABEL'); ?></p>
			<?php } ?>
		 </div>
		</div>
		<div class="two columns second" id="c-output">
		 <div class="c-inner">
			<h4>
			<?php if($this->version == 'dev' || $this->row->state == 5) { ?>
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT'); ?>
			<?php } else { ?>
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_YOUR_OPTIONS'); ?>
			<?php } ?>
			</h4>
			
			<ul class="next-options">
			<?php if($this->version == 'dev' || $this->row->state == 4) { // draft (initial or final) ?>	
				<?php if(!$this->publication_allowed) { ?> 
				<li id="next-edit"><p><?php 
					echo '<strong>'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE').'</strong> '.
					JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISH_MISSING'); 
					$missing = '';
					foreach ($this->checked as $key => $value) { 
						if($value == 0) { 
							$missing .= ' <a href="'
							. $this->url . '/?section='.
							$key.a.'version='.$this->version.'">'.strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_'.strtoupper($key))).'</a>,';						
						} 
					} 
					$missing = substr($missing,0,strlen($missing) - 1);
					echo '<strong>'.$missing.'</strong>';
					echo ' '.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_INFORMATION');
 					?></p>
				</li>
				<?php } ?>
				<li id="next-publish"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISH_READY');  ?> <?php if ($this->config->get('doi_service')) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISH_DOI');  } ?></p>
					<p class="centeralign"><span class="<?php echo $this->publication_allowed ? 'btn' : 'btncancel'; ?>"><?php if($this->publication_allowed) {  ?><a href="<?php echo $this->url.'/?action=publish'. a . 'version='.$this->version; ?>"><?php } ?><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_SUBMIT_TO_PUBLISH_REVIEW'); ?><?php if($this->publication_allowed) {  ?></a><?php } ?></span></p>
				</li>
				<?php if($this->row->state != 4 && $this->publication_allowed) { ?>
				<li id="next-ready"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_SAVE');  ?></p>
					<p class="centeralign"><span class="<?php echo $this->publication_allowed ? 'btn' : 'btncancel'; ?>"><?php if($this->publication_allowed) {  ?><a href="<?php echo $this->url . '/?action=post'. a . 'version='.$this->version; ?>"><?php } ?><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_SAVE_REVIEW'); ?><?php if($this->publication_allowed) {  ?></a><?php } ?></span></p>
				</li>
				<?php } ?>
				<li id="next-cancel"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEED_TO_CANCEL').' <a href="'.$this->url.'/?action=cancel' . a . 'version='.$this->version.'">'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL').'</a> '.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL_BEFORE');  ?></p></li>				
			<?php } ?>	
			<?php if($this->row->state == 6) { ?>
					<li id="next-archive"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DARKARCHIVE_NO_OPTIONS'); ?> <?php if($this->row->ark) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DARKARCHIVE_ARK') . ' <span class="prominent">ark:' . $this->row->ark . '</span>'; } ?></p></li>
			<?php } ?>
			<?php if($this->row->state == 1 || $this->row->state == 6 || $this->row->state == 0) { // new version allowed ?>				
				<?php if($this->pub->dev_version_label && $this->pub->dev_version_label != $this->pub->version_label) { ?>
				<li id="next-draft"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_VERSION_STARTED')
				.' (<strong>v.'
				.$this->pub->dev_version_label.'</strong>)  <a href="'
				. $this->url .'/?version=dev">' 
				. JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION_CONTINUE').'</a>';  ?></p></li>
				<?php } else if(!$this->pub->dev_version_label) { ?>
				<li id="next-newversion"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CHANGES_NEEDED')
				.' <a href="' . $this->url .'/?action=newversion" class="showinbox">'
				.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION').'</a> ';  ?></p></li>
				<?php } ?>
			<?php } ?>
					
			<?php if ($this->row->state == 1) { // published ?>	
				<?php if ($this->typeParams->get('option_unpublish', 0) == 1) { ?>		
				<li id="next-cancel"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISHED_UNPUBLISH');  
				echo ' <a href="' . $this->url . '/?action=cancel' . a . 'version='.$this->version . '">'
				.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISH_VERSION').' &raquo;</a> ';  ?></p></li>
				<?php } ?>				
				<li id="next-usage"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_STATS') 
				.' <strong>'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_USAGE_STATS').'</strong> '
				.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_FOLLOW_FEEDBACK');  ?>
					<span class="block italic"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_FEATURE_IN_DEVELOPMENT'); ?></span></p></li>
			<?php } ?>
			
			<?php if ($this->row->state == 5) { // pending approval ?>
				<li id="next-pending">
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING');  ?>	</p>
					<?php if($this->row->doi) { 
						echo '<p>' . JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING_DOI_ISSUED') . '</p>' 
						. '<div class="citeit">' . $citation . '</div>'; } ?>
				</li>
				<li id="next-ready"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING_REVERT');  
				echo ' <a href="' . $this->url . '/?action=revert' . a . 'version=' . $this->version . '" id="confirm-revert">'
				.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_REVERT').' &raquo;</a> ';  ?></p></li>					
			<?php } ?>
			
			<?php if ($this->row->state == 0) { // unpublished				
					// Check who unpublished this
					$objAA = new ProjectActivity( $this->database );
					$pubtitle = Hubzero_View_Helper_Html::shortenText($this->row->title, 100, 0);
					$activity = JText::_('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_UNPUBLISHED'); 
					$activity .= ' '.strtolower(JText::_('version')).' '.$this->row->version_label.' '
					.JText::_('PLG_PROJECTS_PUBLICATIONS_OF').' '.strtolower(JText::_('publication')).' "'
					.$pubtitle.'" ';

					$admin = $objAA->checkActivity( $this->project->id, $activity);	
				 ?>
				<?php if($this->publication_allowed && $admin != 1) { ?> 
				<li id="next-publish"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_PUBLISH')
				.' <a href="' . $this->url . '/?action=republish' . a . 'version=' . $this->version.'">'
				.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REPUBLISH').' &raquo;</a>';  ?></p></li>
				<?php } ?>
				<?php if($admin == 1) { ?> 
				<li id="next-question"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_BY_ADMIN');  ?></p></li>
				<?php } ?>
			<?php } ?>
			</ul>
		 </div>
		</div>
	</div>
</div>
</form>
