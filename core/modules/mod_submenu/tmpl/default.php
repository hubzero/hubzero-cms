<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

defined('_HZEXEC_') or die();

$hide = Request::getInt('hidemainmenu');
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
						?><a class="active" href="<?php echo \Hubzero\Utility\String::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
					else:
						?><a href="<?php echo \Hubzero\Utility\String::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
					endif;
				else:
					?><?php echo $item[0]; ?><?php
				endif;
			endif;
			?>
		</li>
	<?php endforeach; ?>
</ul>
<?php
if (App::has('subsubmenu'))
{
	$list = App::get('subsubmenu')->getItems();
}
else
{
	$list = array();
}

if (is_array($list) && count($list))
{
	?>
	<nav role="navigation" class="sub sub-navigation">
		<ul>
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
								?><a class="active" href="<?php echo \Hubzero\Utility\String::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
							else:
								?><a href="<?php echo \Hubzero\Utility\String::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
							endif;
						else:
							?><?php echo $item[0]; ?><?php
						endif;
					endif;
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}
