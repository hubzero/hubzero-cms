<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$tabs = array(
	'details' => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_DETAILS'),
	'tokens'  => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_TOKENS'),
	'stats'   => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_STATS')
);
?>

<nav class="sub-menu-cont cf">
	<ul class="sub-menu left">
		<?php foreach ($tabs as $alias => $name) : ?>
			<li class="<?php echo ($this->active == $alias) ? 'active' : ''; ?>">
				<a href="<?php echo Route::url($this->application->link() . '&active=' . $alias); ?>">
					<span><?php echo $name; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<ul class="sub-menu right">
		<li>
			<a class="icon-settings" href="<?php echo Route::url($this->application->link('edit')); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_SETTINGS'); ?>
			</a>
		</li>
	</ul>
</nav>