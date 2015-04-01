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

// Determine pane title
if ($this->version == 'dev')
{
	$ptitle = $this->last_idx > $this->current_idx
			? ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT_AUTHORS'))
			: ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECT_AUTHORS')) ;
}
else
{
	$ptitle = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PANEL_AUTHORS'));
}
$ptitle   = $this->project->provisioned == 1 ? ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADD_AUTHORS'))  : $ptitle;
$instruct = $this->project->provisioned == 1
			? Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ADD_AUTHORS')
			: Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_SELECT_AUTHORS');

// Get image path
$mconfig = Component::params( 'com_members' );
$path  = DS . trim($mconfig->get('webpath'), DS);

// Keep count of authors deleted from team / unconfirmed
$missing 	 = 0;
$unconfirmed = 0;

// Are we allowed to edit?
$canedit = ($this->pub->state == 1 || $this->pub->state == 0 || $this->pub->state == 6 ) ? 0 : 1;

?>
	<?php echo $this->project->provisioned == 1
				? \Components\Publications\Helpers\Html::showPubTitleProvisioned( $this->pub, $this->route, $this->title)
				: \Components\Publications\Helpers\Html::showPubTitle( $this->pub, $this->route); ?>

<?php
	// Draw status bar
	\Components\Publications\Helpers\Html::drawStatusBar($this);

// Section body starts:
?>
<div id="pub-body">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
			<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo Lang::txt('REQUIRED'); ?></span><?php } ?></h4>
			<?php if ($canedit) { ?>
			<p><?php echo $instruct; ?></p>
			<!-- Load content selection browser //-->
			<div id="c-show" class="c-panel-authors">
				<noscript>
					<p class="nojs"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_JS_MESSAGE'); ?></p>
				</noscript>
			</div>
			<div id="pick-authors" class="addnew">
			<form action="<?php echo Route::url($this->route . '&active=publications'); ?>" method="post" id="addmember-form">
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
					<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
					<input type="hidden" name="versionid" value="<?php echo $this->row->id; ?>" />
					<input type="hidden" name="active" value="team" />
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="view" value="browser" />
					<input type="hidden" name="authors" value="1" />
					<input type="hidden" name="role" value="2" />
					<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
					<input type="hidden" name="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
					<label>
					<?php
						JPluginHelper::importPlugin( 'hubzero');
						$dispatcher = JDispatcher::getInstance();

						$mc = $dispatcher->trigger( 'onGetSingleEntry', array(array('members', 'newmember', 'newmember', '', '')) );

						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
							<input type="text" name="newmember" id="newmember" value="" size="35" />
						<?php } ?>
					</label>
					<?php if ($this->project->provisioned == 1 ) { ?>
					<input type="hidden" name="task" value="submit" />
					<?php } ?>
					<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_ADD'); ?>" class="btn yesbtn" id="add-author" />
			</form>
			</div>
			<!-- END content selection browser //-->
			<?php if (!$this->project->provisioned) { ?>
			<p class="pub-info"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_AUTHORS'); ?></p><?php } ?>
			<?php } else { ?>
				<p class="notice"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			<?php } ?>
			<?php if ($this->project->provisioned == 1 ) { ?>
				<p class="notice"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_AUTHORS_PROV_WARNING').' <a href="'
				. Route::url('index.php?option=com_publications&task=submit&pid='
				. $this->pub->id) . '?active=team&amp;action=editauthors&amp;version='. $this->pub->version_number . '" class="showinbox">'
				. Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_AUTHORS_EDIT_TEAM') . '</a>.'; ?></p>
			<?php } ?>
		 </div>
		</div>
		<div class="two columns second" id="c-output">
			<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
				<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
				<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
				<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="selections" id="selections" value="" />
				<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if ($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			 <div class="c-inner">
					<?php if ($canedit) { ?>
					<span class="c-submit"><input type="submit" class="btn" value="<?php if ($this->move) { echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" <?php if (count($this->authors) == 0) { echo 'class="disabled"'; } ?> id="c-continue" /></span>
					<?php } ?>

				<h5><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_AUTHORS')); ?>: </h5>
				<ul id="c-authors" class="c-list <?php if (!$canedit) { ?>noedit<?php } ?>">
					<li id="nosel" <?php if (count($this->authors) > 0) { echo 'class="hidden"'; } ?> ><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_NONE_SELECTED'); ?></li>
					<?php
					// If we have authors selected
					if (count($this->authors) > 0) {
						$o = 1;
						foreach ($this->authors as $author) {
							$org = $author->organization ? $author->organization : $author->p_organization;
							$name = $author->name ? $author->name : $author->p_name;
							$name = trim($name) ? $name : $author->invited_name;
							$name = trim($name) ? $name : $author->invited_email;

							$active = in_array($author->project_owner_id, $this->teamids) ? 1 : 0;
							if ($active == 0) {
								$missing++;
							}
							else if (!$author->user_id) {
								$unconfirmed++;
							}
							?>
						<li id="clone-author::<?php echo $author->project_owner_id; ?>" class="c-drag <?php if ($active == 0) { echo 'i-missing'; } ?> clone-<?php echo $author->project_owner_id; ?>" >
							<span class="a-ordernum"><?php echo $o; ?></span>
							<?php if ($canedit) { ?>
							<span class="c-edit"><a href="<?php echo $this->url . '?vid=' . $this->row->id . '&amp;uid=' . $author->user_id . '&amp;move=' . $this->move . '&amp;action=editauthor&amp;owner=' . $author->project_owner_id . '&amp;version=' . $this->version; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT'); ?></a></span>
							<?php } ?>
							<span class="a-wrap">
								<span class="a-authorname"><?php echo stripslashes($name); ?></span><span class="a-org"><?php echo $org ? ', '.stripslashes($org) : ''; ?></span>
								<span class="a-credit"><?php echo stripslashes($author->credit); ?></span>
							</span>
						</li>
					<?php $o++; } }  ?>
				</ul>
				<?php
					// Showing submitter?
					if ($this->typeParams->get('show_submitter') && $this->submitter)
					{ ?>

					<p class="submitter"><strong><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTER'); ?>*: </strong>
						<?php echo $this->submitter->name; ?><?php echo $this->submitter->organization ? ', ' . $this->submitter->organization : ''; ?>
						<span class="block hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTER_ABOUT'); ?></span>
					</p>
				<?php }
				?>
				<?php if ($canedit) { ?>
				<p id="c-instruct"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_HINT_DRAG'); ?></p>
				<?php } ?>
				<?php if ($missing > 0) { ?>
					<p class="pub-info"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_AUTHORS_MISSING'); ?></p>
				<?php } else if ($unconfirmed > 0) { ?>
					<p class="pub-info"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_AUTHORS_UNCONFIRMED'); ?></p>
				<?php } ?>
			 </div>
			</form>
		</div>
	</div>
</div>
