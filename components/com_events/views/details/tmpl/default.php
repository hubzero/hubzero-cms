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

$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<?php if ($this->authorized) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>"><?php echo JText::_('EVENTS_ADD_EVENT'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>

<div class="main section">
	<div class="aside">
			<div class="calendarwrap">
				<?php
				$view = new JView( array('name'=>'browse','layout'=>'calendar') );
				$view->option = $this->option;
				$view->task = $this->task;
				$view->year = $this->year;
				$view->month = $this->month;
				$view->day = $this->day;
				$view->offset = $this->offset;
				$view->shownav = 0;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$view->display();
				?>
			</div><!-- / .calendarwrap -->
		</div><!-- / .aside -->
		<div class="subject">
<?php
if ($this->row) {
	$html  = '<h3>'. stripslashes($this->row->title);
	if ($this->authorized || $this->row->created_by == $juser->get('id')) {
		$html .= '&nbsp;&nbsp;';
		$html .= '<a class="edit" href="'. JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$this->row->id) .'" title="'.JText::_('EVENTS_EDIT').'">'.strtolower(JText::_('EVENTS_EDIT')).'</a>'."\n";
		$html .= '&nbsp;&nbsp;'."\n";
		$html .= '<a class="delete" href="'. JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$this->row->id) .'" title="'.JText::_('EVENTS_DELETE').'">'.strtolower(JText::_('EVENTS_DELETE')).'</a>'."\n";
	}
	$html .= '</h3>'."\n";
	if ($this->row->registerby && $this->row->registerby != '0000-00-00 00:00:00') {
		//$html .= EventsHtml::menu( $option, $this->row, $page, $pages );
		$html .= '<div id="sub-sub-menu">'."\n";
		$html .= '<ul>'."\n";
		$html .= "\t".'<li';
		if ($this->page->alias == '') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->row->id) .'"><span>'.JText::_('EVENTS_OVERVIEW').'</span></a></li>'."\n";
		if ($this->pages) {
			foreach ($this->pages as $p)
			{
				$html .= "\t".'<li';
				if ($this->page->alias == $p->alias) {
					$html .= ' class="active"';
				}
				$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->row->id.'&page='.$p->alias) .'"><span>'.trim(stripslashes($p->title)).'</span></a></li>'."\n";
			}
		}
		$html .= "\t".'<li';
		if ($this->page->alias == 'register') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->row->id.'&page=register') .'"><span>'.JText::_('EVENTS_REGISTER').'</span></a></li>'."\n";
		$html .= '</ul>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '</div>'."\n";
	}

	if ($this->page->alias != '') {
		$html .= (trim($this->page->pagetext)) ? stripslashes($this->page->pagetext) : '<p class="warning">'. JText::_('EVENTS_NO_INFO_AVAILABLE') .'</p>';
	} else {
		$juser =& JUser::getInstance( $this->row->created_by );

		if (is_object($juser)) {
			$name = $juser->get('name');
		} else {
			$name = JText::_('EVENTS_CAL_LANG_UNKOWN');
		}

		$html .= '<table id="event-info" summary="'.JText::_('EVENTS_DETAILS_TABLE_SUMMARY').'">'."\n";
		$html .= ' <tbody>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY').':</th>'."\n";
		$html .= '   <td>'. stripslashes($this->categories[$this->row->catid]) .'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION').':</th>'."\n";
		$html .= '   <td>'. $this->row->content .'</td>'."\n";
		$html .= '  </tr>'."\n";
		$html .= '  <tr>'."\n";
		$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_WHEN').':</th>'."\n";
		$html .= '   <td>'."\n";

		$ts = explode(':', $this->row->start_time);
		if (intval($ts[0]) > 12) {
			$ts[0] = ($ts[0] - 12);
			$this->row->start_time = implode(':',$ts);
			$this->row->start_time .= ' <small>PM</small>';
		} else {
			$this->row->start_time .= (intval($ts[0]) == 12) ? ' <small>'.JText::_('EVENTS_NOON').'</small>' : ' <small>AM</small>';
		}
		$te = explode(':', $this->row->stop_time);
		if (intval($te[0]) > 12) {
			$te[0] = ($te[0] - 12);
			$this->row->stop_time = implode(':',$te);
			$this->row->stop_time .= ' <small>PM</small>';
		} else {
			$this->row->stop_time .= (intval($te[0]) == 12) ? ' <small>'.JText::_('EVENTS_NOON').'</small>' : ' <small>AM</small>';
		}
		//if ($config->getCfg('repeatview') == 'YES') {
			if ($this->row->start_date == $this->row->stop_date) {
				$html .= $this->row->start_date .', '.$this->row->start_time.'&nbsp;-&nbsp;'.$this->row->stop_time.'&nbsp;'.$this->row->time_zone.' <br />';
			} else {
				$html .= JText::_('EVENTS_CAL_LANG_FROM').' '.$this->row->start_date.'&nbsp;-&nbsp;'.$this->row->start_time.'&nbsp;'.$this->row->time_zone.' <br />'.
					JText::_('EVENTS_CAL_LANG_TO').' '.$this->row->stop_date.'&nbsp;-&nbsp;'.$this->row->stop_time.'&nbsp;'.$this->row->time_zone.' <br />';
			}
		/*} else {
			$html .= $this->row->start_date .', '.$this->row->start_time.'&nbsp;-&nbsp;'.$this->row->stop_time.'<br />';
		}*/
		$html .= '   </td>'."\n";
		$html .= '  </tr>'."\n";
		if (trim($this->row->contact_info)) {
			$html .= '  <tr>'."\n";
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_CONTACT').':</th>'."\n";
			$html .= '   <td>'. $this->row->contact_info .'</td>'."\n";
			$html .= '  </tr>'."\n";
		}
		if (trim($this->row->adresse_info)) {
			$html .= '  <tr>'."\n";
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE').':</th>'."\n";
			$html .= '   <td>'. $this->row->adresse_info .'</td>'."\n";
			$html .= '  </tr>'."\n";
		}
		if (trim($this->row->extra_info)) {
			$html .= '  <tr>'."\n";
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_EXTRA').':</th>'."\n";
			$html .= '   <td><a href="'. htmlentities($this->row->extra_info) .'">'. htmlentities($this->row->extra_info) .'</a></td>'."\n";
			$html .= '  </tr>'."\n";
		}
		if ($this->fields) {
			foreach ($this->fields as $field)
			{
				if (end($field) != NULL) {
					if (end($field) == '1') {
						$html .= '  <tr>'."\n";
						$html .= '   <th scope="row">'.$field[1].':</th>'."\n";
						$html .= '   <td>'.JText::_('YES').'</td>'."\n";
						$html .= '  </tr>'."\n";
					} else {
						$html .= '  <tr>'."\n";
						$html .= '   <th scope="row">'.$field[1].':</th>'."\n";
						$html .= '   <td>'.end($field).'</td>'."\n";
						$html .= '  </tr>'."\n";
					}
				}
			}
		}
		if ($this->config->getCfg('byview') == 'YES') {
			$html .= '  <tr>'."\n";
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS').':</th>'."\n";
			$html .= '   <td>' . $name . '</td>'."\n";
			$html .= '  </tr>'."\n";
		}
		if ($this->tags) {
			$html .= '  <tr>'."\n";
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_TAGS').':</th>'."\n";
			$html .= '   <td>' . $this->tags . '</td>'."\n";
			$html .= '  </tr>'."\n";
		}
		$html .= ' </tbody>'."\n";
		$html .= '</table>'."\n";
	}
	echo $html;
} else { ?>
			<p class="warning"><?php echo JText::_('EVENTS_CAL_LANG_REP_NOEVENTSELECTED'); ?></p>
<?php } ?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
