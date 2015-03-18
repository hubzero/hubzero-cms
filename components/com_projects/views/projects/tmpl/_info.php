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

$project = new \Components\Projects\Models\Project($this->info->project);

$this->info->project->about = $project->about('parsed');
$privacy = $this->info->project->private ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

?>
<div id="plg-header">
	<h3 class="inform"><?php echo Lang::txt('COM_PROJECTS_PROJECT_INFO'); ?></h3>
</div>
<?php if ($this->info->project->role == 1 ) { ?>
<p class="editing"><a href="<?php echo Route::url('index.php?option=' . $this->info->option . '&task=edit&alias=' . $this->info->project->alias . '&active=info'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT'); ?></a></p>
<?php } ?>

<div id="basic_info">
	<table id="infotbl">
		<tbody>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></td>
				<td><?php echo $this->info->project->title; ?></td>
				<?php if ($this->info->config->get('grantinfo', 0) && $this->info->params->get( 'grant_title')) { ?>
					<td rowspan="5" class="grantinfo">
						<h4><?php echo Lang::txt('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
						<p>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:</span> <?php echo $this->info->params->get( 'grant_title'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:</span> <?php echo $this->info->params->get( 'grant_PI', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:</span> <?php echo $this->info->params->get( 'grant_agency', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:</span> <?php echo $this->info->params->get( 'grant_budget', 'N/A'); ?></span>
							<?php if ($this->info->project->role == 1) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->info->option . '&task=edit&alias=' . $this->info->project->alias . '&active=settings'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_THIS'); ?></a>
							<?php } ?>
						</p>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?></td>
				<td><?php echo $this->info->project->alias; ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_TYPE'); ?></td>
				<td><?php echo $this->info->project->projecttype; ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></td>
				<td><?php echo $privacy; ?> <?php if (!$this->info->project->private) { ?><span class="mini faded">[<a href="<?php echo Route::url('index.php?option=' . $this->info->option . '&alias=' . $this->info->project->alias . '&preview=1'); ?>"><?php echo Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a>]</span><?php } ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?></td>
				<td><?php echo JHTML::_('date', $this->info->project->created, 'M d, Y'); ?></td>
			</tr>
			<?php if ($this->info->project->about) { ?>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></td>
				<td><?php echo $this->info->project->about; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div><!-- / .basic info -->
