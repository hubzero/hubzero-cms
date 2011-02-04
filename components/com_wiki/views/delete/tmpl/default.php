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

$xparams =& new JParameter( $this->page->params );

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
	<?php echo WikiHtml::authors( $this->page, $xparams ); ?>
</div><!-- /#content-header -->

<?php
if ($this->page->id) {
	echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $xparams, $this->authorized );
}
?>

<div class="main section">
<?php if ($this->page->state == 1 && $this->authorized !== 'admin') { ?>
	<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php } else { ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('WIKI_DELETE_PAGE_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('WIKI_DELETE_PAGE'); ?></h3>
			<label>
				<input class="option" type="checkbox" name="confirm" value="1" />
				<?php echo JText::_('WIKI_FIELD_CONFIRM_DELETE'); ?> <strong><?php echo JText::_('WIKI_FIELD_CONFIRM_DELETE_HINT'); ?></strong>
			</label>

			<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />
			<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
			<input type="hidden" name="pageid" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="delete" />
<?php if ($this->sub) { ?>
			<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>

		</fieldset>
		<div class="clear"></div>
		<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
	</form>
<?php } ?>
</div><!-- / .main section -->
<div class="clear"></div>