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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->publish)
{
	$this->css();
	?>
	<div id="<?php echo $this->moduleid; ?>" class="modnotices <?php echo $this->alertlevel; ?>">
		<p>
			<?php echo stripslashes($this->message); ?>
			<?php
			$page = Request::getVar('REQUEST_URI', '', 'server');
			if ($page && $this->params->get('allowClose', 1))
			{
				$this->js();

				$page .= (strstr($page, '?')) ? '&' : '?';
				$page .= $this->moduleid . '=close';
				?>
				<a class="close" href="<?php echo $page; ?>" data-duration="<?php echo $this->days_left; ?>" title="<?php echo Lang::txt('MOD_NOTICES_CLOSE_TITLE'); ?>">
					<span><?php echo Lang::txt('MOD_NOTICES_CLOSE'); ?></span>
				</a>
				<?php
			}
			?>
		</p>
	</div>
	<?php
}