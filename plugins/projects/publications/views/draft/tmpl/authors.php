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

$prov = $this->pub->_project->provisioned == 1 ? 1 : 0;

// Get block properties
$step 	  = $this->step;
$block	  = $this->pub->_curationModel->_progress->blocks->$step;
$complete = $block->status->status;
$name	  = $block->name;

$props = $name . '-' . $this->step;

// Build url
$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=team&action=select' . '&p=' . $props . '&vid=' . $this->pub->version_id
		: JRoute::_( $route . '&active=team&action=select') .'/?p=' . $props . '&pid='
		. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);

// Are we in draft flow?
$move = JRequest::getVar( 'move', '' );
$move = $move ? '&move=continue' : '';

$required 		= $this->manifest->params->required;
$showSubmitter  = $this->manifest->params->submitter;
$showGroupOwner = isset($this->manifest->params->group_owner) ? $this->manifest->params->group_owner : '';

$elName = "authorList";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $step, 0, 'author');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated ? ' el-updated' : '';  ?> ">
<div class="element_editing">
	<div class="pane-wrapper">
		<span class="checker">&nbsp;</span>
		<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><?php } ?>
			<?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_AUTHORS')); ?>
		</label>
		<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
	<div class="list-wrapper">
	<?php if (count($this->pub->_authors) > 0) {
		$i= 1; ?>
			<ul class="itemlist" id="author-list">
			<?php foreach ($this->pub->_authors as $author) {
					$org = $author->organization ? $author->organization : $author->p_organization;
					$name = $author->name ? $author->name : $author->p_name;
					$name = trim($name) ? $name : $author->invited_name;
					$name = trim($name) ? $name : $author->invited_email;

					$active 	= in_array($author->project_owner_id, $this->teamids) ? true : false;
					$confirmed 	= $author->user_id ? true : false;

					$details = $author->credit ? stripslashes($author->credit) : NULL;

					if (!$active)
					{
						$details .= $details ? ' | ' : '';
						$details .= JText::_('PLG_PROJECTS_PUBLICATIONS_MISSING_AUTHOR');
					}
				 ?>
				<li class="reorder pick" id="pick-<?php echo $author->id; ?>">
					<span class="item-options">
						<span>
							<?php if (count($this->pub->_authors) > 1) { ?>
							<span class="hint-reorder"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DRAG_TO_REORDER'); ?></span>
							<?php } ?>
							<a href="<?php echo $editUrl . '/?action=editauthor&aid=' . $author->id . '&p=' . $props; ?>" class="showinbox item-edit" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT'); ?>">&nbsp;</a>
							<a href="<?php echo $editUrl . '/?action=deleteitem&aid=' . $author->id . '&p=' . $props; ?>" class="item-remove" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
						</span>
					</span>
					<span class="item-order"><?php echo $i; ?></span>
					<span class="item-title"><?php echo $name; ?> <span class="item-subtext"><?php echo $org ? ' - ' . $org : ''; ?></span></span>
					<span class="item-details"><?php echo $details; ?></span>
				</li>
		<?php	$i++; } ?>
			</ul>
		<?php  }  ?>
			<div class="item-new">
				<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CHOOSE_AUTHORS'); ?></a></span>
			</div>
		</div>

		<?php if (count($this->pub->_authors) > 1) { ?>
		<p class="hint">*<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_HINT_DRAG'); ?></p>
		<?php } ?>

		<?php
			// Showing submitter?
			if ($showSubmitter && $this->pub->_submitter)
			{ ?>

			<div class="submitter"><p><strong><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTER'); ?>*: </strong>
				<?php echo $this->pub->_submitter->name; ?><?php echo $this->pub->_submitter->organization ? ', ' . $this->pub->_submitter->organization : ''; ?></p>
				<p class="hint">* <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTER_ABOUT'); ?>
				</p>
			</div>
		<?php }
			if (($showGroupOwner && $this->groups) || $this->pub->_project->owned_by_group)
			{
				$group = new \Hubzero\User\Group();
				$used = array();
				if ($this->pub->_project->owned_by_group && \Hubzero\User\Group::exists($this->pub->_project->owned_by_group))
				{
					$group = \Hubzero\User\Group::getInstance( $this->pub->_project->owned_by_group );
				}
				if ($this->pub->group_owner)
				{
					$group = \Hubzero\User\Group::getInstance( $this->pub->group_owner );
					if ($group)
					{
						$this->groups[] = $group;
					}
				}
				?>
			<div class="submitter groupowner"><p><strong><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER'); ?>*: </strong> <?php if ($this->pub->_project->owned_by_group) { echo $group->description . '(' . $group->cn . ')'; } ?></p>
				<?php if (!$this->pub->_project->owned_by_group) { ?>
					<select name="group_owner">
						<option value=""><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_NONE'); ?></option>
						<?php foreach ($this->groups as $g) {
							if (in_array($g->gidNumber, $used))
							{
								continue;
							}
							$used[] = $g->gidNumber;
							?>
							<option value="<?php echo $g->gidNumber; ?>" <?php if ($this->pub->group_owner == $g->gidNumber) { echo 'selected="selected"'; } ?>><?php echo \Hubzero\Utility\String::truncate($g->description, 30) . ' (' . $g->cn . ')'; ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
				<input type="hidden" name="group_owner" value="<?php echo $this->pub->group_owner; ?>" />
				<?php } ?>
				<p class="hint">* <?php echo $this->pub->_project->owned_by_group ? JText::_('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_ABOUT_PROJECT') : JText::_('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_ABOUT'); ?></p>
			</div>
		<?php }
		?>
	</div>
</div>
</div>