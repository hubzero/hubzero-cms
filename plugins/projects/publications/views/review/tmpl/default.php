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

// Display publication type
$typetitle = PublicationHelper::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);
$status = PublicationHelper::getPubStateProperty($this->pub, 'status');
$sClass = PublicationHelper::getPubStateProperty($this->pub, 'class');

// CSS class
$class = $this->publication_allowed ? 'maypublish' : 'draft_incomplete';
$class = ($this->version == 'default' || $this->row->state == 1) ? 'maypublish' : $class;

// Posting or publishing?
$post = $this->task == 'post' ? 1 : 0;
$archive = $this->task == 'archive' ? 1 : 0;
$republish = $this->task == 'republish' ? 1 : 0;

$txt = '';
$doi_txt = '';

switch ($this->task) 
{
	case 'publish':
	default: 			
		$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_PUBLISH_IT');
		$doi_txt =  JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_PUBLISH_DOI');		
		break;
	case 'republish': 			
		$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_REPUBLISH');
		break;
	case 'post': 
		$class .= ' posting';
		$doi_txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_POST_NO_DOI');		
		$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_SAVE_IT');
		break;
	case 'archive': 
		$class .= ' darkarchive';			
		$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_ARCHIVE_IT');
		$doi_txt = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_ARCHIVE_NO_DOI');
		break;
}

$class .= ($this->task == 'publish' || $this->task == 'republish') ? ' publishing' : '';

$serveas = 'download';

// Are we allowed to publish?
$canpublish = ($this->pub->state == 1 
				|| $this->pub->state == 5 
				|| $this->pub->state == 0
				|| $this->pub->state == 6 
				|| $this->authorized == 3 
			  ) ? 0 : 1;

// Do we have panels enabled?
$show_access   	= ($this->pubconfig->get('show_access', 0)) ? 1 : 0;
$show_metadata 	= ($this->pubconfig->get('show_metadata', 0)) ? 1 : 0;
$show_tags 		= ($this->pubconfig->get('show_tags', 0)) ? 1 : 0;
$show_notes 	= ($this->pubconfig->get('show_notes', 0)) ? 1 : 0;
$show_gallery 	= ($this->pubconfig->get('show_gallery', 0)) ? 1 : 0;
$show_notes 	= ($this->pubconfig->get('show_notes', 0)) ? 1 : 0;
$show_license 	= ($this->pubconfig->get('show_license', 0)) ? 1 : 0;

$jconfig =& JFactory::getConfig();
$sitename = $jconfig->getValue('config.sitename');

// Get access info
if($show_access) {
	switch ($this->pub->access) 
	{
		case 0: default: 	
			$access = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC');
			$access_tip = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC_EXPLANATION'); 		
			break;
		case 1: 			
			$access = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_REGISTERED'); 
			$access_tip = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_REGISTERED_EXPLANATION'); 		
			break;
		case 2: case 3:		
			$access = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED'); 
			$access_tip = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_EXPLANATION'); 		
			break;
	}
}

// Restricted to group?
$groups = '';
if($this->access_groups && ($this->pub->access == 2 || $this->pub->access == 3)) {
	$i = 1;
	foreach($this->access_groups as $gr) {
		$groups .= $gr->description;
		$groups .= $i == count($this->access_groups) ? '' : ', ';
		$i++;
	}
}

$append  = ' <span class="indlist">&raquo; ' . JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $this->row->version_label . ' ';
$append .= ($this->row->state == 1) ? JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_OVERVIEW') : JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW');
$append .= '</span>';
?>	
<form action="<?php echo $this->url; ?>" method="post" id="plg-form">	
	<input type="hidden" name="version" value="<?php echo $this->version; ?>" />				
	<input type="hidden" name="action" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="confirm" value="1" />
	<?php echo $this->project->provisioned == 1 
				? PublicationHelper::showPubTitleProvisioned( $this->pub, $this->route, $append)
				: PublicationHelper::showPubTitle( $this->pub, $this->route, $this->title, $append); ?>

<?php if ($canpublish) { ?>
<p class="mini"><?php echo ($this->row->state == 1) 
	? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_OVERVIEW_SUMMARY_BELOW')) 
	: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_SUMMARY_BELOW')); ?>
	<?php if($this->row->state != 1 && !$this->publication_allowed) {
		echo ' <span class="urgency block">' . JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_MISSING_PARTS').'</span>'; 
	}
	elseif ($this->publication_allowed) 
	{
		if($post)
		{
			echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_NOT_FINAL_POST'); 	
		}
		else 
		{
			echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_NOT_FINAL');
		}
	} ?>
</p>
<?php } ?>

