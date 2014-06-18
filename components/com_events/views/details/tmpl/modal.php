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
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();
?>
<div id="event" class="modal">
<?php if ($this->row) { ?>
	<h2 class="entry-title">
		<?php echo $this->escape(stripslashes($this->row->title)); ?>
<?php if ($this->authorized || $this->row->created_by == $juser->get('id')) { ?>
		<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $this->row->id); ?>" title="<?php echo JText::_('EVENTS_EDIT'); ?>">
			<?php echo strtolower(JText::_('EVENTS_EDIT')); ?>
		</a>
		<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&id=' . $this->row->id); ?>" title="<?php echo JText::_('EVENTS_DELETE'); ?>">
			<?php echo strtolower(JText::_('EVENTS_DELETE')); ?>
		</a>
<?php } ?>
	</h2>

<?php if ($this->pages || ($this->row->registerby && $this->row->registerby != '0000-00-00 00:00:00')) { ?>
	<div id="sub-sub-menu">
		<ul>
			<li<?php if ($this->page->alias == '') { echo ' class="active"'; } ?>>
				<a class="tab" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1'); ?>">
					<span><?php echo JText::_('EVENTS_OVERVIEW'); ?></span>
				</a>
			</li>
		<?php
		if ($this->pages) {
			foreach ($this->pages as $p)
			{
		?>
			<li<?php if ($this->page->alias == $p->alias) { echo ' class="active"'; } ?>>
				<a class="tab" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1&page=' . $p->alias); ?>">
					<span><?php echo trim(stripslashes($p->title)); ?></span>
				</a>
			</li>
		<?php
			}
		}
		?>
<?php if ($this->row->registerby && $this->row->registerby != '0000-00-00 00:00:00') { ?>
			<li<?php if ($this->page->alias == 'register') { echo ' class="active"'; } ?>>
				<a class="tab" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1&page=register'); ?>">
					<span><?php echo JText::_('EVENTS_REGISTER'); ?></span>
				</a>
			</li>
<?php } ?>
		</ul>
		<div class="clear"></div>
	</div>
<?php } ?>

	<div class="entry-details">
<?php if ($this->page->alias != '') {
		echo (trim($this->page->pagetext)) ? stripslashes($this->page->pagetext) : '<p class="warning">' . JText::_('EVENTS_NO_INFO_AVAILABLE') . '</p>';
	} else {
?>

	<div class="five columns first second third">
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?></h3>
			<p class="entry-description">
				<?php echo stripslashes($this->row->content); ?>
			</p>
<?php
	if ($this->fields) {
		foreach ($this->fields as $field)
		{
			if (end($field) != NULL) {
				if (end($field) == '1') {
?>
			<h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
			<p><?php echo JText::_('YES'); ?></p>
<?php 			} else { ?>
			<h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
			<p><?php echo end($field); ?></p>
<?php
				}
			}
		}
	}
?>
		</div>
<?php if ($this->tags) { ?>
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TAGS'); ?></h3>
			<?php echo $this->tags; ?>
		</div>
<?php } ?>
	</div>
	<div class="five columns fourth fifth">
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></h3>
			<p class="entry-category">
				<?php echo $this->escape(stripslashes($this->categories[$this->row->catid])); ?>
			</p>
		</div>

		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_WHEN'); ?></h3>
			<p class="entry-datetime">
<?php
		$ts = explode(':', $this->row->start_time);
		//$ts[0] = intval($ts[0]);
		if (intval($ts[0]) > 12) {
			$ts[0] = ($ts[0] - 12);
			$ts[0] = (substr($ts[0], 0, 1) == '0') ? substr($ts[0], 1) : $ts[0];
			$this->row->start_time = implode(':',$ts);
			$this->row->start_time .= ' <abbr title="Post Meridian">am</abbr>';
		} else {
			$this->row->start_time = (substr($this->row->start_time, 0, 1) == '0') ? substr($this->row->start_time, 1) : $this->row->start_time;
			$this->row->start_time .= (intval($ts[0]) == 12) ? ' <small>'.JText::_('EVENTS_NOON').'</small>' : ' <abbr title="Ante Meridian">am</abbr>';
		}
		$te = explode(':', $this->row->stop_time);
		//$te[0] = intval($te[0]);
		if (intval($te[0]) > 12) {
			$te[0] = ($te[0] - 12);
			$te[0] = (substr($te[0], 0, 1) == '0') ? substr($te[0], 1) : $te[0];
			$this->row->stop_time = implode(':', $te);
			$this->row->stop_time .= ' <abbr title="Post Meridian">pm</abbr>';
		} else {
			$this->row->stop_time = (substr($this->row->stop_time, 0, 1) == '0') ? substr($this->row->stop_time, 1) : $this->row->stop_time;
			$this->row->stop_time .= (intval($te[0]) == 12) ? ' <small>'.JText::_('EVENTS_NOON').'</small>' : ' <abbr title="Ante Meridian">pm</abbr>';
		}
		if ($this->row->start_date == $this->row->stop_date) {
			echo $this->row->start_date .',<br />'.$this->row->start_time.'&nbsp;-&nbsp;'.$this->row->stop_time.'<br />';
		} else {
			echo JText::_('EVENTS_CAL_LANG_FROM').' '.$this->row->start_date.'&nbsp;-&nbsp;'.$this->row->start_time.'<br />'.
				JText::_('EVENTS_CAL_LANG_TO').' '.$this->row->stop_date.'&nbsp;-&nbsp;'.$this->row->stop_time.'<br />';
		}
?>
			</p>
		</div>

<?php if (trim($this->row->adresse_info)) { ?>
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?></h3>
			<p class="entry-location">
				<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>
			</p>
		</div>
<?php } ?>

<?php if (trim($this->row->extra_info)) { ?>
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_EXTRA'); ?></h3>
			<p class="entry-link">
				<a href="<?php echo stripslashes($this->row->extra_info); ?>"><?php echo $this->escape(stripslashes($this->row->extra_info)); ?></a>
			</p>
		</div>
<?php } ?>

<?php if (trim($this->row->contact_info)) { ?>
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CONTACT'); ?></h3>
			<p class="entry-contact">
				<?php echo $this->escape(stripslashes($this->row->contact_info)); ?>
			</p>
		</div>
<?php } ?>

<?php if ($this->config->getCfg('byview') == 'YES') {
			$user = JUser::getInstance($this->row->created_by);

			if (is_object($user)) {
				$name = $user->get('name');
			} else {
				$name = JText::_('EVENTS_CAL_LANG_UNKOWN');
			}
?>
		<div class="container">
			<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS'); ?></h3>
			<p class="entry-author">
				<?php echo $this->escape(stripslashes($name)); ?>
			</p>
		</div>
<?php } ?>
<?php } ?>

<?php } else { ?>
		<p class="warning"><?php echo JText::_('EVENTS_CAL_LANG_REP_NOEVENTSELECTED'); ?></p>
<?php } ?>
			</div><!-- / .five columns fourth fifth -->
		<div class="clear"></div>
	</div><!-- / .entry-details -->
</div><!-- / .modal -->