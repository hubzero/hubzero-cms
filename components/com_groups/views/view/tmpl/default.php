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

$no_html = JRequest::getInt( 'no_html', 0 );

if (!$no_html) { ?>
	<div id="content-header">
		<h2><?php echo $this->group->get('description'); ?></h2>
	</div><!-- / #content-header -->
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>"><?php echo JText::_('GROUPS_ALL_GROUPS'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
	<div id="sub-menu">
		<ul>
<?php
	$i = 1;
	foreach ($this->cats as $cat)
	{
		$name = key($cat);
		if ($name != '') {
?>
			<li id="sm-<?php echo $i; ?>"<?php if (strtolower($name) == $this->tab) { echo ' class="active"'; } ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
<?php
			$i++;
		}
	}
?>
		</ul>
		<div class="clear"></div>
	</div><!-- / #sub-menu -->
<?php 
}

$html = '';
$h = 'hide';
$c = 'main';
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section) 
	{
		if ($section['html'] != '') {
			$cls  = ($c) ? $c.' ' : '';
			if (key($this->cats[$k]) != $this->tab) {
				$cls .= ($h) ? $h.' ' : '';
			}
			$html .= '<div class="'.$cls.'section'.'" id="'.key($this->cats[$k]).'-section'.'">'.$section['html'].'</div>';
		}
		$k++;
	}
}

echo $html;
?>