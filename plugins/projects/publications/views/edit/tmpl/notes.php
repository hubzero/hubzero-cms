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
		? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_RELEASE_NOTES'))
		: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_RELEASE_NOTES')) ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_NOTES'));
}

$published = $this->pub->versions > 0 ? 1 : 0;

// Are we allowed to edit?
$canedit = (
	$this->pub->state == 3
	|| $this->pub->state == 4
	|| $this->pub->state == 5
	|| in_array($this->active, $this->mayupdate))
	? 1 : 0;

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
	<?php echo $this->project->provisioned == 1
				? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
				: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
		<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
		<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
		<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
		<?php if ($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>
<?php
	// Draw status bar
	$this->contribHelper->drawStatusBar($this);

// Section body starts:
?>
<?php if ($published) { ?>
<div id="pub-body">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
			<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>
			<?php if ($canedit) { ?>
			<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SELECT_NOTES'); ?></p>
			<div id="c-show">
				<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NOTES_SELECT_NO_ITEMS'); ?></p>
			</div>
			<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_NOTES'); ?></p>
			<?php } else { ?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			<?php } ?>
		 </div>
		</div>
		<div class="two columns second" id="c-output">
		 <div class="c-inner">
			<span class="c-submit">
				<?php if ($canedit) { ?>
						<span class="c-submit"><input type="submit" value="<?php if ($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
				<?php } ?>
				<?php if ($this->pub->state != 1 && !$this->move && $this->publication_allowed && $canedit) { echo '<span class="btn-hint"><a href="'.$this->url.'/?section=version">'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT').'</a></span>'; } ?>
			</span>
			<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$this->pub->version_label.' '.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOTES')); ?>: </h5>
			<?php
			$model = new PublicationsModelPublication($this->pub);
			if ($canedit)
			{
				echo \JFactory::getEditor()->display('notes', $this->escape($model->notes('raw')), '', '', 35, 15, false, 'pub_notes', null, null, array('class' => 'minimal no-footer'));
			}
			else
			{
				// Show notes
				if ($notes = $model->notes('parsed'))
				{
					echo $notes;
				}
				else
				{
					echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
				}
			}
			?>
			</div>
		</div>
	</div>
</div>
<?php } else { ?>
<div id="pub-editor" class="pane-desc">
  <div id="c-pane" class="columns">
	 <div class="c-inner">
		<span class="c-submit">
			<?php if ($canedit) { ?>
				<span class="c-submit"><input type="submit" class="btn" value="<?php if ($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
			<?php } ?>
		<?php echo '<span class="btn-hint"><a href="'.$this->url.'/?section=version">'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT').'</a></span>'; ?>
		</span>
		<h4><?php echo $ptitle; ?></h4>
		<?php
		$model = new PublicationsModelPublication($this->pub);
		if ($canedit) { ?>
		<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_NOTES_WRITE_NOTES'); ?></p>
		<table class="tbl-panel">
			<tbody>
				<tr>
					<td>
						<label><?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } else { ?><span class="optional"><?php echo JText::_('OPTIONAL'); ?></span><?php } ?>
							<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$this->pub->version_label.' '.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOTES')); ?>:
							<?php
							echo \JFactory::getEditor()->display('notes', $this->escape($model->notes('raw')), '', '', 35, 20, false, 'pub_notes', null, null, array('class' => 'minimal no-footer'));
							?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_NOTES'); ?></p>
		<?php } else {
				// Show notes
				if ($notes = $model->notes('parsed'))
				{
					echo $notes;
				}
				else
				{
					echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
				}
		 } ?>
	 </div>
  </div>
</div>
<?php } ?>
</form>