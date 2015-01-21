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

// No direct access.
defined('_JEXEC') or die;

$hide = JRequest::getInt('hidemainmenu');
?>
<ul id="submenu">
	<?php foreach ($list as $item): ?>
		<li>
			<?php
			if ($hide):
				if (isset ($item[2]) && $item[2] == 1):
					?><span class="nolink active"><?php echo $item[0]; ?></span><?php
				else:
					?><span class="nolink"><?php echo $item[0]; ?></span><?php
				endif;
			else:
				if (strlen($item[1])):
					if (isset ($item[2]) && $item[2] == 1):
						?><a class="active" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
					else:
						?><a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
					endif;
				else:
					?><?php echo $item[0]; ?><?php
				endif;
			endif;
			?>
		</li>
	<?php endforeach; ?>
</ul>
