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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->no_html) {
	$this->css()
	     ->js();
	?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<nav>
	<ul class="sub-menu">
		<?php
		if ($this->cats) {
			$i = 1;
			$cs = array();
			foreach ($this->cats as $cat)
			{
				$name = key($cat);
				if ($cat[$name] != '') {
		?>
				<li id="sm-<?php echo $i; ?>"<?php if (strtolower($name) == $this->task) { echo ' class="active"'; } ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
		<?php
					$i++;
					$cs[] = $name;
				}
			}
		}
		?>
	</ul>
</nav>

<?php } ?>

<?php
$h = 'hide';
$c = 'main';
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section)
	{
		if ($section != '')
		{
			$cls  = ($c) ? $c.' ' : '';
			if (key($this->cats[$k]) != $this->task)
			{
				$cls .= ($h) ? $h.' ' : '';
			}
			?>
			<section class="<?php echo $cls; ?>section" id="statistics">
				<?php echo $section; ?>
			</section><!-- / #statistics.<?php echo $cls; ?>section -->
			<?php
		}
		$k++;
	}
}
?>