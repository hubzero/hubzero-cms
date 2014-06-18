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

$route = $this->project->provisioned == 1
		? 'index.php?option=com_publications&task=submit&pid=' . $this->publication->id
		: 'index.php?option=com_projects&alias=' . $this->project->alias;

// Save Selection URL
$url = $this->project->provisioned ? JRoute::_( $route) : JRoute::_( 'index.php?option=com_projects&alias='
	. $this->project->alias . '&active=publications&pid=' . $this->publication->id);

$i = 0;

$block   = $this->block;
$step  	 = $this->step;

// Get requirements
$blocks   = $this->publication->_curationModel->_progress->blocks;
$params   = $blocks->$step->manifest->params;

$selected = array();

if (count($this->authors) > 0)
{
	foreach ($this->authors as $sel)
	{
		$selected[] = $sel->project_owner_id;
	}
}

?>
<script src="/plugins/projects/team/js/selector.js"></script>
<div id="abox-content">
<h3><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_ADD_NEW'); ?> </h3>
		<form id="add-author" class="add-author" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias); ?>">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
				<input type="hidden" name="alias" value="<?php echo $this->project->alias; ?>" />
				<input type="hidden" name="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="additem" />
				<?php if ($this->project->provisioned == 1) { ?>
					<input type="hidden" name="task" value="submit" />
					<input type="hidden" name="ajax" value="0" />
				<?php }  ?>
			</fieldset>
			<p class="requirement"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_ADD_NEW'); ?></p>
			<div id="quick-add" class="quick-add">
				<?php if ($this->project->provisioned) { ?>
					<div class="autoc">
						<label>
							<span class="formlabel"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_LOOK_UP_BY_ID')); ?>:</span>
							<?php
								if (count($this->mc) > 0) {
									echo $this->mc[0];
								?>
					<script>
						if($('.autocomplete').length)
						{
							$('.autocomplete').each(function(i, input) {

								id = $(input).attr('id');
								if (id != 'uid')
								{
									return false;
								}

								$(input).on('change', function(e)
								{
									var uid = $(input).val();

									if (uid)
									{
										var name = $('.token-input-token-acm p')[0];

										if ($(name).length)
										{
											name = $(name).html();

											var parts = name.split(" ");

											// Complete name
											if (parts.length > 1 && !$('#firstName').val() && !$('#lastName').val())
											{
												$('#lastName').val(parts[parts.length - 1]);
												parts.pop();
												var first = parts.join(" ");
												$('#firstName').val(first);
											}

										}
									}
								});
							});
						}

					</script>
					<?php			} else { ?>
									<input type="text" name="uid" id="uid" value="" size="35" />
								<?php } ?>
						</label>
					</div>
				<?php } ?>
				<div class="block">
					<div class="inlineblock">
					<label>
						<span class="formlabel"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_FIRST_NAME')); ?>*:</span>
						<input type="text" name="firstName" id="firstName" class="inputrequired" value="" maxlength="255" />
					</label>
					</div>
					<div class="inlineblock">
					<label>
						<span class="formlabel"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_LAST_NAME')); ?>*:</span>
						<input type="text" name="lastName" id="lastName" class="inputrequired" value="" maxlength="255" />
					</label>
					</div>
				</div>
				<div class="block">
					<div class="block-liner">
					<label for="organization">
						<span class="formlabel"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_ORGANIZATION')); ?>*:</span>
						<input type="text" class="inputrequired" name="organization" value="" maxlength="255" />
					<p class="hint"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_HINT'); ?></p>
					</label>
					</div>
				</div>
			<?php if (!$this->project->provisioned) { ?>
				<div class="block">
					<p class="invite-question"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_INVITE_TO_TEAM')); ?></p>
					<div class="block-liner">
					<label for="email">
							<span class="formlabel"><?php echo ucfirst(JText::_('PLG_PROJECTS_TEAM_SELECTOR_EMAIL')); ?>:</span>
							<input type="text"  name="email" value="" maxlength="255" />
					</label>
					</div>
				</div>
				<?php } ?>

				<div class="submitarea">
					<div id="status-box"></div>
					<a class="btn btn-success active" id="b-add"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_SAVE_NEW'); ?></a>
				</div>
			</div>
		</form>
</div>