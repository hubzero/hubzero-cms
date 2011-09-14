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

$xparams = new JParameter( $this->page->params );

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
			<p><?php echo JText::_('WIKI_PAGENAME_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('WIKI_CHANGE_PAGENAME'); ?></h3>
			<label>
				<?php echo JText::_('WIKI_FIELD_PAGENAME'); ?>:
				<input type="text" name="newpagename" value="<?php echo $this->page->pagename; ?>" size="38" />
				<span><?php echo JText::_('WIKI_FIELD_PAGENAME_HINT'); ?></span>
			</label>

			<input type="hidden" name="oldpagename" value="<?php echo $this->page->pagename; ?>" />
			<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
			<input type="hidden" name="pageid" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="saverename" />
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