<?php if($this->publication_allowed && $canpublish) { ?>
<div class="review-controls">	
	<div class="next_action">
		<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_WHAT_TO_EXPECT'); ?></h5>
		<ul class="toexpect">
			<?php if($republish) { ?>
			<li><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_APPROVAL_NOT_NEEDED_REPUBLISH'); ?></li>
			<?php } ?>
			<?php if(!$archive && !$republish && !$post) { ?>
			<li><?php 
					echo ($this->pubconfig->get('autoapprove', 0) || $post) 
					? JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_APPROVAL_NOT_NEEDED')
					: JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_APPROVAL_NEEDED'); ?>
			</li>
			<?php } ?>
			<?php if(!$this->row->doi && !$republish && !$post && $doi_txt && $this->pubconfig->get('doi_service')) { ?>
			<li><?php echo $doi_txt; ?></li>
			<?php } ?>
			<li>
				<?php if($show_access) { ?>
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACCESS_IS').' <strong>'.strtolower($access).'</strong> - '.$access_tip; ?>.
				<?php } else { 
					if ($this->task == 'publish' || $republish) 
					{
					 	echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC_NOOPTIONS');	
					}
					else 
					{
						echo $this->project->provisioned == 1
							? JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_NOOPTIONS_PROV')
							: JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_NOOPTIONS');
					}
				 } ?>
			</li>
		</ul>
		<p>
			<input class="option" name="agree" type="radio" value="1" />
			<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_AGREE_TO') . ' <a href="' 
			. $this->pubconfig->get('deposit_terms', 'https://localhost:5000/legal/termsofdeposit?no_html=1'). '" class="popup">' 
			. $sitename . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_TERMS_OF_DEPOSIT') . '</a>.'; ?>
		</p>
		<?php if ($this->task == 'publish') { ?>
		<p class="pubdate">
			<label>	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_PUBLISH_WHEN'); ?>*:
				<input type="text" id="publish_date" name="publish_date" value="<?php echo $this->pubdate; ?>" placeholder="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_IMMEDIATE'); ?>" />
				<span class="hint block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_HINT_EMBARGO'); ?></span>
			</label>
		</p>
		<?php } ?>
	</div>

	<?php if ($this->publication_allowed) { ?>
		<div class="centeralign">
			<span class="review-question"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_LOOKING_GOOD'); ?></span>
			<span>
				<input type="submit" id="submit-review" value="<?php echo $txt; ?>" />
			</span>
			<span class="cancel"><a href="<?php echo $this->url.'/?version='.$this->version; ?>">
			<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_NOT_NOW'); ?></a></span>
		</div>
	<?php } ?>
</div>
<?php }
else if ($this->authorized == 3)
{ ?>
	<p class="review-controls"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_READ_ONLY'); ?></p>
<?php } ?>
</form>

