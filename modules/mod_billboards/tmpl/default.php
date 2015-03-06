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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div class="slider">
	<div class="banner" id="<?php echo $this->collection; ?>">
		<?php foreach ($this->slides as $slide) : ?>
			<div class="slide" id="<?php echo $slide->alias; ?>">
				<h3><?php echo $slide->header; ?></h3>
				<?php echo $slide->text; ?>
				<div class="<?php echo $slide->learn_more_location; ?>">
					<a class="<?php echo $slide->learn_more_class; ?>" href="<?php echo $slide->learn_more_target; ?>">
						<?php echo $slide->learn_more_text; ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div <?php echo ($this->pager == 'null') ? '' : 'class="pager"'; ?> id="<?php echo($this->pager); ?>"></div>
</div>