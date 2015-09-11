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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$logo = $this->group->getLogo();
?>
<div id="group-owner" class="container">
	<h3><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></h3>
	<div class="group-content">
	<?php if ($logo) { ?>
		<p class="group-img">
			<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>">
				<img src="<?php echo $logo; ?>" width="50" alt="<?php echo Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $this->escape(stripslashes($this->group->get('description')))); ?>" />
			</a>
		</p>
		<p class="group-description group-withlogo"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } else { ?>
		<p class="group-description"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } ?>
	</div>
</div>