<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$params = new JParameter( $this->page->params );
$mode = $params->get( 'mode', 'wiki' );

if ($this->sub) {
	$hid = 'sub-content-header';
	$uid = 'section-useroptions';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$uid = 'useroptions';
	$sid = 'sub-menu';
}
?>
<div id="<?php echo $hid; ?>">
	<h2><?php echo $this->title; ?></h2>
<?php
if (!$mode || ($mode && $mode != 'static')) {
	echo WikiHtml::authors( $this->page, $params );
} 
?>
</div><!-- /#content-header -->

<?php if ($mode &&  $mode == 'static' && $this->authorized === 'admin') { ?>
<div id="<?php echo $hid; ?>-extra">
	<ul id="<?php echo $uid; ?>">
		<li><a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=edit'); ?>">Edit</a></li>
		<li class="last"><a class="history" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=history'); ?>">History</a></li>
	</ul>
</div><!-- /#content-header-extra -->
<?php } ?>

<?php if ($this->getError()) { ?>
<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php if ($this->warning) { ?>
<p class="warning"><?php echo $this->warning; ?></p>
<?php } ?>

<?php
if (!$mode || ($mode && $mode != 'static')) {
	echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->editauthorized );
	
	$first = $this->page->getRevision(1);
?>
<div class="main section">
	<div class="aside">
		<div class="article-toc">
			<h3><?php echo JText::_('WIKI_PAGE_TABLE_OF_CONTENTS'); ?></h3>
			<?php echo $this->output['toc']; ?>
		</div>
		<div class="article-tags">
			<h3><?php echo JText::_('WIKI_PAGE_TAGS'); ?></h3>
			<?php echo WikiHtml::tagcloud( $this->tags ); ?>
		</div>
	</div><!-- / .aside -->
	<div class="subject">
		<?php echo $this->output['text']; ?>
		<p class="timestamp"><?php echo JText::_('WIKI_PAGE_CREATED').' '.JHTML::_('date',$first->created, '%d %b %Y').', '.JText::_('WIKI_PAGE_LAST_MODIFIED').' '.JHTML::_('date',$this->revision->created, '%d %b %Y'); ?></p>
	</div><!-- / .subject -->
</div><!-- / .main section -->
<?php
} else { 
	echo $this->output['text'];
}
?>
<div class="clear"></div>
