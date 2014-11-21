<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
// no direct access
defined('_JEXEC') or die('Restricted access');

$this->css('introduction.css', 'system')
     ->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_PUBLICATIONS_BROWSE') . ' ' . JText::_('COM_PUBLICATIONS_PUBLICATIONS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>
<?php if ($this->getError()) { ?>
<div class="status-msg">
<?php
	// Display error or success message
	if ($this->getError()) {
		echo ('<p class="witherror">' . $this->getError().'</p>');
	}
?>
</div>
<?php } ?>
<div class="clear block">&nbsp;</div>
<section class="section intropage">
	<div class="grid">
		<div class="col <?php echo ($this->contributable) ? 'span4' : 'span6';  ?>">
			<h3><?php echo JText::_('Recent Publications'); ?></h3>
			<?php if ($this->results && count($this->results) > 0) {
				// Display List of items
				$this->view('_list')
				     ->set('results', $this->results)
				     ->set('config', $this->config)
				     ->set('helper', new PublicationHelper($this->database))
				     ->display();
				} else {
				echo ('<p class="noresults">'.JText::_('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</p>');
			} ?>
		</div>
		<div class="col <?php echo ($this->contributable) ? 'span4' : 'span6 omega';  ?>">
			<h3><?php echo JText::_('COM_PUBLICATIONS_PUPULAR'); ?></h3>
			<?php if ($this->best && count($this->best) > 0)
			{ 		// Display List of items
					$this->view('_list')
					     ->set('results', $this->best)
					     ->set('config', $this->config)
					     ->set('helper', new PublicationHelper($this->database))
					     ->display();
			} else {
				echo ('<p class="noresults">'.JText::_('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</p>');
			} ?>
		</div>
		<?php if ($this->contributable) { ?>
		<div class="col span4 omega">
			<h3><?php echo JText::_('COM_PUBLICATIONS_WHO_CAN_SUBMIT'); ?></h3>
			<p><?php echo JText::_('COM_PUBLICATIONS_WHO_CAN_SUBMIT_ANYONE'); ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option=com_publications&task=submit'); ?>" class="btn"><?php echo JText::_('COM_PUBLICATIONS_START_PUBLISHING'); ?> &raquo;</a></p>
		</div>
		<?php } ?>
	</div>
</section><!-- / .section -->
