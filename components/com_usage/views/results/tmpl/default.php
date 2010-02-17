<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->no_html) { ?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="sub-menu">
	<ul>
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
	<div class="clear"></div>
</div><!-- / #sub-menu -->
<?php } ?>

<?php
$h = 'hide';
$c = 'main';
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section) 
	{
		if ($section != '') {
			$cls  = ($c) ? $c.' ' : '';
			if (key($this->cats[$k]) != $this->task) {
				$cls .= ($h) ? $h.' ' : '';
			}
?>
<div class="<?php echo $cls; ?>section" id="statistics">
	<?php echo $section; ?>
</div><!-- / #statistics.<?php echo $cls; ?>section -->
<?php
		}
		$k++;
	}
}
?>