<div id="review-wrap">
	<div class="pub-review-content">
		<?php
		
		if ($this->authorized != 3) {
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
			$view->active = '';
			$view->move = 0;
			$view->hide_version = 1;
			$view->lastpane = $this->lastpane;
			$view->option = $this->option;
			$view->project = $this->project;
			$view->current_idx = $this->current_idx;
			$view->last_idx = $this->last_idx;
			$view->checked = $this->checked;
			$view->url = $this->url;
			$view->review = 1;
			$view->display();	
		}
		
		$description = '';
		if($this->pub->description) {
			$description = $this->parser->parse( stripslashes($this->pub->description), $this->wikiconfig );
		}
		
		// Process metadata
		$metadata = PublicationsHtml::processMetadata($this->pub->metadata, $this->_category, 0, $this->pub->id, $this->option, $this->parser, $this->wikiconfig);
	
		?>
		<div class="two columns first">
		 <div id="pub-card">
			<p class="pub-review-label"><span class="dark"><strong><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$this->pub->version_label; ?></strong> &nbsp; &nbsp; <span class="<?php echo $sClass; ?>"> <?php echo $status; ?></span></span></p>
			<h4><?php echo $this->pub->title; ?></h4>
			<div id="authorslist">
			<?php echo $this->helper->showContributors( $this->authors, true ); ?>
			</div>
			<?php echo $this->pub->abstract ? '<p>'.Hubzero_View_Helper_Html::shortenText(stripslashes($this->pub->abstract), 250, 0).'</p>'  : ''; ?>
		 </div>
			<?php if($show_gallery) { ?>
			<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY')); ?></p>
			<?php
				// Show gallery
				if ($this->shots) {
					$html  = ' <div class="sscontainer">'."\n";					
					$html .= $this->shots;
					$html .= ' </div><!-- / .sscontainer -->'."\n";
					echo $html;
				}
				else {
					echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
				}
			?>
			<?php } ?>
			<p class="pub-review-label"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DESCRIPTION'); ?></p>
			<?php echo $description ? $description  : '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>'; ?>
			<?php 
				if($show_metadata )
				{
					// Show metadata
					echo $metadata['html'] 
					? $metadata['html'] 
					: '<p class="pub-review-label">'.JText::_('PLG_PROJECTS_PUBLICATIONS_METADATA').'</p><p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';	
				}
			?>
			<?php if($show_notes) { ?>
			<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_NOTES')); ?></p>
			<?php
				// Show notes
				if ($this->pub->release_notes) {
					echo $this->parser->parse( $this->pub->release_notes, $this->wikiconfig );
				}
				else {
					echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
				}
			?>
			<?php } ?>
			<?php if($show_tags) { ?>
			<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS')); ?></p>
			<?php
				// Show tags
				if ($this->tags) {
					echo $this->tags;
				}
				else {
					echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
				}
			?>
			<?php } ?>
		</div>
		<div class="two columns second">
			<p class="pub-review-label"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT'); ?> - <span class="dark"><strong><?php echo  JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TYPE_'.strtoupper($this->pub->base)); ?></strong></span></p>
			<?php if (count($this->primary) > 0) { 
				$gitpath = $this->config->get('gitpath', '/opt/local/bin/git');
				$primaryParams = new JParameter($this->primary[0]->params );
				$serveas = $primaryParams->get('serveas', 'download');
			?>
			<div class="three columns first second">
				<ul class="c-list">
				<?php	foreach($this->primary as $att) {
						if ($att->type == 'file') {
							$fpath = $this->version == 'dev' ? $att->path : $att->path;
							$file = str_replace($this->fpath.DS, '', $fpath);
							$ext = explode('.',$file);
							$ext = end($ext);
							$ext = strtolower($ext); 
							$revision = '';
							$revision = $this->projectsHelper->showGitInfo($gitpath, $this->project_path, $att->vcs_hash, $att->path);	
						}					
				?>
						<?php if ($att->type == 'file') { ?>
						<li><img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo $ext; ?>" /><?php echo $file; echo $att->title && $att->title != $fpath ? ' - "<span class="italic">'.$att->title.'</span>"' : ''; ?><?php if($revision) { ?><span class="c-iteminfo"><?php echo $revision; ?></span><?php } ?></li>
						<?php } elseif ($att->type == 'data') { ?>
						<li><span class="databases"><?php echo $att->title; ?>
							[<a href="<?php echo $att->path; ?>" rel="external"><?php echo JText::_('view'); ?></a>]</span>
						</li>
						<?php } 
						elseif ($att->type == 'note') { ?>
						<li><span class="notes"><?php echo $att->title; ?></span>
						</li>
						<?php } ?>
				<?php } ?>
				</ul>	
			</div>
			<div class="three columns third summary">
				<span class="arrow-right"></span>
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_NOTE_'.strtoupper($serveas)); ?>
			</div>
			<div class="clear"></div>
			<?php if($this->version == 'dev') { ?>
				<p class="footnote">*<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_UPDATED_NOTICE'); ?>
				<?php if(!$post) { echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_REPUB_NOTICE')); } ?>
				</p>
			<?php } ?>
			<?php } ?>
			<p class="pub-review-label"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS'); ?></p>
				<?php if(count($this->secondary) > 0) { 
					$gitpath = $this->config->get('gitpath', '/opt/local/bin/git');
				?>
				<ul class="c-list">
				<?php	foreach($this->secondary as $att) {
					if ($att->type == 'file') {
						$fpath = $this->version == 'dev' ? $att->path : $att->path;
						$file = str_replace($this->fpath.DS, '', $fpath);
						$ext = explode('.',$file);
						$ext = end($ext);
						$ext = strtolower($ext); 
						$revision = '';
						$revision = $this->projectsHelper->showGitInfo($gitpath, $this->project_path, $att->vcs_hash, $att->path);	
					}
				?>
						<li><img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo $ext; ?>" /><?php echo $file; echo $att->title && $att->title != $fpath ? ' - "<span class="italic">'.$att->title.'</span>"' : ''; ?><?php if($revision) { ?><span class="c-iteminfo"><?php echo $revision; ?></span><?php } ?></li>	
				<?php } ?>
				</ul>	
				<?php if($this->version == 'dev') { ?>
					<p class="footnote">*<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_UPDATED_NOTICE'); ?></p>
				<?php } ?>
				<?php } else { echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>'; } ?>
				
				<?php if($show_license) { ?>
					<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE')); ?></p>
					<?php echo $this->license 
								? PublicationsHtml::showLicense( $this->pub, $this->version, 'com_publications', $this->license )
								: '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>'; ?>
				<?php } ?>
			
				<?php if($this->pubconfig->get('show_audience')) { ?>
					<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE')); ?></p>
					<?php
						$ra 		= new PublicationAudience( $this->database );
						$audience 	= $ra->getAudience($this->pub->id, $this->pub->version_id , $getlabels = 1, $numlevels = 4);
						echo $audience 
						? PublicationsHtml::showSkillLevel($audience, $numlevels = 4) 
						: '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';				
					?>
				<?php } ?>
				<?php if($show_access) { ?>
					<p class="pub-review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS')); ?></p>
					<p class="mini">
					<?php echo '<span class="dark"><strong>'.$access.'</strong></span> - '.$access_tip; if($groups) { echo ': '.$groups; }?></p>
					<?php if(!$post) { ?><p class="mini"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PAGE').' '; ?><strong>
					<?php echo ($this->pub->access == 3) ? JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PAGE_PRIVATE') : JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PAGE_OPEN');  ?></strong>.
					</p><?php } ?>
				<?php } ?>
		</div>
		<div class="clear"></div>
	</div>
</div>