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

$juser =& JFactory::getUser();
$no_html = JRequest::getInt( 'no_html', 0 );
?>
<div class="vcard">
	<div id="content-header">
		<h2><span class="fn"><?php echo stripslashes($this->profile->get('name')); ?></span></h2>
	</div>
<?php if (!$no_html) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
<?php if ($this->authorized) { ?>
			<li<?php if ($juser->get('guest') || ($this->profile->get('uidNumber') == $juser->get('id'))) { echo ' class="last"'; } ?>><a class="edit-member" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edit&id='. $this->profile->get('uidNumber')); ?>"><?php echo JText::_('Edit profile'); ?></a></li>
<?php 
	} 
	if (!$juser->get('guest') && ($this->profile->get('uidNumber') != $juser->get('id'))) {
?>
			<li class="last"><a class="message" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='. $juser->get('id').'&active=messages&task=new&to='.$this->profile->get('uidNumber')); ?>"><?php echo JText::_('Send Message'); ?></a></li>
<?php } ?>
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
			if (strtolower($name) == $this->tab) {
				$app =& JFactory::getApplication();
				$pathway =& $app->getPathway();
				$pathway->addItem($cat[$name],'index.php?option='.$this->option.'&id='.$this->profile->get('uidNumber').'&active='.$name);
			}
?>
			<li id="sm-<?php echo $i; ?>"<?php echo (strtolower($name) == $this->tab) ? ' class="active"' : ''; ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->profile->get('uidNumber').'&active='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
<?php
			$i++;	
		}
	}
?>
		</ul>
		<div class="clear"></div>
	</div><!-- / #sub-menu -->
<?php } ?>
<?php
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section) 
	{
		if ($section['html'] != '') {
			$cls = ('main') ? 'main ' : '';
			if (key($this->cats[$k]) != $this->tab) {
				$cls .= ('hide') ? 'hide ' : '';
			}
?>
	<div class="<?php echo $cls; ?>section" id="<?php echo key($this->cats[$k]); ?>-section">
		<?php echo $section['html']; ?>
	</div><!-- / #<?php echo key($this->cats[$k]); ?>-section.<?php echo $cls; ?>section -->
<?php
		}
		$k++;
	}
}
?>
</div><!-- / .vcard -->