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

// No direct access
defined('_HZEXEC_') or die();

$privacy = !$this->model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

$config = $this->model->config();

?>
<div id="plg-header">
	<h3 class="inform"><?php echo Lang::txt('COM_PROJECTS_PROJECT_INFO'); ?></h3>
</div>
<?php if ($this->model->access('manager')) { ?>
<p class="editing"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=info'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT'); ?></a></p>
<?php } ?>

<div id="basic_info">
	<table id="infotbl">
		<tbody>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></td>
				<td><?php echo $this->escape($this->model->get('title')); ?></td>
				<?php if ($config->get('grantinfo', 0) && $this->model->params->get( 'grant_title')) { ?>
					<td rowspan="5" class="grantinfo">
						<h4><?php echo Lang::txt('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
						<p>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:</span> <?php echo $this->model->params->get( 'grant_title'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:</span> <?php echo $this->model->params->get( 'grant_PI', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:</span> <?php echo $this->model->params->get( 'grant_agency', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:</span> <?php echo $this->model->params->get( 'grant_budget', 'N/A'); ?></span>
							<?php if ($this->model->access('manager') ) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=settings'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_THIS'); ?></a>
							<?php } ?>
						</p>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?></td>
				<td><?php echo $this->model->get('alias'); ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></td>
				<td><?php echo $privacy; ?> <?php if ($this->model->isPublic()) { ?><span class="mini faded">[<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1'); ?>"><?php echo Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a>]</span><?php } ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?></td>
				<td><?php echo $this->model->created('date'); ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></td>
				<td><?php echo $this->model->groupOwner() ? $this->model->groupOwner('description') : $this->model->owner('name'); ?></td>
			</tr>
			<?php if ($this->model->about('parsed')) { ?>
			<tr>
				<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></td>
				<td><?php echo $this->model->about('parsed'); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div><!-- / .basic info -->
