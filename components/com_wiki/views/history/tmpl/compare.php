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

$params = new JParameter( $this->page->params );

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
