<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Steven Snyder
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

