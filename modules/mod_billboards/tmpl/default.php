<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div class="slider">
	<div class="banner" id="<?php echo $modbillboards->collection; ?>">
		<?php foreach ($modbillboards->slides as $slide) {
			if ($slide->learn_more_location == 'relative')
			{
				$tag = '<p class="relative">';
			}
			else
			{
				$tag = '<div class="' . $slide->learn_more_location . '">';
			}
			$closingtag = ($slide->learn_more_location == 'relative') ? '</p>' : '</div>';	?>

			<div class="slide" id="<?php echo $slide->alias; ?>">
				<h3><?php echo $slide->header; ?></h3>
					<p><?php echo $slide->text; ?></p>
						<?php echo $tag; ?>
							<a class="<?php echo $slide->learn_more_class; ?>" href="<?php echo $slide->learn_more_target; ?>">
								<?php echo $slide->learn_more_text; ?>
							</a>
						<?php echo $closingtag; ?>
			</div>
		<?php } ?>
	</div>
	<!-- @TODO: let's make this whole line an if statement -->
	<div <?php echo ($modbillboards->pager == 'null') ? '' : 'class="pager"'; ?> id="<?php echo($modbillboards->pager); ?>"></div>
</div>