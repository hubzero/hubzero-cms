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
$version_label = $this->row->version_label ? $this->row->version_label : '1.0';

$publicationHelper 	= new PublicationHelper($this->database, $this->row->id, $this->row->publication_id);

$status = $publicationHelper->getPubStateProperty($this->row, 'status');

$move = $this->move ? a.'move='.$this->move : '';
$version = a.'version='.$this->version;
$review = isset($this->review) && $this->review == 1 ?  a.'review=1' : '';

?>
<?php if ($this->row->id && !isset($this->hide_version)) { ?>
	<p id="version-label" <?php if($this->active == 'version') { echo 'class="active"'; } ?>>
		<a href="<?php echo $this->url.'/?action=versions'; ?>" class="versions" id="v-picker"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSIONS'); ?></a> &raquo;
		<a href="<?php echo $this->url.'/?version='.$this->version; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$version_label.' ('.$status.')'; ?></a>
    </p>
<?php } ?>
	<?php if ($this->row->state != 0) { ?>
	<ul id="status-bar" <?php if ($this->move) { echo 'class="moving"'; } ?>>
		<?php if ($review) { ?>
			<li><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_SECTIONS'); ?></li>
		<?php } ?>
		<?php for ($i=0; $i < count($this->panels); $i++) {
			$panel = $this->panels[$i];
			$passed = $this->checked[$panel];
			if ($this->lastpane == 'review')
			{
				$this->current_idx = count($this->panels) + 1;
			}

			if (intval($passed) == 2)
			{
				$class = 'c_incomplete';
			}
			else
			{
				$class = $passed > 0 ? 'c_passed' : 'c_failed';
			}
		?>
		<?php if ($panel == 'content' && $this->active == 'content') {
			// Content submenu (primary / supporting docs)
		?>
			<li id="sub-bar"><?php if($this->step == 'primary') { echo '<span class="active">'.JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY').'</span>'; }
			else { ?><a href="<?php echo $this->url.'/?section=content'.$version.$move.$review; ?>" class="<?php echo $class; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY'); ?></a><?php } ?> <span class="proceedto">&nbsp;</span> <?php if ($this->step == 'primary') { echo $passed ? '<a href="'. $this->url.'/?section=content'.$version.$move.$review.a.'primary=0"  >'.JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS').'</a>' : '<span>'.JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS').'</span>'; } else { echo '<span class="active">'.JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS').'</span>'; } ?></li>
		<?php } ?>

		<?php if($panel == 'description' && $this->active == 'description' && $this->show_substeps == 1) {
			// Description submenu (abstract / metadata)
		?>
			<li id="sub-bar"><?php if($this->step == 'abstract') { echo '<span class="active">'.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TITLE_ABSTRACT').'</span>'; }
			else { ?><a href="<?php echo $this->url.'/?section=description'.$version.$move.$review; ?>" class="<?php echo $class; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TITLE_ABSTRACT'); ?></a><?php } ?> <span class="proceedto">&nbsp;</span> <?php if($this->step == 'abstract') { echo $passed ? '<a href="'. $this->url.'/?section=description'.$version.$move.$review.a.'step=metadata" >'.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_METADATA').'</a>' : '<span>'.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_METADATA').'</span>'; } else { echo '<span class="active">'.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_METADATA').'</span>'; } ?></li>
		<?php } ?>

		<li <?php if ($this->active == $panel) { echo 'class="active"'; } ?>>
		<?php if ($this->move) { ?>
			<?php if($i < $this->current_idx) { ?><a href="<?php echo $this->url.'/?section='.$panel.$version.$move.$review; ?>"  class="<?php echo $class; ?>"><?php } ?><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_'.strtoupper($panel)); ?><?php if($i < $this->current_idx) { ?></a><?php } ?>
		<?php } else { ?>
			<?php if($i != $this->current_idx or $this->active == 'version' or $review) { ?><a href="<?php echo $this->url.'/?section='.$panel.$version.$move.$review; ?>" class="<?php echo $class; ?>"><?php } ?><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_'.strtoupper($panel)); ?><?php if($i != $this->current_idx or $this->active == 'version' or $review) { ?></a><?php } ?>
		<?php } ?>
		</li>
		<?php } ?>
	</ul>
<?php } ?>