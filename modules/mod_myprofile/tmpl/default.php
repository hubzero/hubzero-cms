<?php
/**
 * @package		HUBzero CMS
 * @author		Steven Snyder
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
?>
<div id="myprofile-cont">
	<p id="myprofile-image">
<?php if (!$modmyprofile->profile->get('picture')) { ?>
		<img src="/components/com_members/images/profile.gif" alt="<?php echo JText::_('MOD_MYPROFILE_IMAGE'); ?>" />
<?php 
	} else { 
		list($width,$height) = getimagesize(JPATH_ROOT.$modmyprofile->profile->get('picture'));
		$w = ($width && $width > 190) ? 190 : $width;
		$h = $height;
?>
		<img src="<?php echo $modmyprofile->profile->get('picture'); ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>" alt="<?php echo JText::_('MOD_MYPROFILE_IMAGE'); ?>" />
<?php } ?>
	</p>
	<p id="myprofile-name">
		<strong><?php echo $modmyprofile->profile->get('name'); ?> (<?php echo $modmyprofile->profile->get('username'); ?>)</strong>
	</p>
	<div id="myprofile-bio">
<?php if ($modmyprofile->profile->get('bio')) {
	$wikiconfig = array(
		'option'   => 'com_members',
		'scope'    => 'members'.DS.'profile',
		'pagename' => 'member',
		'pageid'   => $modmyprofile->profile->get('uidNumber'),
		'filepath' => '',
		'domain'   => '' 
	);
	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();
	echo $p->parse(stripslashes($modmyprofile->profile->get('bio')), $wikiconfig);
} else { 
	echo JText::_('MOD_MYPROFILE_NO_BIO');
} ?>
	</div>
	<p><?php echo ($modmyprofile->profile->get('public') ? JText::_('MOD_MYPROFILE_PUBLIC') : JText::_('MOD_MYPROFILE_PRIVATE')); ?></p>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$modmyprofile->id.'&task=edit'); ?>"><?php echo JText::_('MOD_MYPROFILE_EDIT_SETTINGS'); ?></a></li>
	</ul>
</div>
