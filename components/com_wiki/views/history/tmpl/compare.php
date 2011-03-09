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

$params =& new JParameter( $this->page->params );

if ($this->sub) {
	$hid = 'sub-content-header';
	$uid = 'section-useroptions';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$uid = 'useroptions';
	$sid = 'sub-menu';
}

$orauthor = JText::_('Unknown');
$oruser =& JUser::getInstance($this->or->created_by);
if (is_object($oruser)) {
	$orauthor = $oruser->get('name');
}

$drauthor = JText::_('Unknown');
$druser =& JUser::getInstance($this->dr->created_by);
if (is_object($druser)) {
	$drauthor = $druser->get('name');
}

?>
<div id="<?php echo $hid; ?>">
	<h2><?php echo $this->title; ?></h2>
	<?php echo WikiHtml::authors( $this->page, $params ); ?>
</div><!-- /#content-header -->

<?php echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->editauthorized ); ?>

<div class="section">
	<div class="aside">
		<dl class="diff-versions">
			<dt><?php echo JText::_('WIKI_VERSION').' '.$this->or->version; ?><dt>
			<dd><?php echo $this->or->created; ?><dd>
			<dd><?php echo $orauthor; ?><dd>
			<dt><?php echo JText::_('WIKI_VERSION').' '.$this->dr->version; ?><dt>
			<dd><?php echo $this->dr->created; ?><dd>
			<dd><?php echo $drauthor; ?><dd>
		</dl>
	</div><!-- / .aside -->
	<div class="subject">
		<p class="diff-deletedline"><del class="diffchange">Deletions</del> or items before changed</p>
		<p class="diff-addedline"><ins class="diffchange">Additions</ins> or items after changed</p>
	</div><!-- / .subject -->
</div><!-- / .section -->
<div class="clear"></div>

<div class="main section">
	<?php echo $this->content; ?>
</div><!-- / .main section -->
<div class="clear"></div>