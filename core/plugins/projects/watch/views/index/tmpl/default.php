<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div class="watch">
	<?php if ($this->watch) { ?>
		<p>
			<a href="<?php echo Route::url($this->project->link() . '&active=watch&action=manage'); ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_WATCH_MANAGE'); ?></a>
		</p>
	<?php } else { ?>
		<p>
			<a href="<?php echo Route::url($this->project->link() . '&active=watch&action=manage'); ?>" class="showinbox"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE')); ?></a> <span class="new-item"></span>
		</p>
	<?php } ?>
</div>