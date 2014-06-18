<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
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

//section links
$sections = array(
	array(
		'name' => 'inbox',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_INBOX'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'sent',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_SENT'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'archive',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_ARCHIVE'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'trash',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_TRASH'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'new',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_COMPOSE'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=new&limit=' . $this->filters['limit'] . '&limitstart=0')
	)
);

//option links
$options = array(
	array(
		'name' => 'settings',
		'title' => JText::_('PLG_MEMBERS_MESSAGES_SETTINGS'),
		'link' => JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings')
	)
);

//no html?
$no_html = JRequest::getVar("no_html", 0);
?>
<?php if (!$no_html) : ?>
<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_MESSAGES'); ?>
</h3>

<div class="section">
	<ul id="message-toolbar">
		<?php foreach($sections as $s) : ?>
			<?php $sel = ($this->task == $s['name']) ? "active": ""; ?>
			<li><a class="<?php echo $s['name'] . " " . $sel; ?>" title="<?php echo $s['title']; ?>" href="<?php echo $s['link']; ?>"><?php echo $s['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<ul id="message-options">
		<?php foreach($options as $o) : ?>
			<?php $sel = ($this->task == $o['name']) ? "active": ""; ?>
			<li><a class="<?php echo $o['name'] . " " . $sel; ?>" title="<?php echo $o['title']; ?>" href="<?php echo $o['link']; ?>"><?php echo $o['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<br class="clear" />

	<div id="messages-container">
		<?php
			foreach($this->notifications as $n) {
				echo "<p class=\"{$n['type']}\">{$n['message']}</p>";
			}
		?>
<?php endif; ?>

		<?php echo $this->body; ?>

<?php if(!$no_html) : ?>
	</div>
</div>
<?php endif; ?>