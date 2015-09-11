<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

// No direct access.
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css();
?>

<header id="content-header">
	<h2><?php echo $this->forgeName; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<h3><?php echo Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT_INTRO', $this->escape($this->forgeName), $this->escape(Config::get('sitename'))); ?>
			</p>
		</div>
		<div class="col span3 omega">
			<h3><?php echo Lang::txt('COM_TOOLS_HELP'); ?></h3>
			<ul>
			<?php if (User::isGuest()) { ?>
				<li><a href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>"><?php echo Lang::txt('COM_TOOLS_SIGN_UP_FREE'); ?></a></li>
			<?php } ?>
				<li><a href="http://subversion.tigris.org/" rel="external"><?php echo Lang::txt('COM_TOOLS_LEARN_SUBVERSION'); ?></a></li>
				<li><a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=tools&page=index'); ?>"><?php echo Lang::txt('COM_TOOLS_NEED_HELP'); ?></a></li>
			</ul>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('COM_TOOLS_AVAILABLE'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<table>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_ALIAS'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$cls = 'even';
				if (count($this->apps) > 0)
				{
					foreach ($this->apps as $project)
					{
						//if ($project->state == 1 || $project->state == 3) {
						if ($project->tool_state != 8)
						{
							$status = ($project->codeaccess == '@OPEN' ? Lang::txt('COM_TOOLS_OPEN_SOURCE') : Lang::txt('COM_TOOLS_CLOSED_SOURCE'));
							?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
						<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo \Hubzero\Utility\String::truncate(stripslashes($project->title), 50); ?></a></td>
						<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo $this->escape($project->toolname); ?></a></td>
						<td><span class="<?php echo $status; ?>-code"><?php echo $status; ?></span></td>
					</tr>
							<?php
						}
					}
				} else {
				?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
						<td colspan="3"><?php echo Lang::txt('COM_TOOLS_NONE'); ?></td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div><!-- / .col span9 omega -->
	</div>
</div><!-- / .section -->